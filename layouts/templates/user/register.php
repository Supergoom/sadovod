<?php
/*
Template Name: Register
*/

global $wpdb, $user_ID;

//Проверяем, вошел ли уже пользователь в систему
if ($user_ID) {

    //Залогиненного пользователя перенаправляем на главную страницу.
    header('Location:' . home_url());
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
                    <div class="form-tab active"><?php echo do_shortcode('[contact-form-7 id="4b41cf2" title="Запрос на регистрацию собственника"]'); ?></div>
                    <div class="form-tab"><?php echo do_shortcode('[contact-form-7 id="57f201d" title="Запрос на регистрацию не собственника"]'); ?></div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
?>