<?php

// когда пользователь сам редактирует свой профиль
add_action( 'show_user_profile', 'true_show_profile_fields' );

// когда чей-то профиль редактируется админом
add_action( 'edit_user_profile', 'true_show_profile_fields' );

function true_show_profile_fields( $user ) {

    echo '<h1 style="color:red">Дополнительные данные пользователя</h1>';
    echo '<table class="form-table" role="presentation">';

    // добавляем поле Фамилия
    $last_name = get_the_author_meta( 'last_name', $user->ID );
    echo '<tr><th><label for="last_name">Фамилия</label></th>
 	<td><input type="text" name="last_name" id="last_name" value="' . esc_attr($last_name) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Имя
    $first_name = get_the_author_meta( 'first_name', $user->ID );
    echo '<tr><th><label for="first_name">Имя</label></th>
 	<td><input type="text" name="first_name" id="first_name" value="' . esc_attr($first_name) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Отчество
    $patronymic = get_the_author_meta( 'patronymic', $user->ID );
    echo '<tr><th><label for="patronymic">Отчество</label></th>
 	<td><input type="text" name="patronymic" id="patronymic" value="' . esc_attr($patronymic) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Телефон
    $tel = get_the_author_meta( 'tel', $user->ID );
    echo '<tr><th><label for="tel">Телефон</label></th>
 	<td><input type="text" name="tel" id="tel" value="' . esc_attr($tel) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Email
    $email = get_the_author_meta( 'email', $user->ID );
    echo '<tr><th><label for="email">Email</label></th>
 	<td><input type="text" name="email" id="email" value="' . esc_attr($email) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Название СНТ
    $namesnt = get_the_author_meta( 'namesnt', $user->ID );
    echo '<tr><th><label for="namesnt">Название СНТ</label></th>
 	<td><input type="text" name="namesnt" id="snt_name" value="' . esc_attr($namesnt) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Квдвстровый номер
    $cadastral_num = get_the_author_meta( 'cadastral_num', $user->ID );
    echo '<tr><th><label for="cadastral_num">Кадастровый номер</label></th>
 	<td><input type="text" name="cadastral_num" id="cadastral_num" value="' . esc_attr($cadastral_num) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Адрес
    $address = get_the_author_meta( 'address', $user->ID );
    echo '<tr><th><label for="address">Адрес</label></th>
 	<td><input type="text" name="address" id="address" value="' . esc_attr($address) . '" class="regular-text" /></td>
	</tr>';

    echo '</table>';

}

// когда пользователь сам редактирует свой профиль
add_action( 'personal_options_update', 'true_save_profile_fields' );
// когда чей-то профиль редактируется админом например
add_action( 'edit_user_profile_update', 'true_save_profile_fields' );

function true_save_profile_fields( $user_id ) {

    update_user_meta( $user_id, 'last_name', sanitize_text_field( $_POST[ 'last_name' ] ) );
    update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST[ 'first_name' ] ) );
    update_user_meta( $user_id, 'patronymic', sanitize_text_field( $_POST[ 'patronymic' ] ) );
    update_user_meta( $user_id, 'tel', sanitize_text_field( $_POST[ 'tel' ] ) );
    update_user_meta( $user_id, 'email', sanitize_text_field( $_POST[ 'email' ] ) );
    update_user_meta( $user_id, 'namesnt', sanitize_text_field( $_POST[ 'namesnt' ] ) );
    update_user_meta( $user_id, 'cadastral_num', sanitize_text_field( $_POST[ 'cadastral_num' ] ) );
    update_user_meta( $user_id, 'address', sanitize_text_field( $_POST[ 'address' ] ) );
}