<?php

/*
 Template Name: Login
*/

if ($_POST) {

    global $wpdb;

    //Проверяем все поля ввода перед запросом SQL
    $username = $wpdb->_escape($_REQUEST['username']);
    $password = $wpdb->_escape($_REQUEST['password']);
    if (!empty($_REQUEST['rememberme'])) {
        $remember = $wpdb->_escape($_REQUEST['rememberme']);
    } else {
        $remember = true;
    }


    if ($remember) $remember = "true";
    else $remember = "false";

    $login_data = array();
    $login_data['user_login'] = $username;
    $login_data['user_password'] = $password;
    $login_data['remember'] = $remember;

    $user_verify = wp_signon($login_data, false);

    //Передаем параметр error для использования его потом в скрипте
    if (is_wp_error($user_verify)) {

        header("Location: " . home_url() . "/login?error=true");
    } else {
        echo "<script>window.location='" . home_url() . "'</script>";
        exit();
    }
}
get_header();
?>
<main class="logon">
    <div class="container mx-auto px-4 pt-[60px] pb-[60px]">
        <h1 class="logon__title">Войдите свой личный кабинет</h1>
        <div class="mt-[20px] text-[22px] logon__sub-title">для получения доступа к закрытой информации товарищества</div>
        <div class="text-[#191919]/[.5] mt-[60px] logon__desc">Садоводы, имеющие свой личный кабинет могут видеть свой баланс по электроэнергии и взносам, могут скачивать квитанции или производить оплату онлайн. Также внутри личного кабинета можно обмениваться документами с председателем и голосовать в созданных председателем опросах.</div>

        <div class="mt-[60px] bg-[#FFF] p-[30px] inline-block w-[50%] rounded-[10px]">
            <div class="text-[35px] font-[700]">Вход</div>
            <form id="login" name="form" action="<?php echo home_url(); ?>/login/" method="post" class="mt-[30px] grid">
                <label for="">
                    <p class="text-[15px]">Email*</p>
                    <input id="username" name="username" type="text" placeholder="Логин" >
                </label>
                <label for="" class="mt-[20px]">
                    <p class="text-[15px]">Пароль*</p>
                    <input id="password" name="password" type="password" placeholder="Пароль" >
                </label>
                <div class="flex justify-between items-center mt-[30px]">
                    <input id="submit" type="submit" name="submit" value="Войти">
                    <div class="grid logon__btn">
                        <a href="/lost-password">Забыли пароль</a>
                        <a href="/register">Регистрация</a>
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
<?php
get_footer();
?>