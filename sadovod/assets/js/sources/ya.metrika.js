//--------------------------------------
// Константы

/** Максимальная длинна ссылки */
const MAX_URL_LEN = 1024;

/** Максимальная длинна заголовка */
const MAX_TITLE_LEN = 512;

/** Максимальная длинна заголовка */
const MAX_LEN_PATH = 128;

/** Отправлять клики не чаще */
const TIMEOUT_CLICK = 50;

/** Близкие клики отправляем не чаще */
const TIMEOUT_SAME_CLICKS = 1000;

/** Дистаниця юлизкого клика */
const DELTA_SAME_CLICKS = 2;

/** Параметр проверки активности метрики */
const CHECK_URL_PARAM = '_ym_status-check';

/** Ключи хранилища */
const BATTERY_INFO = 'bt';
const HID_NAME = 'hitId';

/** Типы соединений */
const NET_MAP = [
    'other',
    'none',
    'unknown',
    'wifi',
    'ethernet',
    'bluetooth',
    'cellular',
    'wimax',
    'mixed',
];

/** Список тэгов */
const TAGS_MAP = [
    'A', // Распространенные теги
    'B',
    'BIG',
    'BODY',
    'BUTTON',
    'DD',
    'DIV',
    'DL',
    'DT',
    'EM',
    'FIELDSET',
    'FORM',
    'H1',
    'H2',
    'H3',
    'H4',
    'H5',
    'H6',
    'HR',
    'I',
    'IMG',
    'INPUT',
    'LI',
    'OL',
    'P',
    'PRE',
    'SELECT',
    'SMALL',
    'SPAN',
    'STRONG',
    'SUB',
    'SUP',
    'TABLE',
    'TBODY',
    'TD',
    'TEXTAREA',
    'TFOOT',
    'TH',
    'THEAD',
    'TR',
    'U',
    'UL',
    // Менее распространенные теги
    'ABBR',
    'AREA',
    'BLOCKQUOTE',
    'CAPTION',
    'CENTER',
    'CITE',
    'CODE',
    'CANVAS',
    'DFN',
    'EMBED',
    'FONT',
    'INS',
    'KBD',
    'LEGEND',
    'LABEL',
    'MAP',
    'OBJECT',
    'Q',
    'S',
    'SAMP',
    'STRIKE',
    'TT',
    // html 2
    'ARTICLE',
    'AUDIO',
    'ASIDE',
    'FOOTER',
    'HEADER',
    'MENU',
    'METER',
    'NAV',
    'PROGRESS',
    'SECTION',
    'TIME',
    'VIDEO',
    'NOINDEX',
    'NOBR',
    'MAIN',
    // SVG
    'svg',
    'circle',
    'clippath',
    'ellipse',
    'defs',
    'foreignobject',
    'g',
    'glyph',
    'glyphref',
    'image',
    'line',
    'lineargradient',
    'marker',
    'mask',
    'path',
    'pattern',
    'polygon',
    'polyline',
    'radialgradient',
    'rect',
    'set',
    'text',
    'textpath',
    'title',
];

/** Список параметров запроса */
var metrikaStorage = {
    'id': 0,
    'loaded': false,
    'page-url': prepareUrl(getPageUrl()),
    'page-ref': prepareUrl(getReferrer())
};

//--------------------------------------
// API

/**
 * Отправка хита.
 *
 * @param {string} counterId Номер счётчика.
 * @param {object} hitParams  Параметры страницы.
 * @param {object} userVars Параметры визитов.
 *
 * @example
 * sendHit('123456');
 *
 * sendHit('123456', {
 *     referer: document.referer,
 *     title: document.title,
 *     url: window.location.href
 * }, {
 *     myParam: 'value'
 * });
 */
function sendHit(counterId, hitParams, params) {
    const referrer = hitParams && hitParams.referrer !== undefined ?
        hitParams.referrer :
        getReferrer();

    const title = hitParams && hitParams.title !== undefined ?
        hitParams.title :
        getTitle();

    const url = hitParams && hitParams.url !== undefined ?
        hitParams.url :
        getPageUrl();

    hitExt(
        'watch',
        {
            browserInfo: { pv: true, ar: true },
            counterId,
            pageParams: {
                referrer,
                title,
                url
            },
            params
        }
    );
}

/**
 * Отправка хита.
 *
 * @param {string} counterId Номер счётчика.
 * @param {MouseEvent} event Параметры клика.
 * @param {object} userVars Параметры визитов.
 *
 * @example
 * sendHit('123456');
 *
 * sendHit('123456', {
 *     referer: document.referer,
 *     title: document.title,
 *     url: window.location.href
 * }, {
 *     myParam: 'value'
 * });
 */
function sendClick(counterId, event, params) {
    const MAX_VALUE = 65535;
    const clickPosition = getElementPosition(event);
    const elementPosition = getElementXY(event.target);
    const elementSize = getElementSize(event.target);

    const click = [];
    const [startTime, curTick] = getPerformanceInfo();

    addParam(click, 'rn', getRandom());

    addParam(click, 'x',
        Math.floor(
            ((clickPosition.x - elementPosition.left) *
                MAX_VALUE) /
            (elementSize.width || 1),
        )
    );
    addParam(click, 'y',
        Math.floor(
            ((clickPosition.y - elementPosition.top) *
                MAX_VALUE) /
            (elementSize.height || 1),
        )
    );

    addParam(click, 't', Math.floor(curTick() / 100));
    addParam(click, 'p', getElementPath(event.target));

    addParam(click, 'X', clickPosition.x)
    addParam(click, 'Y', clickPosition.y)

    var queryParams = {
        'pointer-click': click.join(':'),
        'force-urlencoded': 1
    }

    hitExt(
        'clmap',
        {
            browserInfo: {},
            counterId,
            pageParams: {},
            params
        },
        queryParams
    );
}

/**
 * Достижение цели.
 *
 * @param {string} counterId Номер счётчика.
 * @param {string} name Название цели.
 * @param {object} userVars Параметры визитов.
 *
 * @example
 * reachGoal('123456', 'goalName');
*/
function reachGoal(counterId, name, params) {
    var referrer;
    var url;

    if (name) {
        referrer = getPageUrl();
        url = `goal://${getHost()}/${name}`;
    } else {
        referrer = getReferrer();
        url = getPageUrl();
    }

    hitExt(
        'watch',
        {
            browserInfo: { ar: true },
            counterId,
            pageParams: { referrer, url },
            params,
        }
    );
}

/**
 * Загрузка файла.
 *
 * @param {string} counterId Номер счётчика.
 * @param {string} file Ссылка на файл.
 * @param {string} title Заголовок страницы.
 *
 * @example
 * sendFile('123456', 'https://mysite.com/file.zip');
 */
function sendFile(counterId, file, title) {
    if (file) {
        hitExt(
            'watch',
            {
                browserInfo: {
                    ar: true,
                    dl: true,
                    ln: true
                },
                counterId,
                pageParams: {
                    referrer: getReferrer(),
                    title,
                    url: file
                }
            }
        );
    }
}

/**
 * Параметры визитов.
 *
 * @param {string} counterId Номер счётчика.
 * @param {object} data Параметры визитов.
 *
 * @example
 * sendParams('123456', { myParam: 'value' });
 */
function sendParams(counterId, data) {
    if (data) {
        hitExt(
            'watch',
            {
                browserInfo: { ar: true, pa: true },
                counterId,
                pageParams: {},
                params: data
            }
        );
    }
}

/**
 * Параметры посетителей сайта.
 *
 * @param {string} counterId Номер счётчика.
 * @param {object} data Параметры.
 *
 * @example
 * userParams('123456', { myParam: 'value', UserID: 123 });
 */
function userParams(counterId, data) {
    if (data) {
        hitExt(
            'watch',
            {
                browserInfo: { ar: true, pa: true },
                counterId,
                pageParams: {},
                params: {
                    __ymu: data,
                }
            }
        );
    }
}

/**
 * Не отказ.
 *
 * @param {string} counterId ID счетчика.
 * @param {object} params Параметры.
 * 
 * @example
 * notBounce('123456');
 */
function notBounce(counterId, params) {
    hitExt(
        'watch',
        {
            browserInfo: { ar: true, nb: true },
            counterId,
            pageParams: {},
            params
        }
    );
}

/**
 * Обработчик отправки данных хита
 * 
 * @param {string} service Сервис 
 * @param {object} hitExtParams Параметры хита
 * @param {object|undefined} queryParams Дополнительные параметры запроса
 * @param {string|undefined} postData Данные пост запроса
 */
function hitExt(service, hitExtParams, queryParams = undefined, postData = undefined) {
    const {
        browserInfo,
        counterId,
        pageParams,
        params,
    } = hitExtParams;

    const data = {
        'browser-info': getBrowserInfo(counterId, browserInfo, pageParams.title),
        rn: getRandom(),
        ut: pageParams.ut
    };

    if (pageParams.url) {
        data['page-url'] = prepareUrl(pageParams.url);
    }

    if (pageParams.referrer) {
        data['page-ref'] = prepareUrl(pageParams.referrer);
    }

    if (params) {
        data['site-info'] = JSON.stringify(params);
    }

    data['wmode'] = 0;
    if (!metrikaStorage['hittoken']) {
        data['wmode'] = 7;
    }

    //TODO
    //data['t'] = 'gdpr(14)ti(2)';

    needAuth = false;
    if (needAuth && metrikaStorage['hittoken']) {
        data['hittoken'] = metrikaStorage['hittoken'];
    }

    if (queryParams) {
        for (var param in queryParams) {
            data[param] = queryParams[param];
        }
    }

    sendData(service, counterId, data, postData);
}

//--------------------------------------
// Отправка данных

/**
 * Отправить данные в метрику
 * 
 * @param {string} service Сервис
 * @param {string} counterId ID счетчика
 * @param {object} queryParams Параметры
 * @param {string} queryParams POST параметры
 */
function sendData(service, counterId, queryParams, postData) {
    var query = queryStringify(queryParams);
    var url = 'https://mc.yandex.ru/' + service + '/' + counterId + '?' + query;
    const hasBeacon = typeof navigator !== 'undefined' && navigator.sendBeacon;

    if (!hasBeacon || !navigator.sendBeacon(url, ' ')) {
        if (typeof fetch !== 'undefined') {
            var responce = fetch(url, {
                headers: 'application/x-www-form-urlencoded',
                method: postData ? 'POST' : 'GET', body: postData, credentials: 'include'
            }).catch(function () {/** unhandled rejection off */ });

            if (!metrikaStorage['hittoken']) {
                var yadata = JSON.parse(responce);
                metrikaStorage['hittoken'] = yadata.settings.hittoken;
            }
        } else if (typeof Image !== 'undefined') {
            url = url.replace(/&wmode=\d/g, '') + (query ? '&' : '?') + postData;
            new Image().src = url;
        }
    }
}

/**
 * Подоготовить запрос
 * 
 * @param {object} params Параметры
 * @returns string `Запрос`
 */
function queryStringify(params) {
    return Object.keys(params)
        .filter(key => params[key] || params[key] === 0)
        .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(params[key]))
        .join('&');
}

/**
 * Укоротить ссылку перед отправкой
 * 
 * @param {string} url Ссылка
 * @returns string `Ссылка`
 */
function prepareUrl(url) {
    return truncate(url, MAX_URL_LEN);
}

//--------------------------------------
// Вебвизор

/**
 * [В разработке] Отправка данных вебвизора.
 *
 * @param {string} counterId Номер счётчика.
 * @param {object} hitParams  Параметры страницы.
 * @param {object} userVars Параметры визитов.
 *
 * @example
 * sendHit('123456');
 *
 * sendHit('123456', {
 *     referer: document.referer,
 *     title: document.title,
 *     url: window.location.href
 * }, {
 *     myParam: 'value'
 * });
 */
function sendWebVisor(counterId) {
    var browserInfo = { we: true };

    if (!metrikaStorage['hitID'])
        metrikaStorage['hitID'] = getRandom();

    var params = {
        'wv-part': 13,
        'wv-hit': metrikaStorage['hitID'],
        'wv-type': 3,
    };

    var data = '';
    if (validate) {
        browserInfo['bt'] = true;

        params['wv-type'] = 0;
        params['wv-check'] = fletcher(data);

        data = 'wv-data=' + encodeBase64(data, true);
    }

    const url = getPageUrl();
    hitExt(
        'webvisor',
        {
            browserInfo: { we: true },
            counterId,
            pageParams: {
                url
            },
            params: {}
        },
        params,
        data
    );
}

/**
 * Вычисляет чексумму данных по алгоритму Флетчера.
 *
 * @param {Array|String} data
 *
 * @returns {Number}
 */
function fletcher(data) {
    let { length } = data;
    let i = 0;
    let sum1 = 0xff;
    let sum2 = 0xff;
    let tlen;
    let ch;
    let ch2;
    while (length) {
        tlen = length > 21 ? 21 : length;
        length -= tlen;

        do {
            ch = typeof data === 'string' ? data.charCodeAt(i) : data[i];
            i += 1;
            if (ch > 255) {
                ch2 = ch >> 8;
                ch &= 0xff;
                ch ^= ch2;
            }
            sum1 += ch;
            sum2 += sum1;
        } while ((tlen -= 1));
        sum1 = (sum1 & 0xff) + (sum1 >> 8);
        sum2 = (sum2 & 0xff) + (sum2 >> 8);
    }
    const result =
        (((sum1 & 0xff) + (sum1 >> 8)) << 8) | ((sum2 & 0xff) + (sum2 >> 8));
    return result === 0xffff ? 0 : result;
}

//--------------------------------------
// Base64

const base64abc =
    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
const safeBase64replacement = {
    // safe decode
    '*': '+',
    '-': '/',
    _: '=',
    // safe encode
    '+': '*',
    '/': '-',
    '=': '_',
};

/**
 * 
 * @param {number[]} data 
 * @param {boolean} safe 
 * @returns 
 */
function encodeBase64(data, safe = false) {
    const { length: len } = data;
    const lPos = len - (len % 3);
    const result = [];

    for (let i = 0; i < lPos; i += 3) {
        const t = (data[i] << 16) + (data[i + 1] << 8) + data[i + 2];
        result.push(
            base64abc[(t >> 18) & 0x3f],
            base64abc[(t >> 12) & 0x3f],
            base64abc[(t >> 6) & 0x3f],
            base64abc[t & 0x3f],
        );
    }
    let t;
    switch (len - lPos) {
        case 1:
            t = data[lPos] << 4;
            result.push(
                base64abc[(t >> 6) & 0x3f],
                base64abc[t & 0x3f],
                base64abc[64],
                base64abc[64],
            );
            break;
        case 2:
            t = (data[lPos] << 10) + (data[lPos + 1] << 2);
            result.push(
                base64abc[(t >> 12) & 0x3f],
                base64abc[(t >> 6) & 0x3f],
                base64abc[t & 0x3f],
                base64abc[64],
            );
            break;
        default:
    }

    const str = result.join('');

    return safe ? replaceBase64(str, true) : str;
};

/**
 * 
 * @param {string} str 
 * @param {boolean} safe 
 * @returns string
 */
function replaceBase64(str, safe = false) {
    if (!str) {
        return '';
    }

    // Заменить обычные на безопасные символы или наоборот (зависит от флага)
    // encoder - в полученной base64 заменяет символы на безопасные
    // decoder - перед декодингом заменяет все безопасные на обычные
    return str.replace(safe ? /[+/=]/g : /[-*_]/g, (c) => {
        return safeBase64replacement[c] || c;
    });
};

//--------------------------------------
// Информация о пользователе

/**
 * Добавить параметр
 * 
 * @param {string[]} result Результирующий массив
 * @param {string} name Параметр
 * @param {string} value Значение
 */
function addParam(result, name, value) {
    if (value || value === 0) {
        result.push(name + ':' + (value === true ? '1' : value));
    }
}

/**
 * Собрать информацию о браузере
 * 
 * @param {number} counterId Номер счетчика
 * @param {string[]} params Параметры
 * @param {string} title Заголовок
 * @returns string
 */
function getBrowserInfo(counterId, params, title) {
    const result = [];

    if (params) {
        Object.keys(params).forEach(function (key) { addParam(result, key, params[key]) });
    }

    // we и bb ???
    // t - Отслеживание хэша?

    // Это отправляется отправляется только первый раз
    if (metrikaStorage['loaded'] !== true) {
        addParam(result, 'cn', '1'); //Номер счетчика

        addParam(result, 'en', getDocumentEncoding());
        addParam(result, 'la', getDocumentLanguage());

        addParam(result, 'hid', getHid());
        addParam(result, 'fu', isFalseURL());
        addParam(result, 'rn', getRandom());
        addParam(result, 'c', cookieEnabled());
        addParam(result, 's', getScreenSize());
        addParam(result, 'dp', getDesktopFlag());
        addParam(result, 'nt', getNetType());

        addParam(result, 'ns', getNavigationStart());
        addParam(result, 'fp', getFirstPaintTime());

        addParam(result, 't', truncate(title, MAX_TITLE_LEN));

        //TODO
        addParam(result, 'rqn', '');
        addParam(result, 'sk', '');
        addParam(result, 'wv', '2'); // Походу версия веб визора
        addParam(result, 'ds', '0,105,185,0,7,0,,624,1,,,,1006');
        addParam(result, 'co', '0');
        addParam(result, 'cpf', '1');
        addParam(result, 'rqnl', '1');
        addParam(result, 'fip', '8c9e1ab18efb582fa6e2ef1362b67fad-0ed8ce9e1e39'); // Что то очень длиннон
        addParam(result, 'adb', '1'); // Походу наличие адблока

        metrikaStorage['id'] = counterId;
        metrikaStorage['loaded'] = true;
    }

    // Это отправляется всегда
    addParam(result, 'v', '1030'); // Версия
    addParam(result, 'vf', '10ym9geic8i73flogxj2lsv'); // Хешированый список фич https://github.com/yandex/metrica-tag/blob/c1db159efe1b47031eb70681133923e5f3aa2290/src/api/common/browserInfo.ts#L16

    addParam(result, 'w', getClientSize()); //++
    addParam(result, 'u', getClientID()); //++
    addParam(result, 'i', getClientTime()); //++
    addParam(result, 'z', getClientTimeZone()); //++

    const time = getSeconds();
    addParam(result, 'et', time); //++
    addParam(result, 'st', time); //++


    return result.join(':');
}

/**
 * Получить хост
 * 
 * @returns string `Хост`
 */
function getHost() {
    return window && window.location ? window.location.hostname : '';
}

/**
 * Получить url страницы
 * 
 * @returns string `Url страницы`
 */
function getPageUrl() {
    return window && window.location ? window.location.href : '';
}

/**
 * Получить источник
 * 
 * @returns string `Url источника`
 */
function getReferrer() {
    return document ? document.referrer : '';
}

/**
 * Получить заголовок
 * 
 * @returns string `Заголовок`
 */
function getTitle() {
    return document ? document.title : '';
}

/**
 * Проверка на куки
 * 
 * @returns boolean `Статус кук`
 */
function cookieEnabled() {
    return navigator ? navigator.cookieEnabled : false;
}

/**
 * Получить размер экрана
 * 
 * @returns string `Размер экрана`
 */
function getScreenSize() {
    return screen ? [
        screen.width,
        screen.height,
        screen.colorDepth
    ].join('x') : '';
}

/**
 * Получить размер окна
 * 
 * @returns string `Размер окна`
 */
function getClientSize() {
    return window ? [
        window.innerWidth,
        window.innerHeight
    ].join('x') : '';
}

/**
 * Получить время
 * 
 * @returns string `Время`
 */
function getClientTime() {
    const date = new Date();
    const dateInfo = [
        date.getFullYear(),
        date.getMonth() + 1,
        date.getDate(),
        date.getHours(),
        date.getMinutes(),
        date.getSeconds(),
    ];

    return dateInfo.map(function (el) {
        return el.toString().padStart(2, '0');
    }).join('');
}

/**
 * Получить часовой пояс
 * 
 * @returns number `Часовой пояс в минутах`
 */
function getClientTimeZone() {
    return -(new Date()).getTimezoneOffset();
}

/**
 * Получить ClientID
 * 
 * @returns string `ClientID`
 */
function getClientID() {
    var clientID = getCookie('_ym_uid');

    if (!clientID) {
        clientID = "" + Date.now() + Math.floor(Math.floor(Math.random() * 1000000));
        setCookie('_ym_uid', clientID);
    }

    return clientID;
}

/**
 * Получение информации мониторинга производительности
 * 
 * @returns int Время начала загрузки и текущее время или false при остуствиее мониторинга
 */
function getPerformanceInfo() {
    const performance = window.performance || window.webkitPerformance;

    if (performance) {
        const ns = performance.timing.navigationStart;
        var now = performance.now;
        if (now) {
            now = now.bind(performance);
        }

        return [ns, now];
    }

    return false;
}

/**
 * Получение время начала загрузки
 * 
 * @returns int Время начала загрузки или false при остуствиее мониторинга
 */
function getNavigationStart() {
    const ns = getPerformanceInfo();
    if (ns) {
        return ns;
    }

    return false;
}

/**
 * Получить время первой отрисовки
 * 
 * @returns number|undefined Время отрисовки
 */
function getFirstPaintTime() {
    const performance = window.performance || window.webkitPerformance;
    const [ns] = getPerformanceInfo();

    if ('getEntriesByType' in performance) {
        const data = performance.getEntriesByType('paint');
        if (data.length && data[0].name == 'first-contentful-paint') {
            return data[0].startTime;
        }

        return undefined;
    }

    if (chrome && 'loadTimes' in chrome) {
        const time = chrome.loadTimes();
        const fp = time.firstPaintTime;
        if (ns && fp) {
            return fp * 1000 - ns;
        }
    }

    const ms = performance.timing.msFirstPaint;
    if (ms) {
        return ms - ns;
    }

    return undefined;
};

/**
 *  Время с начала работы. Походу нужно для вебвизора
 * 
 * @returns int Время
 */
function getFromStart() {
    var out;

    const [ns, now] = getPerformanceInfo();
    if (ns && now) {
        out = now();
    } else {
        out = getMs(timeState) - timeState.initTime;
    }

    return Math.round(out);
}

function getHid() {
    let val = metrikaStorage[HID_NAME];
    if (!val) {
        val = getRandom();
        metrikaStorage[HID_NAME] = val;
    }

    return val;
}

/**
 * Проверка на изменение url
 * 
 * @returns boolean Статус изменения
 */
function isFalseURL() {
    const replaceRegex = /\/$/;

    if (!metrikaStorage) {
        return null;
    }
    const trueRef = getReferrer().replace(replaceRegex, '');
    const senderRef = (metrikaStorage['page-ref'] || '').replace(replaceRegex, '');

    const trueUrl = getPageUrl();
    const senderUrl = metrikaStorage['page-url'];

    const isFalseUrlBool = trueUrl.href !== senderUrl;
    const isFalseRefBool = trueRef !== senderRef;

    let result = 0;
    if (isFalseUrlBool && isFalseRefBool) {
        result = 3;
    } else if (isFalseRefBool) {
        result = 1;
    } else if (isFalseUrlBool) {
        result = 2;
    }

    return result;
};

/**
 * Получить кодировку документа
 * 
 * @returns string Кодировка
 */
function getDocumentEncoding() {
    return (document.characterSet || document.charset || '').toLowerCase();
}

/**
 * Получить язык документа
 * 
 * @returns string Язык
 */
function getDocumentLanguage() {
    return navigator.language || navigator.userLanguage ||
        navigator.browserLanguage || navigator.systemLanguage || '';
}

/**
 * Проверка флага ПК
 * 
 * @returns boolean Флаг
 */
function getDesktopFlag() {
    var globalConfig = metrikaStorage[BATTERY_INFO];
    var batteryInfo = { v: false, p: undefined };
    if (!globalConfig) {
        try {
            batteryInfo.p = navigator.getBattery && navigator.getBattery.call(window.navigator);
        } catch (e) { }

        metrikaStorage[BATTERY_INFO] = batteryInfo;
        if (batteryInfo.p && batteryInfo.p.then) {
            batteryInfo.p.then(function (battery) {
                batteryInfo.v = battery.charging && battery.chargingTime === 0;
            });
        }
    }

    return batteryInfo.v;
}

/**
 * Определение типа соединения
 * 
 * @returns int|null Тип соединения
 */
function getNetType() {
    if (!('connection' in navigator)) {
        return null;
    }

    const connectionType = navigator.connection.type;
    const index = NET_MAP.indexOf(connectionType);
    return index === -1 ? connectionType : index;
}

//--------------------------------------
// Элементы

/**
 * Получить путь к html элементу
 * 
 * @param {HTMLElement|null} el 
 * @param {HTMLElement} ignored 
 * @returns string
 */
function getElementPath(el, ignored) {
    var path = '';
    var element = el;
    const cacheTags = getCachedTags();
    var nodeName = element.nodeName || element.tagName || '*';

    while (
        element &&
        element.parentNode &&
        ['BODY', 'HTML'].indexOf(nodeName) != 0
    ) {
        path += cacheTags[nodeName] || '*';
        path += getElementNeighborPosition(element, ignored) || '';
        element = element.parentElement;
        nodeName = element.nodeName || element.tagName || '*';
    }

    return truncate(path, MAX_LEN_PATH);
};

function getCachedTags() {
    let charCode = ';'.charCodeAt(0);
    const cacheTags = {};

    for (let i = 0; i < TAGS_MAP.length; i += 1) {
        cacheTags[TAGS_MAP[i]] = String.fromCharCode(charCode);
        charCode += 1;
    }

    return cacheTags;
};

/**
 * Получить позицию элемента
 * 
 * @param {MouseEvent} event 
 */
function getElementPosition(event) {
    const body = getBody();
    const scroll = getDocumentScroll();

    return {
        x:
            event.pageX ||
            event.clientX + scroll.x - (body.clientLeft || 0) ||
            0,
        y:
            event.pageY ||
            event.clientY + scroll.y - (body.clientTop || 0) ||
            0,
    };
};

/**
 * Определить координаты обьекта
 * 
 * @param {HTMLElement} element
 * 
 * @returns object X,Y координаты
 */
function getElementXY(element) {
    const box = element.getBoundingClientRect();
    if (box) {
        const documentScroll = getDocumentScroll();
        return {
            left: Math.round(box.left + documentScroll.x),
            top: Math.round(box.top + documentScroll.y),
        };
    }

    let left = 0;
    let top = 0;
    while (element) {
        left += element.offsetLeft;
        top += element.offsetTop;
        element = element.offsetParent;
    }

    return {
        left,
        top,
    };
}

/**
 * Получить размер элемента
 * 
 * @param {HTMLElement} element 
 * 
 * @returns object
 */
function getElementSize(element) {
    const body = getBody();
    if (element === body || element === document.documentElement) {
        return getDocumentSize();
    }

    const rect = element.getBoundingClientRect();
    return rect ? rect : { width: element.offsetWidth, height: element.offsetHeight };
};

/**
 * Поиск номера соседа
 * 
 * @param {Node} element Элемент для которого найдо найти соседей
 * @param {HTMLElement} ignored
 * @returns number
 */
function getElementNeighborPosition(element, ignored) {
    const parent = getElementParent(element);

    if (parent) {
        const children = parent.childNodes;
        const elementNodeName = element && element.nodeName;
        let n = 0;

        for (let i = 0; i < children.length; i += 1) {
            const childNodeName = children[i] && children[i].nodeName;
            if (elementNodeName === childNodeName) {
                if (element === children[i]) {
                    return n;
                }
                if (!ignored || children[i] !== ignored) {
                    n += 1;
                }
            }
        }
    }
    return 0;
};

/**
 * Поиск родительского нода для элемента
 * 
 * @param {Node} element Жлемент для которого надо найти родителя
 * @returns Node Родитель
 */
function getElementParent(element) {
    if (!element || element === document.documentElement) return null;

    if (element === getBody()) return document.documentElement;

    let parent = null;
    try {
        parent = element.parentNode;
    } catch (e) { }

    return parent;
};

/**
 * Получить позицию мыши
 * 
 * @param {MouseEvent} event 
 * 
 * @returns object
 */
function getDocumentScroll(element) {
    const body = getBody();

    return {
        x:
            window.pageXOffset ||
            (document.documentElement && document.documentElement.scrollLeft) ||
            (body && body.scrollLeft) ||
            0,
        y:
            window.pageYOffset ||
            (document.documentElement && document.documentElement.scrollTop) ||
            (body && body.scrollTop) ||
            0,
    };
};

/**
 * Получить размер документа
 * 
 * @returns object
 */
function getDocumentSize() {
    const body = getBody();

    const [vWidth, vHeight] = getViewportSize();
    return {
        width: Math.max(body.scrollWidth, vWidth),
        height: Math.max(body.scrollHeight, vHeight),
    };
};

/**
 * Получить размер окна
 * 
 * @returns number[]
 */
function getViewportSize() {
    const visualViewport = getVisualViewportSize();
    if (visualViewport) {
        const [width, height, scale] = visualViewport;
        return [
            Math.round(width * scale),
            Math.round(height * scale)
        ];
    }

    const body = getBody();
    return [
        body.clientWidth || window.innerWidth,
        body.clientHeight || window.innerHeight,
    ];
};

/**
 * Получить размер окна просмотра
 * 
 * @returns number[]|null
 */
function getVisualViewportSize() {
    const width = window.visualViewport.width;
    const height = window.visualViewport.height;
    const scale = window.visualViewport.scale;

    if (width && height) {
        return [Math.floor(width), Math.floor(height), scale];
    }

    return null;
};

/**
 * Получить тело страницы
 * 
 * @returns Node Тело
 */
function getBody() {
    return document.body || document.getElementsByTagName('body')[0]
}


//--------------------------------------
// Куки

/**
 * Создать куку
 * 
 * @param {string} name Название куки
 * @param {string} value Значение
 * @param {number} days Период хранения 
 */
function setCookie(name, value) {
    var date = new Date();
    date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));

    document.cookie = name + "=" + value + ";" +
        " expires=" + date.toGMTString() + ";" +
        " path=/;" +
        " domain=" + window.location.hostname + ";" +
        " samesite=Strict;";
}

/**
 * Прочитать куку
 * 
 * @param {string} name Название куки
 * 
 * @returns string `Кука`
 */
function getCookie(name) {
    if (document.cookie.length > 0) {
        start = document.cookie.indexOf(name + "=");
        if (start != -1) {
            start = start + name.length + 1;
            end = document.cookie.indexOf(";", start);
            if (end == -1) {
                end = document.cookie.length;
            }
            return unescape(document.cookie.substring(start, end));
        }
    }
    return "";
}

//--------------------------------------
// Утлиты

/**
 * Получить случайное число
 * 
 * @returns number `Cлучайное число`
 */
function getRandom() {
    return Math.floor(Math.random() * (1 << 31 - 1));
}

/**
 * Получить время в секундах 
 * 
 * @returns number `Секунды`
 */
function getSeconds() {
    return Math.round(Date.now() / 1000);
}

/**
 * Укоротить строку
 * 
 * @param {string} str Строка
 * @param {number} len Длина
 * 
 * @returns string `Строка`
 */
function truncate(str, len) {
    return (str || '').slice(0, len);
}

//--------------------------------------
// Сервисы

function responceTestRequest(userRequest = false) {
    var params = window.location.search.substr(1).split("&")
        .reduce(function (acc, param) {
            const [key, value] = param.split("=");
            return { ...acc, [key]: value };
        }, {});

    if (params[CHECK_URL_PARAM]) {
        if (parseInt(params[CHECK_URL_PARAM]) == metrikaStorage['id']) {
            alert('Работает');
        }
    }
}


//--------------------------------------
// События

jQuery(function ($) {
    //-----------------------------------
    //Init

    sendHit(metrikaID, {}, { 'userID': userID, 'UserPage': UserPage });

    setTimeout(() => {
        notBounce(metrikaID, { 'userID': userID, 'UserPage': UserPage });
    }, 25000);

    responceTestRequest();
    $(document).trigger('loaded.yametrika');

    //-----------------------------------
    //Events

    var lastClick = 0;
    var lastClickPos = { x: 0, y: 0 };
    var lastClickElement = true;
    $(document).on('click.yametrika', function (event) {
        const [_, curTick] = getPerformanceInfo();

        const deltaTime = curTick() - lastClick;
        if (deltaTime > TIMEOUT_CLICK) {

            const curClickPos = getElementPosition(event);

            const deltaX = Math.abs(lastClickPos.x - curClickPos.x);
            const deltaY = Math.abs(lastClickPos.y - curClickPos.y);

            if (
                lastClickElement !== event.target &&
                deltaX > DELTA_SAME_CLICKS &&
                deltaY > DELTA_SAME_CLICKS &&
                deltaTime > TIMEOUT_SAME_CLICKS
            ) {

                if (
                    !$(event.target).attr('ym-disable-clickmap') ||
                    !$(event.target).attr('ym-clickmap-ignore')
                ) {

                    lastClick = curTick();
                    lastClickPos = curClickPos;
                    lastClickElement = event.target;

                    sendClick(metrikaID, event, { 'userID': userID, 'UserPage': UserPage });
                }
            }
        }
    });

    $(document).on('hashchange.yametrika', function () {
        sendHit(metrikaID, {}, { 'userID': userID, 'UserPage': UserPage });
    });

    $(document).on('beforeunload.yametrika, unload.yametrika', function () {
        sendHit(metrikaID, {}, { 'userID': userID, 'UserPage': UserPage });
    });
});