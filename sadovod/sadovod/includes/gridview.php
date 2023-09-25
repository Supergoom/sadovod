<?php

/* Отображение сетки -----------------------------------------------*/
function setup_theme_grid_data()
{
    if (isset($_GET['grid'])) {
        if ($_SESSION['show_grid'] = ($_GET['grid'] === '1' || $_GET['grid'] === '2')) {
            $_SESSION['show_grid_type'] = $_GET['grid'];
        }
    }
}
add_filter('init', 'setup_theme_grid_data');

function devmode_clear_grid_args($args)
{
    $args['grid'] = 0;

    return $args;
}
add_filter('devmode_clear_args', 'devmode_clear_grid_args');

function setup_theme_grid_class($classes)
{
    if ($_SESSION['show_grid'] ?? false) {
        $grid_type = is_page('community') || is_page('office') ? '1' : $_SESSION['show_grid_type'];

        $classes[] = "show-grid";
        $classes[] = "show-grid-" .  $grid_type;

        if ($grid_type === '1') {
            add_devmode_detail(__('Main grid enabled', 'sadovod-misc'));
        } elseif ($grid_type === '2') {
            add_devmode_detail(__('Content grid enabled', 'sadovod-misc'));
        }
    }

    return $classes;
}
add_filter('body_class', 'setup_theme_grid_class');

function setup_theme_grid_colums($classes)
{
    if ($_SESSION['show_grid'] ?? false) {
        echo '<div class="grid-view-container main-grid-view-container">';
        echo '<div class="container-fluid">';
        echo '<div class="row">';
        echo str_repeat('<div class="col-1"></div>', 12);
        echo '</div>';
        echo '</div>';
        echo '</div>';

        if ($_SESSION['show_grid_type'] === '2') {
            add_action('main_content', function () {
                echo '<div class="grid-view-container content-grid-view-container">';
                echo '<div class="container-fluid">';
                echo '<div class="row">';
                echo str_repeat('<div class="col-1"></div>', 12);
                echo '</div>';
                echo '</div>';
                echo '</div>';
            });
        }
    }

    return $classes;
}
add_action('wp_body_open', 'setup_theme_grid_colums');

/*  Очистка после выхода -----------------------------------------------*/
function clear_grid_data_on_logout()
{
    unset($_SESSION['show_grid']);
}
//add_action('wp_logout', 'clear_grid_data_on_logout');
