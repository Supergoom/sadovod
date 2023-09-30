<?php

/*  Система ------------------------------------------------*/
include_once('config/debug.php');

/*  Вспомогательные функции --------------------------------*/
include_once('includes/mysql.php');

include_once('includes/ico_gen.php');

include_once('includes/devmode.php');

include_once('includes/ratelimit.php');
include_once('includes/helpers.php');
include_once('includes/scripts.php');
include_once('includes/images.php');
include_once('includes/utils.php');
include_once('includes/seo.php');

include_once('includes/page.php');
include_once('includes/term.php');
include_once('includes/comment.php');
include_once('includes/comments.php');
include_once('includes/category.php');


include_once('includes/nav.php');
include_once('includes/path.php');
include_once('includes/reports.php');
include_once('includes/profile-update.php');

/*  Установка --------------------------------*/
include_once('includes/install.php');

/*  Вордпресс ----------------------------------------------*/
include_once('config/protection.php');

include_once('config/lang.php');

include_once('config/template.php');
include_once('config/theme.php');
include_once('config/embed.php');
include_once('config/mail.php');
include_once('config/pwa.php');
include_once('config/seo.php');

/*  Настройки темы ------------------------------------------*/
include_once('settings/setup.php');

/*  Настройка ассетов ---------------------------------------*/
include_once('assets/setup.php');

/*  Загрузка модулей --------------------------------------------*/
include_once('modules/slugify/main.php');
include_once('modules/darkify/main.php');
include_once('modules/upscale/main.php');

/*  AJAX --------------------------------------------------------*/

if (wp_doing_ajax()) {
    include_once('requests/user_message.php');
    include_once('requests/filter-blog.php');
};

// Перенаправление со страниц входа


// Перенаправление на страницу регистрации

function register_link_url($url)
{
    if (!is_user_logged_in()) {
        if (get_option('users_can_register'))
            $url = '<li><a href="' . get_bloginfo('url') . "/register" . '">' . __('Register', 'yourtheme') . '</a></li>';
        else  $url = '';
    } else {
        $url = '<li><a href="' . admin_url() . '">' . __('Site Admin', 'yourtheme') . '</a></li>';
    }
    return $url;
}

add_filter('register', 'register_link_url', 10, 2);

//форма обратной связи

/**
 * Шорткод вывода формы
 *
 */

add_shortcode('art_feedback', 'art_feedback');

function art_feedback()
{

    ob_start();
    ?>
    <form id="add_feedback">
        <p>
        <div class="main-form__top flex gap-[20px]">
            <label for="">
                <span>
                   <input type="text" name="art_name" id="art_name" class="required art_name" placeholder="Ваше имя"
                          value=""/>
                 </span>
            </label>
            <label for="">
                <span>
                  <input type="email" name="art_email" id="art_email" class="required art_email"
                         placeholder="Ваш E-Mail" value=""/>
                </span>
            </label>
            <label for="">
                <span>
                   <input type="text" name="art_tel" id="art_tel" class="required art_tel" placeholder="Телефон"
                          value=""/>
                </span>
            </label>
        </div>
        <div class="main-form__bottom flex gap-[20px]">
            <span>
                <textarea name="art_comments" id="art_comments" placeholder="Сообщение" rows="2" cols="85"
                          class="required art_comments"></textarea>
            </span>
            <input type="checkbox" name="art_anticheck" id="art_anticheck" class="art_anticheck"
                   style="display: none !important;" value="true" checked="checked"/>

            <input type="text" name="art_submitted" id="art_submitted" value="" style="display: none !important;"/>

            <input type="submit" id="submit-feedback" class="button" value="Отправить"/>
        </div>
        </p>
    </form>
    <?php

    return ob_get_clean();
}

add_action('wp_enqueue_scripts', 'art_feedback_scripts');
/**
 * Подключение файлов скрипта формы обратной связи
 */
function art_feedback_scripts()
{

    // Обрабтка полей формы
    wp_enqueue_script('jquery-form');

    // Подключаем файл скрипта
    wp_enqueue_script(
        'feedback',
        get_stylesheet_directory_uri() . '/assets/js/feedback.js',
        ['jquery'],
        1.0,
        true
    );

    // Задаем данные обьекта ajax
    wp_localize_script(
        'feedback',
        'feedback_object',
        [
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('feedback-nonce'),
        ]
    );

}

add_action('wp_ajax_feedback_action', 'ajax_action_callback');
add_action('wp_ajax_nopriv_feedback_action', 'ajax_action_callback');
/**
 * Обработка скрипта
 *
 */
function ajax_action_callback()
{

    // Массив ошибок
    $err_message = [];

    // Проверяем nonce. Если проверкане прошла, то блокируем отправку
    if (!wp_verify_nonce($_POST['nonce'], 'feedback-nonce')) {
        wp_die('Данные отправлены с левого адреса');
    }

    // Проверяем на спам. Если скрытое поле заполнено или снят чек, то блокируем отправку
    if (false === $_POST['art_anticheck'] || !empty($_POST['art_submitted'])) {
        wp_die('Пошел нахрен, мальчик!(c)');
    }

    // Проверяем полей имени, если пустое, то пишем сообщение в массив ошибок
    if (empty($_POST['art_name']) || !isset($_POST['art_name'])) {
        $err_message['name'] = 'Пожалуйста, введите ваше имя.';
    } else {
        $art_name = sanitize_text_field($_POST['art_name']);
    }

    // Проверяем полей емайла, если пустое, то пишем сообщение в массив ошибок
    if (empty($_POST['art_email']) || !isset($_POST['art_email'])) {
        $err_message['email'] = 'Пожалуйста, введите адрес вашей электронной почты.';
    } elseif (!preg_match('/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i', $_POST['art_email'])) {
        $err_message['email'] = 'Адрес электронной почты некорректный.';
    } else {
        $art_email = sanitize_email($_POST['art_email']);

    }
    // Проверяем полей темы письма, если пустое, то пишем сообщение по умолчанию
    if (empty($_POST['art_tel']) || !isset($_POST['art_tel'])) {
        $art_subject = 'Телефон не указан';
    } else {
        $art_subject = sanitize_text_field($_POST['art_tel']);
    }

    // Проверяем полей сообщения, если пустое, то пишем сообщение в массив ошибок
    if (empty($_POST['art_comments']) || !isset($_POST['art_comments'])) {
        $err_message['comments'] = 'Пожалуйста, введите ваше сообщение.';
    } else {
        $art_comments = sanitize_textarea_field($_POST['art_comments']);
    }

    // Проверяем массив ошибок, если не пустой, то передаем сообщение. Иначе отправляем письмо
    if ($err_message) {

        wp_send_json_error($err_message);

    } else {

        // Указываем адресата
        $email_to = '';

        // Если адресат не указан, то берем данные из настроек сайта
        if (!$email_to) {
            $email_to = get_option('admin_email');
        }

        $body = "Имя: $art_name \nEmail: $art_email \n\nСообщение: $art_comments";
        $headers = 'From: ' . $art_name . ' <' . $email_to . '>' . "\r\n" . 'Reply-To: ' . $email_to;

        // Отправляем письмо
        wp_mail($email_to, $art_subject, $body, $headers);

        // Отправляем сообщение об успешной отправке
        $message_success = 'Собщение отправлено. В ближайшее время я свяжусь с вами.';
        wp_send_json_success($message_success);
    }

    // На всякий случай убиваем еще раз процесс ajax
    wp_die();

}