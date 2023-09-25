jQuery(document).on('ready loaded.yametrika', function () {

    /** Ключ статуса удаленного подключения */
    const REMOTE_CONTROL = 'i';

    const SPLITTER = '.';
    const AVAILABLE_FILES = ['form', 'button', 'phone', 'status'];

    const ID = 'i';
    const NAME = 'n';
    const PATH = 'p';
    const CONTENT = 'c';
    const HREF = 'h';
    const TYPE = 'ty';

    const UTILS_KEY = '_u';
    const UTILS_CLOSEST_KEY = 'closest';
    const UTILS_SELECT_KEY = 'select';
    const UTILS_GET_DATA_KEY = 'getData';
    const UTILS_HIDE_PHONES_KEY = 'hidePhones';
    const UTILS_CHECK_STATUS_KEY = 'checkStatus';

    const BETA_URL = 'https://s3.mds.yandex.net/internal-metrika-betas';
    const URL = 'https://yastatic.net/s3/metrika';

    var POLYFILL_STORAGE = {};

    function remoteControl() {
        if (metrikaStorage[REMOTE_CONTROL]) {
            return;
        }

        metrikaStorage[REMOTE_CONTROL] = 1;

        window.addEventListener('message', function (e) {
            var origin = e.origin;
            if (!origin)
                return;

            const isMetrikaOrigin = isAllowedOrigin(origin);
            if (!isMetrikaOrigin)
                return;

            var message = null;
            try {
                message = JSON.parse(event.data);
            } catch (error) {
                message = null;
            }

            if (message && message['action'] === 'appendremote') {
                handleMessage(event, message);
            }
        });
    }

    /**
     * Обработать сообщение от Яндекса
     * 
     * @param Event event Событие
     * @param array message Сообщение
     * 
     * @example
     * - `action` - Type of action to perform on page
     * - `resource` - URL of resource to load
     * - `id` - Request id
     * - `inpageMode` - Functionality type, for example clickmap
     * - `initMessage` - Feature init data
     */
    function handleMessage(event, message) {
        if (message['inline']) {
            const src = getResourceUrl(message);
            const { ['data']: data = '', id = '' } = message;
            setupUtilsAndLoadScript(src, id, data);
        } else if (
            message['resource'] &&
            isAllowedResource(message['resource'])
        ) {
            window['_ym__postMessageEvent'] = event;
            window['_ym__inpageMode'] = message['inpageMode'];
            window['_ym__initMessage'] = message['initMessage'];

            buildRemoteIframe(message['resource']);
        }
    }

    /**
     * @param array Данные сообщения
     * 
     * @returns string
     */
    function getResourceUrl(message) {
        const {
            ['lang']: lang = '',
            ['appVersion']: appVersion = '',
            ['fileId']: fileId = '',
            ['beta']: beta = false,
        } = message;
        const validVersion = arrayJoin(
            SPLITTER,
            pipe(
                windowMap(pipe(firstArg, parseDecimalInt)),
                windowFilter(Boolean),
            )(appVersion.split(SPLITTER)),
        );

        if (
            !includes(fileId, AVAILABLE_FILES) ||
            !includes(lang, ['ru', 'en', 'tr'])
        ) {
            return '';
        }

        const baseUrl = beta ? BETA_URL : URL;
        const version = validVersion ? `/${validVersion}` : '';
        const fileName = `${fileId}_${lang}.js`;

        const result = `${baseUrl}${version}/form-selector/${fileName}`;
        if (!isAllowedResource(result)) {
            return '';
        }

        return result;
    };

    /**
     * Настройка скрипта и доп функционала
     * 
     * @param {object} options Параметпы скрипта
     * @param {string} counterId Номер счетчика
     * @param {string} phones Телефоны
     */
    function setupUtilsAndLoadScript(options, counterId = '', phones = '') {
        utils['getCachedTags'] = getCachedTags;

        utils['form'] = {
            [UTILS_CLOSEST_KEY]: closestForm,
            [UTILS_SELECT_KEY]: selectForms,
            [UTILS_GET_DATA_KEY]: getFormData,
        };

        utils['button'] = {
            [UTILS_CLOSEST_KEY]: closestButton,
            [UTILS_SELECT_KEY]: selectButtons,
            [UTILS_GET_DATA_KEY]: getButtonData,
        };

        utils['phone'] = {
            [UTILS_HIDE_PHONES_KEY]: hidePhones.bind(null, null, phones)
        };

        utils['status'] = {
            [UTILS_CHECK_STATUS_KEY]: metrikaStorage['id'] == parseInt(counterId)
        };

        metrikaStorage[UTILS_KEY] = utils;

        if (options) {
            loadScript(window, options);
        }
    };

    function buildRemoteIframe(src) {
        const createElement = getCreateElementFunction();
        if (!createElement) {
            return;
        }

        var iframeContainer = createElement('div');
        var root = document.documentElement;
        if (!root) {
            return;
        }

        iframeContainer.innerHTML =
            '<iframe name="RemoteIframe" allowtransparency="true" style="position: absolute; left: -999px; top: -999px; width: 1px; height: 1px;"></iframe>';

        var iframeEl = iframeContainer.firstChild;
        // нельзя использовать bind, т.к. здесь iframeEl.contentWindow все еще null
        iframeEl.onload = function () {
            const csp = createElement('meta');
            csp.setAttribute('http-equiv', 'Content-Security-Policy');
            // Тут нет unsafe-inline, поэтому попытка сделать location.href = "javascript:" зафейлится об csp
            csp.setAttribute('content', 'script-src *');
            iframeEl.contentWindow.document.head.appendChild(csp);

            loadScript(iframeEl.contentWindow, { src });
        };

        window['_ym__remoteIframeEl'] = iframeEl;

        root.appendChild(iframeContainer);
        iframeContainer.removeChild(iframeEl);

        var shadowRoot = null;

        if (iframeContainer.attachShadow) {
            shadowRoot = iframeContainer.attachShadow({ mode: 'open' });
        } else if (iframeContainer.createShadowRoot) {
            shadowRoot = iframeContainer.createShadowRoot();
        } else if (iframeContainer.webkitCreateShadowRoot) {
            shadowRoot = iframeContainer.webkitCreateShadowRoot();
        }

        if (shadowRoot) {
            shadowRoot.appendChild(iframeEl);
        } else {
            root.appendChild(iframeEl);
            window['_ym__remoteIframeContainer'] = iframeEl;
        }
    };

    /**
     * 
     * @param {HTMLElement} view
     * @param {object} options 
     * 
     * @returns HTMLScriptElement|undefined
     */
    function loadScript(view, options) {
        const { document: document } = view;
        const newOpt = mergeParams(
            {
                type: 'text/javascript',
                charset: 'utf-8',
                async: true,
            },
            options,
        );
        const createFn = getCreateElementFunction(view);
        if (!createFn) {
            return undefined;
        }

        var scriptTag = createFn('script');
        for (const [key, value] of objectEntries(newOpt)) {
            if (key === 'async' && value) {
                scriptTag.async = true;
            } else {
                scriptTag[key] = value;
            }
        }

        try {
            const getElems = document.getElementsByTagName.bind(document);
            var head = getElems('head')[0];
            // fix for Opera
            if (!head) {
                const html = getElems('html')[0];
                head = createFn('head');
                if (html) {
                    html.appendChild(head);
                }
            }
            head.insertBefore(scriptTag, head.firstChild);
            return scriptTag;
        } catch (e) {
            // empty
        }

        return undefined;
    };

    //------------------------------------
    // Кнопки

    const TAG_DATA = {
        ['A']: HREF,
        ['BUTTON']: ID,
        ['DIV']: ID,
        ['INPUT']: TYPE,
    }

    const BUTTON_SELECTOR = 'button,' +
        ['button', 'submit', 'reset', 'file'].map(function (type) {
            return 'input[type="' + type + '"]'
        }).join(',') + ',a';

    const MAYBE_BUTTON_SELECTOR = 'div';

    /**
     * 
     * @param {HTMLElement} node Поиск кнопки
     * @returns HTMLElement|null
     */
    function closestButton(node) {
        var button = closest(BUTTON_SELECTOR, node);
        if (!button) {
            const maybeButton = closest(MAYBE_BUTTON_SELECTOR, node);
            if (maybeButton) {
                const childMaybe = select(
                    `${BUTTON_SELECTOR},${MAYBE_BUTTON_SELECTOR}`,
                    maybeButton,
                );
                if (!childMaybe.length) {
                    button = maybeButton;
                }
            }
        }

        return button;
    };

    function selectButtons(node) {
        return select(BUTTON_SELECTOR, node);
    }

    /**
     * 
     * @param {HTMLElement} button 
     * @param {HTMLElement} ignored 
     * @returns array
     */
    function getButtonData(button, ignored = {}) {
        const nodeName = getNodeName(button);

        return (
            nodeName &&
            getData(
                button,
                [PATH, TAG_DATA[nodeName], CONTENT].filter(Boolean),
                selectButtons,
                ignored,
            )
        );
    };

    //---------------------------
    // Формы

    const FORM_SELECTOR = 'form';

    /**
     *
     * @param {HTMLElement} node 
     * @returns HTMLElement
     */
    function closestForm(node) {
        return closest(FORM_SELECTOR, node);
    }

    /**
     * 
     * @param {HTMLElement} node 
     * @returns HTMLElement
     */
    function selectForms(node) {
        return select(FORM_SELECTOR, node);
    }

    /**
     * 
     * @param {HTMLElement} form 
     * @param {HTMLElement} ignored 
     * @returns object
     */
    function getFormData(form, ignored = {}) {
        return getData(form, [ID, NAME, PATH], undefined, ignored)
    }

    //------------------------------------
    // Телефоны

    const ANY_PHONE = "*";
    const ReplaceElementLink = 'href';

    /**
     * Скрыть номера
     * 
     * @param {array} counterOpt Параметры счетчика
     * @param {string[]} phones Список номеров
     */
    function hidePhones(counterOpt, phones) {
        alert('Не разработано');
    }

    //---------------------------
    // Контент

    /**
     * 
     * @param {HTMLElement|Element|null} node 
     * @returns 
     */
    function getNodeName(node) {
        if (node) {
            try {
                // Чтобы не звать лишний раз нативный геттер который
                var name = node.nodeName;
                // METR-41427
                if (isString(name)) {
                    return name;
                }
                name = node.tagName;
                if (isString(name)) {
                    return name;
                }
            } catch (e) { }
        }
        return undefined;
    };

    const DEFAULT_SIZE_LIMIT = 100;
    const SIZE_LIMITS = { PATH: 500 }

    const ATTRIBUTES_MAP = [ID, NAME, HREF, TYPE];
    const GETTERS_MAP = {
        PATH: getElementPath,
        CONTENT: getElementContent
    };

    /**
     * 
     * @param {HTMLElement} element 
     * @param {string} ids 
     * @param {Function} selectFn 
     * @param {HTMLElement} ignored 
     * @returns 
     */
    function getData(element, ids, selectFn, ignored) {
        var result = {};
        var value = null;
        if (ids in ATTRIBUTES_MAP) {
            value = getAttribute(element, ATTRIBUTES_MAP[ids]);
        } else if (ids in GETTERS_MAP) {
            if (ids === PATH) {
                value = GETTERS_MAP[ids](element, ignored);
            } else if (ids === CONTENT) {
                value = GETTERS_MAP[ids](element, selectFn);
            } else {
                value = GETTERS_MAP[ids](element);
            }
        }

        if (value) {
            const slicedValue = value.slice(
                0,
                SIZE_LIMITS[ids] || DEFAULT_SIZE_LIMIT,
            );
            result[ids] = HASH[ids]
                ? convertToString(fnv32a(slicedValue))
                : slicedValue;
        }

        return result;
    };

    /**
     * Получить содержимое элемента
     * 
     * @param {HTMLElement|null} el 
     * @param {HTMLElement} ignored 
     * @returns string
     */
    function getElementContent(element, selectFn) {
        var result = trimText(element.textContent);
        if (result && selectFn) {
            const childButtons = selectFn(element);
            if (childButtons.length) {
                for (var index = 0; index < childButtons.length; index++) {
                    if (trimText(array[index].textContent) == result) {
                        result = '';
                        break;
                    }
                }
            }
        }

        if (getNodeName(element) == 'input') {
            result = trimText(getAttribute(element, 'value') || result);
        }
        return result;
    };

    /**
     * 
     * @param {string|null|undefined} text 
     * @param {number} length 
     * @returns string
     */
    function trimText(text, length = 0) {
        const trimRegexp = /^\s+|\s+$/g;
        const nativeTrim = isNativeFunction(String.prototype.trim) && String.prototype.trim;

        if (text) {
            const result = nativeTrim
                ? nativeTrim.call(text)
                : `${text}`.replace(trimRegexp, '');
            if (length && result.length > length) {
                return result.substring(0, length);
            }
            return result;
        }

        return '';
    };

    /**
     * Получить атрибут
     * 
     * @param {HTMLElement} element 
     * @param {string} name 
     * @returns string
     */
    function getAttribute() {
        return element.getAttribute && element.getAttribute(name);
    };

    //------------------------------------
    // Утлиты

    /**
     * Проверка источника сообщения
     * 
     * @param {string} origin Проверяемый источник
     * 
     * returns bool Результат
     */
    function isAllowedOrigin(origin) {
        if (!origin)
            return;

        return /^http:\/\/([\w\-.]+\.)?webvisor\.com\/?$/.test(origin) ||
            /^https:\/\/([\w\-.]+\.)?metri[kc]a\.yandex\.(ru|ua|by|kz|com|com\.tr)\/?$/.test(origin);
    };

    /**
     * Проверка запрашиваемого ресурса
     * 
     * @param string staticUrl Ссылка на ресурс
     * 
     * @returns boolean
     * 
     * @example
     * (\.)(?!\.) - точка, но не две точки подряд
     * нужно для защиты от yastatic.net/s3/metrika/../evil-bucket/script.js
     */
    function isAllowedResource(staticUrl) {
        if (!staticUrl)
            return;

        return /^https:\/\/(yastatic\.net\/s3\/metrika|s3\.mds\.yandex\.net\/internal-metrika-betas|[\w-]+\.dev\.webvisor\.com|[\w-]+\.dev\.metrika\.yandex\.ru)\/(\w|-|\/|(\.)(?!\.))+\.js$/.test(staticUrl);
    }

    /**
     * 
     * @param {string} selector 
     * @param {HTMLElement} node 
     * @returns HTMLElement[]
     */
    function select(selector, node) {
        if (!node || !node.querySelectorAll) {
            return [];
        }

        const result = node.querySelectorAll(selector);
        return result ? toArray(result) : [];
    };

    /**
     * 
     * @param {string} selector Селектор элемента
     * @param {HTMLElement} el Сравниваемый элемент
     * @returns 
     */
    function closest(selector, el) {
        if (!(window && window.Element && window.Element.prototype && window.document) || !el) {
            return null;
        }

        if (
            window.Element.prototype.closest &&
            isNativeFunction('closest', window.Element.prototype.closest) &&
            el.closest
        ) {
            return el.closest(selector);
        }

        const matchesFunction = getMatchesFunction(window);
        if (matchesFunction) {
            var cursor = el;

            while (
                cursor &&
                cursor.nodeType === 1 &&
                !matchesFunction.call(cursor, selector)
            ) {
                cursor = cursor.parentElement || cursor.parentNode;
            }

            if (!cursor || cursor.nodeType !== 1) {
                return null;
            }

            return cursor;
        }
        if (isQuerySelectorSupported(window)) {
            const matches = toArray(
                (window.document || window.ownerDocument).querySelectorAll(
                    selector,
                ),
            );
            var cursor = el;

            while (
                cursor &&
                cursor.nodeType === 1 &&
                cIndexOf(cursor, matches) === -1
            ) {
                cursor = cursor.parentElement || cursor.parentNode;
            }

            if (!cursor || cursor.nodeType !== 1) {
                return null;
            }

            return cursor;
        }

        return null;
    };

    function isNativeFunction(fn) {
        try {
            void new Function(fn.toString());
        } catch (e) {
            return true;
        }
        return false;
    }

    /**
     * Поддержка выбора элемента по запросу
     * 
     * @returns boolean Поддержка
     */
    function isQuerySelectorSupported() {
        return !!(
            isNativeFunction(
                'querySelectorAll',
                window.Element.prototype.querySelectorAll,
            ) && window.document.querySelectorAll
        );
    }

    function getCreateElementFunction(view = false) {
        if (!view)
            view = window;

        return view.document.createElement && isNativeFunction(view.document.createElement)
            && view.document.createElement.bind(view.document);
    }

    /**
     * Определить функцию
     * 
     * @returns function|null Функция
     */
    function getMatchesFunction() {
        if (POLYFILL_STORAGE['matches'])
            return POLYFILL_STORAGE['matches'];

        const elementPrototype = window.Element.prototype;
        if (!elementPrototype) {
            return null;
        }

        const matchFunction = elementPrototype.matches || elementPrototype.webkitMatchesSelector ||
            elementPrototype.mozMatchesSelector || elementPrototype.msMatchesSelector || elementPrototype.oMatchesSelector;
        if (isNativeFunction(matchFunction)) {
            POLYFILL_STORAGE['matches'] = matchFunction;
            return matchFunction;
        }

        return null;
    }

    /**
     * Преобразовать в массив
     * 
     * @param {any} smth Преобраземая переменная
     * @returns array Массив
     */
    function toArray(smth) {
        if (!smth) {
            return [];
        }

        if (isArray(smth)) {
            return smth;
        }

        if (arrayFrom) {
            return arrayFrom(smth);
        }

        if (typeof smth.length === 'number' && smth.length >= 0) {
            return arrayFromPoly(smth);
        }

        return [];
    };

    /**
     * Проверка типа на массив
     * 
     * @param {any} obj Проверяемая переменная
     * @returns boolean Результат
     */
    function isArray(obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    }

    /**
     * Преобразование в массив
     * 
     * @param {any} smth
     * 
     * @returns array
     */
    function arrayFrom(smth) {
        if (isNativeFunction(Array.from))
            return Array.from(smth);

        return null;
    }

    /**
     * Преобразование в массив вручную
     * 
     * @param {any} smth
     * 
     * @returns array
     */
    function arrayFromPoly(smth) {
        const len = smth.length;
        const result = [];
        for (var i = 0; i < len; i += 1) {
            result.push(smth[i]);
        }

        return result;
    }

    /**
     * Найти позицию в списке
     * 
     * @param list Список
     * @param search Искомое
     * 
     * @returns number Позиция
     */
    function cIndexOf() {
        if (POLYFILL_STORAGE['cIndexOf'])
            return POLYFILL_STORAGE['cIndexOf'];

        var checkIndexFn = false;
        try {
            // Тест для IE 6 или старого safari (никто точно не помнит)
            // eslint-disable-next-line
            checkIndexFn = [].indexOf && [undefined].indexOf(undefined) === 0;
        } catch (error) {
            // empty
        }

        const isAccesebleArray = window.Array && window.Array.prototype;
        const nativeFn =
            isAccesebleArray && isNativeFunction(window.Array.prototype.indexOf)
            && window.Array.prototype.indexOf;

        var indexFn;
        if (checkIndexFn && nativeFn) {
            indexFn = (val, array) => {
                return nativeFn.call(array, val);
            };
        } else {
            indexFn = indexOfPoly;
        }

        POLYFILL_STORAGE['cIndexOf'] = indexFn;
        return indexFn;
    };

    function assignPoly() {
        var args = Array.prototype.slice.call(arguments);

        const dst = args.shift();
        while (args.length) {
            const obj = args.shift();
            for (const key in obj) {
                if (has(obj, key)) {
                    dst[key] = obj[key];
                }
            }
            /**
             * по всей видимости в каких-то браузерах проп toString не попадал в for..in
             * но попадал в obj.hasOwnProperty
             * поэтому приходится вручную проверять
             */
            if (obj.toString) {
                dst['toString'] = obj['toString'];
            }
        }
        return dst;
    };

    const mergeParams = Object.assign || assignPoly;

    //-------------------------

    function objectEntries(obj) {
        if (Object.entries) {
            if (!obj) {
                return [];
            }
            return Object.entries(obj);
        }
        return objectEntriesPoly(obj);
    }

    function objectEntriesPoly(obj) {
        if (!obj) {
            return [];
        }
        return arrayReducePoly(
            function (rawResul, key) {
                const result = rawResult;
                result.push([key, obj[key]]);
                return result;
            },
            [],
            arrayKeysPoly(obj),
        );
    };

    function arrayKeysPoly(obj) {
        const out = [];
        var key;
        // eslint-disable-next-line no-restricted-syntax
        for (key in obj) {
            if (has(obj, key)) {
                out.push(key);
            }
        }

        return out;
    };

    function arrayReducePoly(fn, first, array) {
        var i = 0;
        const len = array.length;
        var out = first;
        while (i < len) {
            out = fn(out, array[i], i);
            i += 1;
        }
        return out;
    };

    //------------------------------------
    // Запуск

    remoteControl();
});