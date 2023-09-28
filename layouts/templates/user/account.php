<?php
/*
 Template Name: Account
*/

get_header();

global $user_ID;

// если пользователь не авторизован, отправляем его на страницу входа
if( !$user_ID ) {
    header('location:' . site_url() . '/login');
    exit;
} else {
    $userdata = get_user_by( 'id', $user_ID );
}

if( isset($_GET['status']) ) :
    switch( $_GET['status'] ) :
        case 'ok':{
            echo '<div class="success">Сохранено.</div>';
            break;
        }
        case 'exist':{
            echo '<div class="error">Пользователь с указанным email уже существует.</div>';
            break;
        }
        case 'short':{
            echo '<div class="error">Пароль слишком короткий.</div>';
            break;
        }
        case 'mismatch':{
            echo '<div class="error">Пароли не совпадают.</div>';
            break;
        }
        case 'wrong':{
            echo '<div class="error">Старый пароль неверен.</div>';
            break;
        }
        case 'required':{
            echo '<div class="error">Пожалуйста, заполните все обязательные поля.</div>';
            break;
        }
    endswitch;
endif;

// profile-update.php - обрабатывает сохранение
?>
<form action="<?php echo get_stylesheet_directory_uri() ?>/profile-update.php" method="POST">

    <input type="text" name="last_name" placeholder="Фамилия" value="<?php echo $userdata->last_name ?>" /><br>
    <input type="text" name="first_name" placeholder="Имя" value="<?php echo $userdata->first_name ?>" /><br>
    <input type="text" name="patronymic" placeholder="Отчество" value="<?php echo get_user_meta($user_ID, 'patronymic', true ); ?>" /><br>
    <input type="text" name="tel" placeholder="Телефон" value="<?php echo get_user_meta($user_ID, 'tel', true ) ?>" /><br>
    <input type="email" name="email" placeholder="Email" value="<?php echo $userdata->user_email ?>" /><br>

    <?php if (get_user_meta($user_ID, 'namesnt', true )) ?>
        <input type="text" name="namesnt" placeholder="Название СНТ" value="<?php echo get_user_meta($user_ID, 'namesnt', true ) ?>" /><br>
    <?php if (get_user_meta($user_ID, 'cadastral_num', true )) ?>
        <input type="text" name="cadastral_num" placeholder="Кадастровый номер" value="<?php echo get_user_meta($user_ID, 'cadastral_num', true ) ?>" /><br>
    <?php if (get_user_meta($user_ID, 'address', true )) ?>
        <input type="text" name="address" placeholder="Адрес" value="<?php echo get_user_meta($user_ID, 'address', true ) ?>" /><br>


    <input type="password" name="pwd1" placeholder="Старый пароль" /><br>
    <input type="password" name="pwd2" placeholder="Новый пароль" /><br>
    <input type="password" name="pwd3" placeholder="Повторите новый пароль" /><br>

    <button>Сохранить</button>
</form>

    <br><br><a href="<?php echo wp_logout_url( home_url() ); ?>" title="Выход">Выход</a>
<?php get_footer(); ?>