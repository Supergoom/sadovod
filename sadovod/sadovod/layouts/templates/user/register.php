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
    $errors = array();

    if ($_POST) {

        //Убедитесь, что имя пользователя присутствует и еще не используется
        $username = $wpdb->escape($_REQUEST['username']);
        if (strpos($username, ' ') !== false) {
            $errors['username'] = "Извините, в именах пользователей нельзя использовать пробелы";
        }
        //если поле с именем пользователя пустое
        if (empty($username)) {
            $errors['username'] = "Пожалуйста введите имя пользователя";
        } elseif (username_exists($username)) {
            //если такой пользователь уже зарегистрирован
            $errors['username'] = "Имя пользователя уже существует, попробуйте другое";
        }

        // Проверяем, есть ли email и действителен ли он
        $email = $wpdb->escape($_REQUEST['email']);
        if (!is_email($email)) {
            $errors['email'] = "Пожалуйста, введите действительный email";
        } elseif (email_exists($email)) {
            $errors['email'] = "Такой email уже зарегистрирован";
        }

        // Проверка пароля на валидность
        if (0 === preg_match("/.{6,}/", $_POST['password'])) {
            $errors['password'] = "Пароль должен состоять не менее, чем из шести символов.";
        }

        // Проверка повторного ввода пароля
        if (0 !== strcmp($_POST['password'], $_POST['password_confirmation'])) {
            $errors['password_confirmation'] = "Пароли не совпадают";
        }

        // Проверить согласие с условиями обслуживания
        if ($_POST['terms'] != "Yes") {
            $errors['terms'] = "Вы должны согласиться с Условиями использования";
        }
        // если ошибок нет
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
        <div class="mt-[20px] text-[22px] logon__sub-title">для получения доступа к закрытой информации товарищества</div>
        <div class="text-[#191919]/[.5] mt-[60px] logon__desc">Садоводы, имеющие свой личный кабинет могут видеть свой баланс по электроэнергии и взносам, могут скачивать квитанции или производить оплату онлайн. Также внутри личного кабинета можно обмениваться документами с председателем и голосовать в созданных председателем опросах.</div>

        <div class="mt-[60px] bg-[#FFF] p-[30px] inline-block w-[50%] rounded-[10px]">
            <div class="text-[35px] font-[700]">Регистрация</div>
            <form id="wp_signup_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="mt-[30px] grid">
                <label for="">
                    <p class="text-[15px]">Фио*</p>
                    <input type="text" name="username" id="username" placeholder="Иванов Иван Иванович">
                    <span class="error"><?= isset($errors['username']) ? $errors['username']  : '' ?></span>
                </label>
                <label for="" class="mt-[20px]">
                    <p class="text-[15px]">Email*</p>
                    <input type="text" name="email" id="email" placeholder="ivan@mail.ru">
                    <span class="error"><?= isset($errors['email']) ? $errors['email']  : '' ?></span>
                </label>
                <label for="" class="mt-[20px]">
                    <p class="text-[15px]">Телефон*</p>
                    <input type="text" name="telephone" id="telephone" placeholder="+7 (___) ___ __ __">
                    <span class="error"><?= isset($errors['telephone']) ? $errors['telephone']  : '' ?></span>
                </label>
                <label for="" class="mt-[20px]">
                    <p class="text-[15px]">Название СНТ*</p>
                    <input type="text" name="snt" id="snt" placeholder="Садовод">
                    <span class="error"><?= isset($errors['snt']) ? $errors['snt']  : '' ?></span>
                </label>
                <label for="" class="mt-[20px]">
                    <p class="text-[15px]">Пароль*</p>
                    <input type="text" name="password" id="password" placeholder="**********">
                    <span class="error"><?= isset($errors['password']) ? $errors['password']  : '' ?></span>
                </label>
                <label for="" class="mt-[20px]">
                    <p class="text-[15px]">Повторите пароль*</p>
                    <input type="text" name="password_confirmation" id="password_confirmation" placeholder="**********">
                    <span class="error"><?= isset($errors['password_confirmation']) ? $errors['password_confirmation']  : '' ?></span>
                </label>
                <div class="flex justify-between items-center mt-[30px]">
                    <input id="submit" type="submit" name="submit" value="Зарегистрироваться">
                    <div class="grid logon__btn">
                        <a href="/logo">Войти</a>
                    </div>
                </div>

            </form>
        </div>
        <script>
            let message = document.getElementById('message');
            if (location.search.indexOf('error') > -1) {
                message.innerHTML = 'Неверные учетные данные';
                message.innerHTML += '<br>Введите заново или перейдите на страницу <a href="<?php echo home_url(); ?>/register">регистрации</a>';
            }
        </script>
    </div>
</main>
<!-- <p id="message"><?= isset($message) ? $message  : '' ?></p>
    <form id="wp_signup_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" style="max-width: 500px">

        <p>
            <label for="username">Имя пользователя</label>
            <input type="text" name="username" id="username" value="<?= isset($_REQUEST['username']) ? $_REQUEST['username']  : '' ?>">
            <span class="error"><?= isset($errors['username']) ? $errors['username']  : '' ?></span>
        </p>
        <p>
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="<?= isset($_REQUEST['email']) ? $_REQUEST['email']  : '' ?>">
            <span class="error"><?= isset($errors['email']) ? $errors['email']  : '' ?></span>
        </p>
        <p>
            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" value="<?= isset($_REQUEST['password']) ? $_REQUEST['password']  : '' ?>">
            <span class="error"><?= isset($errors['password']) ? $errors['password']  : '' ?></span>
        </p>
        <p>
            <label for="password_confirmation">Повторите пароль</label>
            <input type="password" name="password_confirmation" id="password_confirmation" value="<?= isset($_REQUEST['password_confirmation']) ? $_REQUEST['password_confirmation']  : '' ?>">
            <span class="error"><?= isset($errors['password_confirmation']) ? $errors['password_confirmation']  : '' ?></span>
        </p>

        <p> <input name="terms" id="terms" type="checkbox" value="Yes">
            <label for="terms">Я согласен(-на) с условиями предоставления услуг</label><br>
            <span class="error"><?= isset($errors['terms']) ? $errors['terms']  : '' ?></span>
        </p>
        <input type="submit" id="submitbtn" name="submit" value="Зарегистрироваться" />

    </form> -->

<?php
get_footer();
?>