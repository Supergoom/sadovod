<?php

/* Проверка на странные запросы -----------------------------------------------*/
function check_to_send_zip()
{
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $uri = $_SERVER['REQUEST_URI'] ?? '';

    if (
        preg_match('/nikto|sqlmap|zgrab|python-requests|go-http-client/i', $useragent) ||
        preg_match('/wordpress|wp-config/', $uri) ||
        preg_match('/eval\(|[a-z0-9]{2000}/', $uri) ||
        preg_match('/gzinflate\(|[a-z0-9]{2000}/', $uri) ||
        preg_match('/(\<|%3C).*script.*(\>|%3E)/i', $uri) ||
        preg_match('/base64_(en|de)code[^(]*\([^)]*\)/', $uri) ||
        preg_match('/GLOBALS(=|\[|\%[0-9A-Z]{0,2})/', $uri) ||
        preg_match('/REQUEST(=|\[|\%[0-9A-Z]{0,2})/', $uri) ||
        preg_match('/\.ini|etc\/passwd|self\/environ/', $uri) ||
        preg_match('/(\.\/|\..\/|\...\/)+(motd|etc|bin)/', $uri) ||
        preg_match('/(\\|\.\.\.|\.\.\/|~|`)/', $uri) ||
        preg_match('/(<|>|\'|%0A|%0D|%27|%3C|%3E|%00)/i', $uri) ||
        preg_match('/(<|>|\'|\+|%2B|%0A|%0D|%27|%3C|%3E|%00)/i', $uri) ||
        preg_match('/concat[^\(]*\(/i', $uri) ||
        preg_match('/union([^s]*s)+elect/i', $uri) ||
        preg_match('/union([^a]*a)+ll([^s]*s)+elect/i', $uri) ||
        preg_match('/(;|<|>|\'|"|\)|%0A|%0D|%22|%27|%3C|%3E|%00).*(\/\*|union|select|insert|drop|delete|update|'
            . 'cast|create|char|convert|alter|declare|order|script|set|md5|benchmark|encode)/i', $uri)
    ) {
        write_log('New bot detected', 'bot_attack');
        send_zip();
        exit;
    }
}
add_action('init', 'check_to_send_zip', 1);

/*  Не давать выполняться на доменах отличных от gissnt -------------------------------------------------*/
// Защита от подмены Host заголовка

function close_unknow_domains()
{
    if (isset($_SERVER['SERVER_NAME'])) {
        preg_match('/([\w-]+\.[\w-]+)[\/|:]?$/', $_SERVER['SERVER_NAME'], $matches);
        $root_domain = $matches[0];

        if ($root_domain === 'gissnt.ru')
            return;

        if ($_SERVER['SERVER_NAME'] !== get_site_domain()) {
            write_log('Runing on incorrect domain!', 'host_attack');
            send_zip();
            exit;
        }
    }

    $_SERVER['SERVER_NAME'] = get_site_domain();
}
add_action('init', 'close_unknow_domains', 1);

/*  Вспомогательные функции -------------------------------------------------*/

function write_log($text, $file)
{
    $msg = $text . "\n";
    $msg .= 'Date: ' . wp_date('j F Y H:i:s') . "\n";
    $msg .= 'Domain: ' . ($_SERVER['SERVER_NAME'] ?? '') . "\n";
    $msg .= 'Refferer: ' . ($_SERVER['HTTP_REFERER'] ?? '') . "\n";
    $msg .= 'User Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? '') . "\n";
    $msg .= 'Http host: ' . ($_SERVER['HTTP_HOST'] ?? '') . "\n";
    $msg .= 'Remote: ' . ($_SERVER['REMOTE_ADDR'] ?? '') . "\n";
    $msg .= 'Script: ' . ($_SERVER['SCRIPT_FILENAME'] ?? '') . "\n";
    $msg .= 'Request: ' . ($_SERVER['REQUEST_URI'] ?? '') . "\n";
    $msg .= 'Root: ' . ($_SERVER['DOCUMENT_ROOT'] ?? '') . "\n";
    $msg .= '-------------------------------------' . "\n";

    $log_dir = get_template_directory() . '/storage/logs';
    if (!is_dir($log_dir))
        mkdir($log_dir, 0755, true);

    file_put_contents($log_dir . '/' . $file . '.log', $msg, FILE_APPEND);
}

function send_zip()
{
    $file = get_template_directory() . '/storage/data/10G.gzip';

    header("Content-Encoding: gzip");
    header("Content-Length: " . filesize($file));

    if (ob_get_level()) ob_end_clean();
    readfile($file);
}
