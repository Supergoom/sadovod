<?php

add_filter('site_status_tests', function ($tests) {

    unset($tests['direct']['theme_version']); //Always Updated, but //XXX: Maybe create fallback theme in case of site failure
    unset($tests['direct']['rest_availability']); //Rest API not used
    unset($tests['direct']['php_sessions']); //TODO: Maybe need to take look

    return $tests;
});

/*  Добавить обработчик ссылок -----------------------------------------------*/
add_filter('the_content', 'make_clickable');

/*  Сброс отступа -----------------------------------------------*/
add_theme_support('admin-bar', array('callback' => '__return_false'));

/*  Включить фичи html5 -----------------------------------------------*/
add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));

/*  Убрать migrate --------------------------------------------*/
function remove_jquery_migrate(&$scripts)
{
    if (!is_admin()) {
        $scripts->remove('jquery');
        $scripts->add('jquery', false, array('jquery-core'), '1.12.4');
    }
}
add_filter('wp_default_scripts', 'remove_jquery_migrate');

function remove_style_trailings($tag)
{
    return str_replace('/>', ">", $tag);
}
add_filter('style_loader_tag', 'remove_style_trailings', 10, 1);

function remove_script_trailings($tag)
{
    return str_replace(array('/>', "type='text/javascript'"), array(">", ""), $tag);
}
add_filter('script_loader_tag', 'remove_script_trailings', 10, 1);
add_filter('script_locale_loader_tag', 'remove_script_trailings', 10, 1);

/*  Убрать доступ к нефильтрованому html в комментариях --------------------------------------------*/
remove_action('comment_form', 'wp_comment_form_unfiltered_html_nonce');

/*  Убрать тег title --------------------------------------------*/
remove_action('wp_head', '_wp_render_title_tag', 1);

/*  Убрать проверку на кастомизируемость --------------------------------------------*/

function remove_customize_check()
{
    remove_action('wp_before_admin_bar_render', 'wp_customize_support_script');
}
add_action('admin_bar_menu', 'remove_customize_check', 41);

function remove_printing_styles()
{
    remove_action('wp_head', 'wp_admin_bar_header');
}
add_action('admin_bar_init', 'remove_printing_styles', 41);

function desable_attachemnt_pages($rules)
{
    foreach ($rules as $regex => $query) {
        if (strpos($regex, 'attachment') || strpos($query, 'attachment')) {
            unset($rules[$regex]);
        }
    }
    return $rules;
}
add_filter('rewrite_rules_array', 'desable_attachemnt_pages');

function cleanup_attachment_link($link)
{
    return;
}
add_filter('attachment_link', 'cleanup_attachment_link');

/*  Убрать обработчик ошибок --------------------------------------------*/
//TODO: Custom error handler
add_filter('wp_fatal_error_handler_enabled', '__return_false');

/*  Убрать xmlrpc -------------------------------------------------*/

function close_xmlrpc()
{
    global $wp_query;
    if ($_SERVER["REQUEST_URI"] == '/xmlrpc.php') {
        header('HTTP/2 404 ' . get_status_header_desc(404));
        $wp_query->set_404();
    }
}
add_action('init', 'close_xmlrpc');
add_filter('xmlrpc_enabled', '__return_false');

/*  Изменить htacces -------------------------------------------------*/
function rewrite_htaccess($rules)
{
    $theme = get_template_directory();

    $allowed_domains = array(
        get_site_root_domain(),
        get_site_domain(),
        'yandex.com',
        'yandex.ru',
        'ya.com',
        'ya.ru',
        'vk.com',
        'vk.ru',
        'google.com'
    );

    $media_files = array(
        'xml', 'css', 'js',
        'svg', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'ico',
        'pdf', 'docx', 'rtf', 'odf',
        'zip', 'rar',
        'ttf', 'woff', 'woff2',
        'mp4', 'webm',
        'mp3', 'ogg', 'webp',
        'txt'
    );
    $media_files_regex = implode('|', $media_files);

    $rules = "\n";

    $rules .= 'ServerSignature Off' . "\n";
    $rules .= 'Options All -Indexes' . "\n";
    $rules .= 'AddDefaultCharset UTF-8 ' . "\n\n";

    // Deny access to wp-config.php, xmlrpc.php, readme.html, license.txt, htaccess|htpasswd files
    $rules .= '<FilesMatch "^.*(wp-config\.php|xmlrpc\.php|readme.html|license.txt|\.log|\.[hH][tT][aApP].*)$">' . "\n";
    $rules .= "\t" . 'Order deny,allow' . "\n";
    $rules .= "\t" . 'Deny from all' . "\n";
    $rules .= '</FilesMatch>' . "\n\n";

    // Setup browser caching
    $rules .= '<IfModule mod_expires.c>' . "\n";
    $rules .= "\n";

    $rules .= "\t" . 'ExpiresActive On' . "\n";

    $rules .= "\n";
    foreach ($media_files as $file) {
        $rules .= "\t" . 'ExpiresByType ' . ext_mime_content_type($file) . ' "access 1 year"' . "\n";
    }
    $rules .= "\n";

    $rules .= "\t" . 'ExpiresByType text/html                       "access 0 seconds"' . "\n";

    // Data interchange
    $rules .= "\t" . 'ExpiresByType application/atom+xml            "access plus 1 hour"' . "\n";
    $rules .= "\t" . 'ExpiresByType application/rdf+xml             "access plus 1 hour" ' . "\n";
    $rules .= "\t" . 'ExpiresByType application/rss+xml             "access plus 1 hour"' . "\n";

    $rules .= "\t" . 'ExpiresByType application/json                "access plus 0 seconds" ' . "\n";
    $rules .= "\t" . 'ExpiresByType application/ld+json             "access plus 0 seconds"' . "\n";
    $rules .= "\t" . 'ExpiresByType application/schema+json         "access plus 0 seconds" ' . "\n";
    $rules .= "\t" . 'ExpiresByType application/vnd.geo+json        "access plus 0 seconds"' . "\n";

    $rules .= "\t" . 'ExpiresByType image/vnd.microsoft.icon        "access 3 month"' . "\n";
    $rules .= "\t" . 'ExpiresByType image/x-icon                    "access 3 month"' . "\n";
    $rules .= "\t" . 'ExpiresDefault "access 1 week"' . "\n";

    $rules .= "\n";
    $rules .= '</IfModule>' . "\n\n";

    #Alternative caching using Apache's "mod_headers", if it's installed.
    #Caching of common files - ENABLED
    $rules .= '<IfModule mod_headers.c>' . "\n";
    $rules .= "\n";

    $rules .= "\t" . '<FilesMatch "\.(' . $media_files_regex . ')$">' . "\n";
    $rules .= "\t\t" . 'Header set Cache-Control "max-age=' . MONTH_IN_SECONDS . ', public"' . "\n";
    $rules .= "\t" . '</FilesMatch>' . "\n";

    $rules .= "\n";
    $rules .= '</IfModule>' . "\n\n";

    // Setup compression
    $rules .= '<IfModule mod_deflate.c>' . "\n";
    $rules .= "\n";

    $rules .= "\t" . 'AddOutputFilterByType DEFLATE text/plain' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE text/html' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE text/xml' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE text/css' . "\n\n";

    $rules .= "\t" . 'AddOutputFilterByType DEFLATE application/xml' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE application/xhtml+xml' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE application/rss+xml' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE application/javascript' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE application/x-javascript' . "\n\n";

    $rules .= "\t" . 'AddOutputFilterByType DEFLATE font/woff' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE font/opentype' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE application/x-font-ttf' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE application/font-woff2' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE application/x-font-woff' . "\n\n";

    $rules .= "\t" . 'AddOutputFilterByType DEFLATE image/svg+xml' . "\n";
    $rules .= "\t" . 'AddOutputFilterByType DEFLATE font/x-icon' . "\n\n";

    //# Exception: Images
    $rules .= "\t" . 'SetEnvIfNoCase REQUEST_URI \.(?:gif|jpg|jpeg|png|svg|ico)$ no-gzip dont-vary' . "\n\n";

    // Drop problematic browsers
    $rules .= "\t" . 'BrowserMatch ^Mozilla/4 gzip-only-text/html' . "\n";
    $rules .= "\t" . 'BrowserMatch ^Mozilla/4\.0[678] no-gzip' . "\n";
    $rules .= "\t" . 'BrowserMatch \bMSI[E] !no-gzip !gzip-only-text/html' . "\n";

    // Make sure proxies don't deliver the wrong content
    $rules .= "\t" . 'Header append Vary User-Agent env=!dont-vary' . "\n";

    $rules .= "\n";
    $rules .= '</IfModule>' . "\n\n";

    // Setup compression for proxy
    $rules .= '<IfModule mod_headers.c>' . "\n";
    $rules .= "\n";

    $rules .= "\t" . '<FilesMatch ".(js|mjs|css|xml|gz|html|woff|woff2|ttf)$">' . "\n";
    $rules .= "\t\t" . 'Header append Vary: Accept-Encoding' . "\n";
    $rules .= "\t" . '</FilesMatch>' . "\n";

    $rules .= "\n";
    $rules .= '</IfModule>' . "\n\n";

    //Default wordpress
    $rules .= '<IfModule mod_rewrite.c>' . "\n";
    $rules .= "\n";

    $rules .= "\t" . 'RewriteEngine On' . "\n";

    //$rules .= 'LogLevel warn rewrite:trace3' . "\n"; // in shell: tail -f error_log|fgrep '[rewrite:' 

    $rules .= "\t" . 'RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]' . "\n";
    $rules .= "\t" . 'RewriteBase /' . "\n\n";

    //Disable access to wordpress files
    $rules .= "\t" . 'RewriteRule ^wp-admin/includes/ - [F,L]' . "\n";
    $rules .= "\t" . 'RewriteRule !^wp-includes/ - [S=3]' . "\n";
    $rules .= "\t" . 'RewriteRule ^wp-includes/[^/]+\.php$ - [F,L]' . "\n";
    $rules .= "\t" . 'RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F,L]' . "\n";
    $rules .= "\t" . 'RewriteRule ^wp-includes/theme-compat/ - [F,L]' . "\n\n";

    //Hotlink fix
    $rules .= "\t" . 'RewriteCond %{HTTP_REFERER} !^$' . "\n";
    foreach ($allowed_domains as $domain) {
        $rules .= "\t" . 'RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?' . $domain . ' [NC]' . "\n";
    }
    $rules .= "\t" . 'RewriteRule \.(' . $media_files_regex . ')$ - [L,R=404,NC]' . "\n\n";

    //Vulnerable HTTP methods
    $rules .= "\t" . 'RewriteCond %{THE_REQUEST} !^(POST|GET|HEAD|OPTIONS) [NC]' . "\n";
    $rules .= "\t" . 'RewriteRule .* - [L,R=405]' . "\n\n";

    //Default wordpress
    $rules .= "\t" . 'RewriteRule ^index\.php$ - [L]' . "\n";
    $rules .= "\t" . 'RewriteCond %{REQUEST_FILENAME} !-f' . "\n";
    $rules .= "\t" . 'RewriteCond %{REQUEST_FILENAME} !-d' . "\n";
    $rules .= "\t" . 'RewriteRule . /index.php [L]' . "\n";

    $rules .= "\n";
    $rules .= '</IfModule>' . "\n\n";

    //Custom error page for error 403, 404, 429 and 500
    $rules .= 'ErrorDocument 403 ' . $theme . '/layouts/403.php' . "\n";
    $rules .= 'ErrorDocument 404 ' . $theme . '/layouts/404.php' . "\n";
    $rules .= 'ErrorDocument 429 ' . $theme . '/layouts/429.php' . "\n";
    $rules .= 'ErrorDocument 500 ' . $theme . '/layouts/500.php' . "\n";

    return $rules;
}
add_filter('mod_rewrite_rules', 'rewrite_htaccess');

/*  Не давать открывать не валидные медиа-файлы -------------------------------------------------*/
function rewrite_content_htaccess()
{
    $media_files = array(
        'xml', 'css', 'js',
        'svg', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'ico',
        'pdf', 'docx', 'rtf', 'odf',
        'zip', 'rar',
        'ttf', 'woff', 'woff2',
        'mp4', 'webm',
        'mp3', 'ogg', 'webp',
        'txt'
    );

    // Disable access to all file types except the following
    $rules = 'Order deny,allow' . "\n";
    $rules .= 'Deny from all' . "\n";
    $rules .= '<Files ~ ".(' . implode('|', $media_files) . ')$">' . "\n";
    $rules .= "\t" . 'Allow from all' . "\n";
    $rules .= '</Files>' . "\n\n";

    $uploads_dir = wp_get_upload_dir()['basedir'];
    file_put_contents($uploads_dir . '/.htaccess', $rules);
}
add_action('generate_rewrite_rules', 'rewrite_content_htaccess');

/*  Изменить htacces -------------------------------------------------*/
function rewrite_theme_htaccess()
{
    //Default wordpress
    $rules = '<IfModule mod_rewrite.c>' . "\n";
    $rules .= "\n";

    $rules .= "\t" . 'RewriteEngine On' . "\n";

    //Default wordpress
    $rules .= "\t" . 'RewriteRule ^index\.php$ - [L]' . "\n";
    $rules .= "\t" . 'RewriteCond %{REQUEST_URI} .*/storage  [OR]' . "\n";
    $rules .= "\t" . 'RewriteCond %{REQUEST_FILENAME} \.md$ [OR]' . "\n";
    $rules .= "\t" . 'RewriteCond %{REQUEST_FILENAME} \.bak$ [OR]' . "\n";
    $rules .= "\t" . 'RewriteCond %{REQUEST_FILENAME} \.php$' . "\n";
    $rules .= "\t" . 'RewriteRule .* - [L,R=404,NC]' . "\n";

    $rules .= "\n";
    $rules .= '</IfModule>' . "\n\n";

    $theme_dir = get_template_directory();
    file_put_contents($theme_dir . '/.htaccess', $rules);
}
add_action('generate_rewrite_rules', 'rewrite_theme_htaccess');

/* Очистка шапки -----------------------------------------------*/
function cleanup_head()
{
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_resource_hints', 2);
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'noindex', 1);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
add_action('init', 'cleanup_head');

/* Отключение REST API -----------------------------------------------*/
function disable_rest()
{
    add_filter('rest_enabled', '__return_false');
    remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
    remove_action('template_redirect', 'rest_output_link_header', 11);
    remove_action('auth_cookie_malformed', 'rest_cookie_collect_status');
    remove_action('auth_cookie_expired', 'rest_cookie_collect_status');
    remove_action('auth_cookie_bad_username', 'rest_cookie_collect_status');
    remove_action('auth_cookie_bad_hash', 'rest_cookie_collect_status');
    remove_action('auth_cookie_valid', 'rest_cookie_collect_status');
    remove_filter('rest_authentication_errors', 'rest_cookie_check_errors', 100);
    remove_action('init', 'rest_api_init');
    remove_action('rest_api_init', 'rest_api_default_filters', 10);
    remove_action('parse_request', 'rest_api_loaded');
    remove_action('rest_api_init', 'wp_oembed_register_route');
    remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10);
}
//add_action('init', 'disable_rest'); Нужен для чат бота от ChatGPT

/* Отключение rss ленты -----------------------------------------------*/
function disable_feed()
{
    wp_redirect(get_option('siteurl'));
}
add_action('do_feed', 'disable_feed', 1);
add_action('do_feed_rdf', 'disable_feed', 1);
add_action('do_feed_rss', 'disable_feed', 1);
add_action('do_feed_rss2', 'disable_feed', 1);
add_action('do_feed_atom', 'disable_feed', 1);

/* Отключение Emojii -----------------------------------------------*/
function disable_default_emoji()
{
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'disable_default_emoji');

/* Созранить отчет csp -----------------------------------------------*/
function save_csp_report()
{
    if (
        strpos($_SERVER["REQUEST_URI"], '/report') !== false
        && isset($_GET) && isset($_GET['type']) && $_GET['type'] == 'csp'
    ) {
        header('HTTP/2 200 ' . get_status_header_desc(200));
        header('Content-type: application/json; charset=UTF-8');

        $data = file_get_contents('php://input');
        $report = json_decode($data, true);

        if ($report && check_csp_record($report)) {
            $report_content = $report['csp-report'];

            $report_document = stripslashes(sanitize_text_field($report_content['document-uri']));
            $report_referrer = stripslashes(sanitize_text_field($report_content['referrer']));
            $report_blocked = stripslashes(sanitize_text_field($report_content['blocked-uri']));

            $report_violated = stripslashes(sanitize_text_field($report_content['violated-directive']));
            $report_effective = stripslashes(sanitize_text_field($report_content['effective-directive']));

            $report_policy = stripslashes(sanitize_text_field($report_content['original-policy']));

            $report_text = sprintf(__('On page: %s', 'sadovod'), $report_document) . "\n";
            $report_text .= sprintf(__('Referrer: %s', 'sadovod'), $report_referrer) . "\n";
            $report_text .= sprintf(__('Got blocked url: %s', 'sadovod'), $report_blocked) . "\n";
            $report_text .= sprintf(__('Violated directive: %s', 'sadovod'), $report_violated) . "\n";
            $report_text .= sprintf(__('Effective directive: %s', 'sadovod'), $report_effective) . "\n";

            $report_text .= sprintf(__('Original policy: %s', 'sadovod'), $report_policy) . "\n";;

            //-----------------------------------------------------------
            $report_file = stripslashes(sanitize_text_field($report_content['source-file'])) . "\n";
            $report_file_line = stripslashes(sanitize_text_field($report_content['line-number'])) . "\n";
            $report_file_column = stripslashes(sanitize_text_field($report_content['column-number'])) . "\n";

            if (!empty($report_file)) {
                $report_text .= sprintf(__('Reported file: %s', 'sadovod'), $report_file) . "\n";
                $report_text .= sprintf(__('Line: %s. Column: %s', 'sadovod'), $report_file_line, $report_file_column) . "\n";
            }
            //------------------------------------------------------

            $report_text = stripslashes(sanitize_text_field($report_text));
            add_report(1, $report_text);
        }

        $out['success'] = array(
            'text' => __('Report successfully received', 'sadovod')
        );

        exit(json_encode($out));
    }
}
add_action('parse_request', 'save_csp_report');

/* Поготовка к настройке политики защиты контента -----------------------------------------------*/
function setup_csp_nonce()
{
    global $style_csp_nonce;
    global $script_csp_nonce;
    global $font_csp_nonce;

    $style_csp_nonce = base64_encode(rand(100, 999) . wp_create_nonce('style-' . rand(10000, 99999)) . rand(100, 999));
    $script_csp_nonce = base64_encode(rand(100, 999) . wp_create_nonce('script-' . rand(10000, 99999)) . rand(100, 999));
    $font_csp_nonce = base64_encode(rand(100, 999) . wp_create_nonce('font-' . rand(10000, 99999)) . rand(100, 999));
}

add_action('init', 'setup_csp_nonce');

function add_csp_nonce_to_style($tag)
{
    global $style_csp_nonce;
    return preg_replace("/(css-\w+')/", '$1' . " nonce='" . $style_csp_nonce . "'", $tag);
    //return str_replace("css'", "css' nonce='" . $style_csp_nonce . "'", $tag);
}
add_filter('style_loader_tag', 'add_csp_nonce_to_style', 10, 1);
add_filter('autoptimize_html_after_minify', 'add_csp_nonce_to_style', 10, 1);

function add_csp_nonce_to_script($tag)
{
    global $script_csp_nonce;
    //return preg_replace("/(js-\w+')/", '$1' . " nonce='" . $script_csp_nonce . "'", $tag);
    return str_replace('<script', "<script nonce='" . $script_csp_nonce . "'", $tag);
}
add_filter('script_loader_tag', 'add_csp_nonce_to_script', 10, 1);
add_filter('script_locale_loader_tag', 'add_csp_nonce_to_script', 10, 1);
add_filter('autoptimize_html_after_minify', 'add_csp_nonce_to_script', 10, 1);

function add_csp_nonce_to_script_tag($attributes)
{
    global $script_csp_nonce;
    $attributes['nonce'] = $script_csp_nonce;

    return $attributes;
}
add_filter('wp_inline_script_attributes', 'add_csp_nonce_to_script_tag', 10, 1);

/* Настройка политики защиты контента -----------------------------------------------*/
function setup_csp()
{
    global $style_csp_nonce;
    global $script_csp_nonce;
    global $font_csp_nonce;

    $domain = get_site_domain();
    $rdomain = get_site_root_domain();

    header(
        "Content-Security-Policy:"
            . " default-src 'self' data: blob:;"

            . " script-src 'self' " . (is_admin() ? " 'unsafe-inline'" : "") . " 'unsafe-eval' https://" . $rdomain . " https://*." . $rdomain
            . " https://*.google.com https://*.gstatic.com"
            . " https://youtube.com https://*.youtube.com"
            . " https://yandex.ru https://*.yandex.ru https://yandex.com https://*.yandex.com"
            . " https://*.maps.yandex.net https://yastatic.net"
            . " https://*.bitrix24.ru/"
            . (is_admin() ? "" : " 'strict-dynamic' 'nonce-" . $script_csp_nonce . "'")
            . ";"

            . " style-src 'self' 'unsafe-inline' https://" . $rdomain . " https://*." . $rdomain . " data: blob:"
            . " https://*.bitrix24.ru/"
            . " https://*.googleapis.com"
            //. " 'nonce-" . $style_csp_nonce . "' 'nonce-" . $font_csp_nonce . "';"
            . " ;"

            . " font-src 'self' https://" . $rdomain . " https://*." . $rdomain . " data:"
            . " https://*.googleapis.com https://*.gstatic.com;"

            . " img-src 'self' https://" . $rdomain . " https://*." . $rdomain . " data: blob:"
            . " https://*.google.com https://*.gstatic.com"
            . " https://yandex.ru https://*.yandex.ru https://yandex.com https://*.yandex.com"
            . " https://*.maps.yandex.net https://yastatic.net"
            . " https://youtube.com https://*.youtube.com"
            . " https://ps.w.org"
            . " https://*.gravatar.com https://s.w.org;"

            . " frame-src 'self' https://" . $rdomain . " https://*." . $rdomain
            . " https://google.com https://*.google.com"
            . " https://youtube.com https://*.youtube.com"
            . " https://yandex.ru https://*.yandex.ru https://yandex.com https://*.yandex.com;"

            . " frame-ancestors 'self' https://" . $rdomain . " https://*." . $rdomain
            . " https://google.com https://*.google.com"
            . " https://youtube.com https://*.youtube.com"
            . " https://yandex.ru https://*.yandex.ru https://yandex.com https://*.yandex.com;"

            . " form-action 'self';"
            . " object-src 'none';"
            . " base-uri 'none';"

            . " connect-src 'self' https://" . $rdomain . " https://*." . $rdomain . " wss://" . $rdomain . ":* wss://*." . $rdomain . ":*"
            . " https://*.google.com https://*.gstatic.com"
            . " https://youtube.com https://*.youtube.com"
            . " https://yandex.ru https://*.yandex.ru https://yandex.com https://*.yandex.com"
            . " https://*.maps.yandex.net https://*.taxi.yandex.net https://yastatic.net;"

            . " media-src 'self'"

            //. " require-trusted-types-for 'script';"
            . " report-uri https://" . $domain . "/report/?type=csp;"
    );
}
//add_action('init', 'setup_csp');

function setup_cors()
{
    $origin = get_request_origin();

    $allowed_origins = array(
        'https://sadovod.my3cx.ru',
        'https://' . get_site_root_domain(),
        'https://dev.' . get_site_root_domain(),
        'https://test.' . get_site_root_domain(),
    );

    $allowed = 'https://' . get_site_domain();
    if (in_array($origin, $allowed_origins)) {
        $allowed = $origin;
        add_filter('allowed_http_origin', '__return_true');
    }

    header('Access-Control-Allow-Origin: ' . $allowed);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, HEAD");
        header('Access-Control-Allow-Headers: x-requested-with');

        header('Content-Length: 0');
        header('Content-Type: text/plain');

        exit();
    }
}
add_action('init', 'setup_cors');

function handle_head_request()
{
    if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
        exit();
    }
}
add_action('send_headers', 'handle_head_request');


/* Установить заголовок Last-Modified -----------------------------------------------*/
function setup_modified()
{
    $media_files = array(
        'xml', 'css', 'js',
        'svg', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'ico',
        'pdf', 'docx', 'rtf', 'odf',
        'zip', 'rar',
        'ttf', 'woff', 'woff2',
        'mp4', 'webm',
        'mp3', 'ogg', 'webp'
    );

    $file = $_SERVER['SCRIPT_FILENAME'];
    if (
        !isset($file) || !is_file($file)
        || !in_array(get_file_extension($file), $media_files)
    ) {
        return;
    }

    $timestamp = filemtime($file);
    $file_md5 = md5($timestamp . $file);
    $gmt_mtime = wp_date("D, d M Y H:i:s T", $timestamp);

    if (
        isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime
        || isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $file_md5
    ) {
        header('HTTP/1.1 304 Not Modified');
        header("Vary: Accept-Encoding, User-Agent");
        exit();
    }

    header('ETag: "' . $file_md5 . '"');
    header('Last-Modified: ' . $gmt_mtime);
    header('Cache-Control: must-revalidate, proxy-revalidate, max-age=3600');
}
add_action('init', 'setup_modified');

/* Не передавать полные рефереры при переходе на внешние сайты -----------------------------------------------*/
function setup_refferer_options()
{
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
add_action('init', 'setup_refferer_options');

/* Использовать соединение повторно -----------------------------------------------*/
function setup_connection_options()
{
    header('Connection: keep-alive');
}
add_action('init', 'setup_connection_options');

/* Защита от обнаружения версии php -----------------------------------------------*/
function remove_php_info()
{
    header_remove('X-Powered-By');
}
add_action('init', 'remove_php_info');

/* Защита от подделки типа файла -----------------------------------------------*/
function setup_content_options()
{
    header('X-Content-Type-Options: nosniff');
}
add_action('init', 'setup_content_options');

/* Максимальная защита от xss -----------------------------------------------*/
function setup_xframe_xss_options()
{
    header('X-XSS-Protection: 1; mode=block');
}
add_action('init', 'setup_xframe_xss_options');

/* Защита от кликджакинга -----------------------------------------------*/
function setup_xframe_options()
{
    header('X-Frame-Options: SAMEORIGIN');
}
add_action('init', 'setup_xframe_options');

/* Работать только по https -----------------------------------------------*/
function setup_transport_security()
{
    header('Strict-Transport-Security: max-age=' . YEAR_IN_SECONDS . '; includeSubdomains');
}
add_action('init', 'setup_transport_security');


/* Оптимизация под поисковики -----------------------------------------------*/
function setup_robots_txt($output)
{
    $str = '
	Disallow: /cgi-bin
	Disallow: /?
	Disallow: *?s=
	Disallow: *&s=
	Disallow: /search
	Disallow: /author/
	Disallow: /office/
	Disallow: */embed
	Disallow: */page/
	Disallow: */xmlrpc.php
	Disallow: *utm*=
	Disallow: *openstat=

	Clean-param: type /feed/
	';

    $str = trim($str);
    $str = preg_replace('/^[\t ]+(?!#)/mU', '', $str);
    $output .= "$str\n";

    return $output;
}
add_action('robots_txt', 'setup_robots_txt', -1);

function setup_sitemap_max_urls($num, $object_type)
{
    return 500;
}
add_filter('wp_sitemaps_max_urls', 'setup_sitemap_max_urls', 10, 2);

function clear_sitemaps_posts_query_args($args, $post_type)
{
    return $args;
}
add_filter('wp_sitemaps_posts_query_args', 'clear_sitemaps_posts_query_args', 10, 2);

/* Не выводить версию вордпресс -----------------------------------------------*/
if (!function_exists('no_generator')) {
    function no_generator()
    {
        return '';
    }
}
add_filter('the_generator', 'no_generator');

/* Не выводить админбар вордпресс -----------------------------------------------*/
function hide_admin_bar($content)
{
    if (!current_user_can('manage_options')) {
        return false;
    }
    return true;
}
add_filter('show_admin_bar', 'hide_admin_bar');

/* Картинки -----------------------------------------------*/
function post_image_html($html, $post_id)
{

    if (!is_admin() and strpos($html, 'data-src') === false) {
        if (empty($html)) {
            $html = '<img itemprop="image" alt="" title="" data-src="' . get_stylesheet_directory_uri() . '/assets/img/placeholder-thumbnail.jpg" class="lazyload">';
        } else {
            $html = str_replace(' src', ' data-src', $html);
        }

        //$html = preg_replace('/(width|height)="\d*"\s/', "", $html);
    }

    $html = str_replace('/>', '>', $html);

    $title = esc_html(get_the_title($post_id));
    if (empty($title))
        $title = get_site_root_domain();

    if (strpos($html, 'alt') === false) {
        $html = str_replace('<img ', '<img alt="' . $title . '"', $html);
    } else if (strpos($html, 'alt=""') !== false) {
        $html = str_replace('alt=""', 'alt="' . $title . '"', $html);
    }

    if (strpos($html, 'title') === false) {
        $html = str_replace('<img ', '<img title="' . $title . '"', $html);
    } else if (strpos($html, 'title=""') !== false) {
        $html = str_replace('title=""', 'title="' . $title . '"', $html);
    }

    return $html;
}

add_filter('post_thumbnail_html', 'post_image_html', 10, 2);

function attachment_image_html($html, $attachment_id)
{
    $post = get_post_parent($attachment_id);
    if ($post)
        return post_image_html($html, $post->ID);

    return $html;
}
add_filter('wp_get_attachment_image', 'attachment_image_html', 10, 2);

function change_empty_alt_to_title($response)
{
    if (isset($response['uploadedToTitle'])) {
        if (!$response['alt']) {
            $response['alt'] = sanitize_text_field($response['uploadedToTitle']);
        }

        if (!$response['title']) {
            $response['title'] = sanitize_text_field($response['uploadedToTitle']);
        }
    }

    return $response;
}

add_filter('wp_prepare_attachment_for_js', 'change_empty_alt_to_title');

function post_image_src($image, $attachment_id, $size)
{
    if (/* !is_admin() and */$image === false) {
		$image_id = get_theme_mod('record_placeholder', get_theme_mod('custom_logo_dark'));
		if ($image_id) {
			$src = wp_get_attachment_image_url($image_id, $size);
			if (!$src) {
				$src = get_stylesheet_directory_uri() . '/assets/img/placeholder.jpg';
				if ($size == 'thumbnail')
					$src = get_stylesheet_directory_uri() . '/assets/img/placeholder-thumbnail.jpg';
			}

			$image = array($src, 0, 0, false);
		}
	}

    return $image;
}
add_filter('wp_get_attachment_image_src', 'post_image_src', 10, 3);

function fix_image_size_attr($html)
{
    return str_replace(array('width="1"', 'height="1"'), '', $html);
}
add_filter('wp_get_attachment_image', 'fix_image_size_attr');
