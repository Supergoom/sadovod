<?php


function localize_sadovod($locale)
{
    if (isset($_GET['l']))
        return esc_attr($_GET['l']);

    return $locale;
}
add_filter('locale', 'localize_sadovod');

function setup_sadovod_translations()
{
    $locale = determine_locale();
    $path = get_template_directory() . '/languages/';
    $domains = array('sadovod', 'sadovod-records', 'sadovod-dialogs', 'sadovod-mail', 'sadovod-scripts', 'sadovod-emoji', 'sadovod-misc', 'sadovod-colors');

    foreach ($domains as $domain) {
        load_textdomain($domain, $path . $domain . '-' . $locale . '.mo');
    }
}
add_action('after_setup_theme', 'setup_sadovod_translations', 1);

/**
 * Take PO file name for all JSON files in domain
 */
function compile_single_json($jsonPath, $poPath)
{
    if (strpos($poPath, 'sadovod') !== false) {
        $info = pathinfo($poPath);
        $jsonPath = $info['dirname'] . '/' . $info['filename'] . '.json';
    }
    return $jsonPath;
}
add_filter('loco_compile_single_json', 'compile_single_json', 999, 3);

/**
 * Set name of translation file
 */
function load_single_json($file, $handle, $domain)
{
    if (strpos($domain, 'sadovod') !== false && is_string($file)) {
        $info = pathinfo($file);
        $file = $info['dirname'] . '/' . $domain . '-' . get_locale() . '.json';
    }
    return $file;
}
add_filter('load_script_translation_file', 'load_single_json', 999, 3);


/* Поправить склонение месяцев в дате -----------------------------------------------*/
function update_months_format($date, $req_format)
{
    if (false !== strpos($req_format, '\\') || !preg_match('/[FMlS]/', $req_format) || determine_locale() !== 'ru_RU')
        return $date;

    $date = strtr($date, array(
        'Январь' => 'января', 'Февраль' => 'февраля', 'Март' => 'марта', 'Апрель' => 'апреля', 'Май' => 'мая', 'Июнь' => 'июня', 'Июль' => 'июля', 'Август' => 'августа', 'Сентябрь' => 'сентября', 'Октябрь' => 'октября', 'Ноябрь' => 'ноября', 'Декабрь' => 'декабря',
        'Янв' => 'январь', 'Фев' => 'февраль', 'Мар' => 'март', 'Апр' => 'апрель', 'Июн' => 'июнь', 'Июл' => 'июль', 'Авг' => 'август', 'Сен' => 'сентябрь', 'Окт' => 'октябрь', 'Ноя' => 'ноябрь', 'Дек' => 'декабрь',
    ));

    return $date;
}
add_filter('wp_date', 'update_months_format', 11, 2);

/* Добавить ссылку для быстрого перехода между файлами в Loco Translate -----------------------------------------------*/
function add_loco_tranlation_edit_link($force = false)
{
    $screen = get_current_screen();

    $link = $text = '';
    if ($screen->id == 'loco-translate_page_loco-theme') {
        $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        if (strpos($url, '.pot&') !== false) {
            $link = get_admin_url(null, 'admin.php?' . str_replace('.pot', '-ru_RU.po', $url));
            $text = __('To edit template localization file, use this link.', 'sadovod');
        } else if (strpos($url, '.po&') !== false) {
            $link = get_admin_url(null, 'admin.php?' . str_replace('-ru_RU.po', '.pot', $url));
            $text = __('To edit template localization <b style="color:#fe632a">markup</b> file, use this link.', 'sadovod');
        }
        if (!empty($link)) {
?>
            <div id="loco-localization-warn" class="notice notice-info">
                <p>
                    <strong class="has-icon"><?php esc_html_e('Info', 'sadovod') ?>:</strong>
                    <span class="loco-msg">
                        <?= $text ?>
                        <a href="<?= $link; ?>"><?= __('Open file', 'sadovod'); ?></a>
                    </span>
                </p>
            </div>
<?php
        }
    }
}
add_action('loco_admin_notices', 'add_loco_tranlation_edit_link');
