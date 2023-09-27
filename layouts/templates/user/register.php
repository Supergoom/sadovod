<?php
/*
Template Name: Register
*/

//require_once(ABSPATH . WPINC . '/registration.php');

global $wpdb, $user_ID;
//Проверяем, вошел ли уже пользователь в систему
if ($user_ID) {

    //Залогиненного пользователя перенаправляем на главную страницу.
    header('Location:' . home_url());
} else {
    $errors = [];

    if ($_POST) {

        //        //Убедитесь, что имя пользователя присутствует и еще не используется
        //        $username = $wpdb->_escape($_REQUEST['username']);
        //        if (strpos($username, ' ') !== false) {
        //            $errors['username'] = "Извините, в именах пользователей нельзя использовать пробелы";
        //        }
        //        //если поле с именем пользователя пустое
        //        if (empty($username)) {
        //            $errors['username'] = "Пожалуйста введите имя пользователя";
        //        } elseif (username_exists($username)) {
        //            //если такой пользователь уже зарегистрирован
        //            $errors['username'] = "Имя пользователя уже существует, попробуйте другое";
        //        }
        //
        //        // Проверяем, есть ли email и действителен ли он
        //        $email = $wpdb->_escape($_REQUEST['email']);
        //        if (!is_email($email)) {
        //            $errors['email'] = "Пожалуйста, введите действительный email";
        //        } elseif (email_exists($email)) {
        //            $errors['email'] = "Такой email уже зарегистрирован";
        //        }
        //
        //        // Проверка пароля на валидность
        //        if (0 === preg_match("/.{6,}/", $_POST['password'])) {
        //            $errors['password'] = "Пароль должен состоять не менее, чем из шести символов.";
        //        }
        //
        //        // Проверка повторного ввода пароля
        //        if (0 !== strcmp($_POST['password'], $_POST['password_confirmation'])) {
        //            $errors['password_confirmation'] = "Пароли не совпадают";
        //        }
        //
        //        // Проверить согласие с условиями обслуживания
        //        if ($_POST['terms'] != "Yes") {
        //            $errors['terms'] = "Вы должны согласиться с Условиями использования";
        //        }
        // если ошибок нет
        var_dump($_REQUEST);
        if (0 === count($errors)) {

            $password = $_POST['password'];

            $new_user_id = wp_create_user($username, $password, $email);

            // Здесь вы можете делать все, что угодно, например, отправлять электронное письмо пользователю и т. д.

            $success = 1;

            header('Location:' . get_bloginfo('url') . '/login/?success=1&u=' . $username);
        } else {
            $message = 'Есть ошибки в заполнении формы';
        }
    }
}
?>

<?php get_header(); ?>
<main class="logon">
    <div class="container mx-auto px-4 pt-[60px] pb-[60px]">
        <h1 class="logon__title">Создайте свой личный кабинет</h1>
        <div class="mt-[20px] text-[22px] logon__sub-title">для получения доступа к закрытой информации
            товарищества
        </div>
        <div class="text-[#191919]/[.5] mt-[60px] logon__desc">Садоводы, имеющие свой личный кабинет могут видеть
            свой баланс по электроэнергии и взносам, могут скачивать квитанции или производить оплату онлайн. Также
            внутри личного кабинета можно обмениваться документами с председателем и голосовать в созданных
            председателем опросах.
        </div>
        <div class="mt-[60px] bg-[#FFF] p-[30px] inline-block w-[50%] rounded-[10px]">
            <div class="text-[35px] font-[700]">Регистрация</div>

            <div class="register-tabs-wrapper">
                <div class="tabs flex gap-[40px] mt-[40px] mb-[40px]">
                    <div class="register-input-rol active">Собственник</div>
                    <div class="register-input-rol">Не являюсь собственником</div>
                </div>
                <div class="tabs-content">
                    <div class="form-tab active"><?php echo do_shortcode('[contact-form-7 id="4b41cf2" title="Запрос на регистрацию пользователя"]'); ?></div>
                    <div class="form-tab">Форма для обывателя</div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
get_footer();
?>