<?php

error_reporting(E_ALL);

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('error_log', get_template_directory() . '/php-errors.log');

if (is_user_logged_in() and current_user_can('manage_options')) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}
