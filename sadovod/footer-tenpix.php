</div><!-- #primary -->

<footer itemscope itemtype="http://schema.org/WPFooter">
    <meta itemprop="copyrightYear" content="<?= date('Y'); ?>">
    <meta itemprop="copyrightHolder" content="<?= get_bloginfo('name'); ?>">

    <div class="container">
        <div id="footer-main" class="row row-center">
            <div class="col-md-4 footer-logo-col">
                <a href="/">
                    <img class="site-logo lazyload" title="<?php bloginfo('name'); ?>" alt="<?php bloginfo('name'); ?>"
                        src="<?= get_logo_placeholder('custom_logo'); ?>"
                        data-src="<?= wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'tiny'); ?>">
                </a>
                <p>
                    Консалтинговая компания<br>
                    Внедрение, настройка, интеграция Битрикс24
                </p>
            </div>
            <div class="col-md-5 footer-nav-col">
                <div class="nav-menu">
                    <a class="nav-link" href="#"><i class="i-check"></i><span>Вакансии</span></a>
                    <a class="nav-link" href="#"><i class="i-check"></i><span>Обучение</span></a>
                    <a class="nav-link" href="#"><i class="i-check"></i><span>Приложение для удаленного
                            доступа</span></a>
                    <a class="nav-link" href="#"><i class="i-check"></i><span>Страница партнера на сайте
                            Битрикс24</span></a>
                    <a class="nav-link" href="#"><i class="i-check"></i><span>Сертификат партнера Битрикс24</span></a>
                </div>
            </div>
            <div class="col-md-3 footer-contact-col">
                <div class="contact-phones">
                    <a href="tel:+79675556855">
                        <span>+7 (967) 555-68-55</span>
                    </a>
                </div>
                <div class="contact-social">
                    <a class="btn btn-outline-primary" href="https://tg.me/" target="_blank">
                        <i class="i-telegram"></i>
                    </a>
                    <a class="btn btn-outline-primary" href="https://wa.me/" target="_blank">
                        <i class="i-whatsapp"></i>
                    </a><a class="btn btn-outline-primary" href="https://vk.com/" target="_blank">
                        <i class="i-vk"></i>
                    </a>
                    <a class="btn btn-outline-primary" href="https://wa.me/" target="_blank">
                        <i class="i-whatsapp"></i>
                    </a>
                </div>
                <div class="contact-addres">
                    г. Севастополь<br>
                    ул. Хрусталева, д. 74А, оф. 303
                </div>
            </div>
        </div>
        <div class="row row-center">
            <div class="col-12">
                <p><span class="copyright-text">
                        <?= get_theme_mod('footer_copyright_text'); ?>
                    </span> 2021 -
                    <?= date('Y'); ?>гг.
                </p>
                <a class="privacy-link" href="<?= get_privacy_policy_url(); ?>" target="_blank">
                    <?= __('Privacy policy', 'sadovod'); ?>
                </a>
                <p class="disclaimer-note">
                    Внимание! Данный интернет-сайт носит информационный характер и ни при каких условиях не является
                    публичной офертой,
                    которая определяется положениями Статьи 437 (2) Гражданского кодекса РФ.
                    Для получения подробной информации о наличии и стоимости указанных товаров и (или) услуг,
                    пожалуйста, обращайтесь к нашим менеджерам по указанным на сайте контактам.
                </p>
            </div>
        </div>
</footer>

<span itemid="#sadovod" itemscope itemtype="http://schema.org/Organization" hidden>
    <span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
        <meta itemprop="name" content="Лого">
        <link itemprop="contentUrl" href="<?= wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') ?>">
        <meta itemprop="image" content="<?= wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') ?>">
    </span>
    <meta itemprop="image" content="<?= wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full') ?>">
    <span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <meta itemprop="streetAddress" content="Крым, г. Севастополь, ул. Хрусталева, д. 74А, оф. 303">
        <meta itemprop="addressLocality" content="Россия">
        <meta itemprop="addressRegion" content="Крым">
        <meta itemprop="postalCode" content="299029">
        <meta itemprop="addressCountry" content="RU">
    </span>
    <meta itemprop="telephone" content="+79675556855">
    <meta itemprop="email" content="info@sadovod-oleg.ru">
    <meta itemprop="name" content="<?= get_bloginfo('description'); ?>">
    <link itemprop="url" href="<?= get_site_root_domain(); ?>">
</span>

<?php do_action('schema_org'); ?>

<div class="toast-container"></div>
<div class="announcement-container"></div>

<?php do_action('before_modals'); ?>

<?php
/*--------------------------------------------------------------
## Успешная отправка
--------------------------------------------------------------*/
?>

<div id="resultModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-bs-dismiss="modal" aria-label="Close"><i class="i-close"></i></a>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 modal-text">
                    <i class="i-tick-square"></i>
                    <div class="success-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/*--------------------------------------------------------------
## Неуспешная отправка
--------------------------------------------------------------*/
?>

<div id="errorModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-bs-dismiss="modal" aria-label="Close"><i class="i-close"></i></a>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 modal-text">
                    <i class="i-danger-triangle"></i>
                    <div class="error-content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/*--------------------------------------------------------------
## Подтверждение действия
--------------------------------------------------------------*/
?>

<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-bs-dismiss="modal" aria-label="Close"><i class="i-close"></i></a>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 modal-text">
                    <i class="i-danger-circle"></i>
                    <div class="confirmation-content"></div>
                    <div class="alert alert-warning" role="alert">
                        <span class="alert-content"></span>
                    </div>
                    <form class="confirmation-fields"></form>
                    <div class="confirmation-actions"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/*--------------------------------------------------------------
## Обратаня связь
--------------------------------------------------------------*/
$name = '';
$email = '';
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $name = $user->display_name;
    $email = $user->user_email;
}
?>

<div class="modal fade" id="contactForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="twoline-header">
                    <h4 class="modal-title">
                        <?= __('Contact us', 'sadovod'); ?>
                    </h4>
                    <span>
                        <?= __('If you have idea, comentary or remark - send this form.', 'sadovod'); ?>
                    </span>
                    <span>
                        <?=
                                sprintf(
                                    __('Also you can vizit our support forum <a href="%s" target="_blank">%s</a>.', 'sadovod'),
                                    'https://help.sadovod.ru/',
                                    'help.sadovod.ru'
                                );
                                ?>
                    </span>

                </div>
                <a class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="i-close"></i>
                </a>
            </div>
            <div class="modal-body">
                <form class="form-vertical g-recaptcha" method="POST">
                    <input type="hidden" name="action" value="user_message">
                    <?php sadovod_nonce_field('contact-action', 'contact-nonce'); ?>
                    <div class="alert alert-warning" role="alert">
                        <span class="alert-content"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('Name', 'sadovod'); ?>
                        </label>
                        <input class="form-control" type="text" placeholder="<?= __('Enter name', 'sadovod'); ?>"
                            maxlength="100" name="name" value="<?= $name; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('E-mail', 'sadovod'); ?>
                        </label>
                        <input class="form-control email" type="email"
                            placeholder="<?= __('Enter e-mail', 'sadovod'); ?>" maxlength="100" name="email"
                            value="<?= $email; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('Message', 'sadovod'); ?>
                        </label>
                        <textarea class="form-control" rows="5"
                            placeholder="<?= __('Enter your message', 'sadovod'); ?>" maxlength="500" name="message"
                            required></textarea>
                    </div>
                    <div class="form-group file-upload-group">
                        <label>
                            <?= __('Files', 'sadovod'); ?>
                        </label>
                        <div class="form-file-upload">
                            <?php
                            $allowed = array(
                                '.pdf',
                                '.xlsx',
                                '.xls',
                                '.doc',
                                '.docx',
                                '.txt',
                                '.zip',
                                '.rar',
                                'video/*',
                                'image/*',
                                'audio/*'
                            );
                            ?>
                            <input id="contactFiles" class="file-uploader" type="file" name="uploader"
                                accept="<?= implode(',', $allowed); ?>" multiple hidden>
                            <label class="upload-area" for="contactFiles">
                                <?=
                                '<i class="i-upload"></i>' .
                                '<span>' . __('Drop file(s) here', 'sadovod') . '</span>' .
                                '<small>' . __('or', 'sadovod') . '</small>' .
                                '<span>' . __('Click to select file', 'sadovod') . '</span>';
                                ?>
                            </label>
                            <div class="form-attachments-list row row-cols-2 row-cols-sm-5"></div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary-alt btn-block">
                            <?= __('Send', 'sadovod'); ?>
                        </button>
                    </div>
                    <div class="form-group recpatcha-notif">
                        <p>
                            <?= sprintf(
                                __(
                                    'This site is protected by <span>reCAPTCHA</span> and the <span>Google</span>
		                                    <a href="%s" target="_blank">Privacy Policy</a> and
		                                    <a href="%s" target="_blank">Terms of Service</a> apply.',
                                    'sadovod-misc'
                                ),
                                'https://policies.google.com/privacy',
                                'https://policies.google.com/terms'
                            ); ?>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
/*--------------------------------------------------------------
## UniorPay Авторизация/Регистрация/Восстановление пароля
--------------------------------------------------------------*/
?>

<div class="modal fade" id="walletLoginForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <?= __('Login', 'sadovod'); ?>
                </h4>
                <a class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="i-close"></i>
                </a>
            </div>
            <div class="modal-body">
                <form class="form-vertical g-recaptcha" method="POST">
                    <input type="hidden" name="action" value="user_login">
                    <?php sadovod_nonce_field('unior-login-action', 'unior-login-nonce'); ?>
                    <div class="alert alert-warning" role="alert">
                        <span class="alert-content"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('E-mail', 'sadovod'); ?>
                        </label>
                        <input class="form-control" type="text" placeholder="<?= __('Enter e-mail', 'sadovod'); ?>"
                            maxlength="100" name="login" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('Password', 'sadovod'); ?>
                        </label>
                        <div class="input-group input-group-multiline has-validation">
                            <input class="form-control form-floating password" type="password"
                                placeholder="<?= __('Enter password', 'sadovod'); ?>" maxlength="100" name="password"
                                required>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary-alt btn-block">
                            <?= __('Login', 'sadovod'); ?>
                        </button>
                    </div>
                    <div class="form-group recpatcha-notif">
                        <p>
                            <?= sprintf(
                                __(
                                    'This site is protected by <span>reCAPTCHA</span> and the <span>Google</span>
		                                    <a href="%s" target="_blank">Privacy Policy</a> and
		                                    <a href="%s" target="_blank">Terms of Service</a> apply.',
                                    'sadovod-misc'
                                ),
                                'https://policies.google.com/privacy',
                                'https://policies.google.com/terms'
                            ); ?>
                        </p>
                    </div>
                    <div class="form-group d-flex flex-wrap justify-content-between">
                        <a class="btn btn-link btn-sm" data-bs-toggle="modal" data-bs-target="#walletResetPassForm">
                            <?= __('Reset Password', 'sadovod'); ?>
                        </a>
                        <a class="btn btn-link btn-sm" data-bs-toggle="modal" data-bs-target="#walletRegisterForm">
                            <?= __('Register', 'sadovod'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="walletRegisterForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-pre-header">
                <a class="btn btn-link btn-sm" data-bs-toggle="modal" data-bs-target="#walletLoginForm"><i
                        class="i-arrow-left"></i>
                    <?= __('Login', 'sadovod'); ?>
                </a>
            </div>
            <div class="modal-header">
                <h4 class="modal-title">
                    <?= __('Register', 'sadovod'); ?>
                </h4>
                <a class="close" data-bs-dismiss="modal" aria-label="Close"><i class="i-close"></i></a>
            </div>
            <div class="modal-body">
                <form class="form-vertical g-recaptcha" method="POST">
                    <input type="hidden" name="action" value="user_register">
                    <?php sadovod_nonce_field('unior-register-action', 'unior-register-nonce'); ?>
                    <div class="alert alert-warning" role="alert">
                        <span class="alert-content"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('Name', 'sadovod'); ?>
                        </label>
                        <input class="form-control name" type="text" maxlength="100"
                            placeholder="<?= __('Enter name', 'sadovod'); ?>" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('Username', 'sadovod'); ?>
                        </label>
                        <input class="form-control login" type="text" maxlength="100"
                            placeholder="<?= __('Username', 'sadovod'); ?>" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('E-mail', 'sadovod'); ?>
                        </label>
                        <input class="form-control email" type="email"
                            placeholder="<?= __('Enter e-mail', 'sadovod'); ?>" maxlength="40" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('Password', 'sadovod'); ?>
                        </label>
                        <div class="input-group input-group-multiline has-validation">
                            <input class="form-control form-floating password password-strength" type="password"
                                placeholder="<?= __('Enter password', 'sadovod'); ?>" minlength="6" maxlength="100"
                                name="password" required>
                        </div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" required name="checkbox" class="checkbox form-check-input">
                        <span class="checkbox-text">
                            <?php
                                    printf(
                                        __('By submitting this form you agree to our <a href="%s" target="_blank">privacy policy</a>.', 'sadovod'),
                                        get_privacy_policy_url()
                                    );
                                    ?>
                        </span>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary-alt btn-block">
                            <?= __('Register', 'sadovod'); ?>
                        </button>
                    </div>
                    <div class="form-group recpatcha-notif">
                        <p>
                            <?= sprintf(
                                __(
                                    'This site is protected by <span>reCAPTCHA</span> and the <span>Google</span>
		                                    <a href="%s" target="_blank">Privacy Policy</a> and
		                                    <a href="%s" target="_blank">Terms of Service</a> apply.',
                                    'sadovod-misc'
                                ),
                                'https://policies.google.com/privacy',
                                'https://policies.google.com/terms'
                            ); ?>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="walletResetPassForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-pre-header">
                <a class="btn btn-link btn-sm" data-bs-toggle="modal" data-bs-target="#walletLoginForm"><i
                        class="i-arrow-left"></i>
                    <?= __('Login', 'sadovod'); ?>
                </a>
            </div>
            <div class="modal-header">
                <h4 class="modal-title">
                    <?= __('Reset Password', 'sadovod'); ?>
                </h4>
                <a class="close" data-bs-dismiss="modal" aria-label="Close"><i class="i-close"></i></a>
            </div>
            <div class="modal-body">
                <form class="form-vertical g-recaptcha" method="POST">
                    <input type="hidden" name="action" value="user_password_reset">
                    <?php sadovod_nonce_field('unior-reset-password-action', 'unior-reset-password-nonce'); ?>
                    <div class="alert alert-warning" role="alert">
                        <span class="alert-content"></span>
                    </div>
                    <div class="form-group">
                        <label>
                            <?= __('Username', 'sadovod'); ?>
                        </label>
                        <input class="form-control" type="text" placeholder="<?= __('Enter username', 'sadovod'); ?>"
                            maxlength="100" name="login" required>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary-alt btn-block">
                            <?= __('Proceed', 'sadovod'); ?>
                        </button>
                    </div>
                    <div class="form-group recpatcha-notif">
                        <p>
                            <?= sprintf(
                                __(
                                    'This site is protected by <span>reCAPTCHA</span> and the <span>Google</span>
		                                    <a href="%s" target="_blank">Privacy Policy</a> and
		                                    <a href="%s" target="_blank">Terms of Service</a> apply.',
                                    'sadovod-misc'
                                ),
                                'https://policies.google.com/privacy',
                                'https://policies.google.com/terms'
                            ); ?>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php
/*--------------------------------------------------------------
## Создание встречи
--------------------------------------------------------------*/
?>

<div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <?= __('Create new room', 'sadovod'); ?>
                </h4>
                <a class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="i-close"></i>
                </a>
            </div>
            <div class="modal-body">
                <form class="form-vertical" method="POST">
                    <input type="hidden" name="action" value="room_add">
                    <?php sadovod_nonce_field('room-add-action', 'room-add-nonce'); ?>
                    <div class="alert alert-warning" role="alert">
                        <span class="alert-content"></span>
                    </div>
                    <h5>
                        <?= __('Select room type', 'sadovod'); ?>
                    </h5>
                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                        <input id="private-room-radio" type="radio" class="btn-check" name="room_type"
                            autocomplete="off" value="0" checked>
                        <label class="btn btn-outline-primary" for="private-room-radio">
                            <?= __('Private Room', 'sadovod'); ?>
                        </label>
                        <input id="public-room-radio" type="radio" class="btn-check" name="room_type" autocomplete="off"
                            value="1">
                        <label class="btn btn-outline-primary" for="public-room-radio">
                            <?= __('Public Room', 'sadovod'); ?>
                        </label>
                    </div>
                    <div class="room-data-wrap">
                        <h5>
                            <?= __('Set room password', 'sadovod'); ?>
                        </h5>
                        <div class="channel-info-fields">
                            <input class="form-control" type="text"
                                placeholder="<?= __('Enter password', 'sadovod'); ?>" maxlength="255"
                                name="room_password">
                        </div>
                    </div>
                    <h6>В случае если встреча приватная нужно выводить поле ввести пароль</h6>
                    <h6>После отправки должно отображаться ссылка на приглашение в модаке resultModal</h6>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary-alt btn-block">
                            <?= __('Create', 'sadovod'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
/*--------------------------------------------------------------
## Кнопки управления
--------------------------------------------------------------*/
?>

<div id="contact-us" title="<?= __('Contact us', 'sadovod'); ?>" data-bs-activate="modal" data-bs-target="#contactForm">
</div>

<?php if (is_user_logged_in()) : ?>
<div id="create-btn" title="<?= __('Create new record', 'sadovod'); ?>" data-bs-toggle="modal"
    data-bs-target="#createModal"></div>
<div id="message-btn" title="<?= __('Show messenger', 'sadovod'); ?>" data-bs-toggle="modal"
    data-bs-target="#messageModal"></div>
<?php endif; ?>

<div id="arrow-up" title="<?= __('Scroll to top', 'sadovod'); ?>"></div>
<div id="dark-theme" title="<?= __('Toggle theme color', 'sadovod'); ?>"></div>

<?php
/*--------------------------------------------------------------
## Галерея
--------------------------------------------------------------*/
?>

<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
    <div class="slides"></div>
    <div class="title"></div>
    <a class="prev"><i class="i-arrow-left"></i></a>
    <a class="next"><i class="i-arrow-right"></i></a>
    <a class="close"><i class="i-close"></i></a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>

<?php
/*--------------------------------------------------------------
## Counter
--------------------------------------------------------------*/
?>

<noscript>
    <div><img src="https://mc.yandex.ru/watch/<?= get_theme_mod('metrika_id', '') ?>"
            style="position:absolute; left:-9999px;" alt="" /></div>
</noscript>

<?php wp_footer(); ?>

</body>

</html>