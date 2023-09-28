<?php

//add_action( 'admin_menu', 'top_menu_page', 25 );
//
//function top_menu_page(){
//
//    add_menu_page(
//        'Добавить СНТ', // тайтл страницы
//        'Добавить СНТ', // текст ссылки в меню
//        'manage_options', // права пользователя, необходимые для доступа к странице
//        'gis_snt', // ярлык страницы
//        'gis_snt_page_callback', // функция, которая выводит содержимое страницы
//        'data:image/svg+xml;base64,' . base64_encode('<svg width="1792" height="1792" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 640q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm256 0q0 109-33 179l-364 774q-16 33-47.5 52t-67.5 19-67.5-19-46.5-52l-365-774q-33-70-33-179 0-212 150-362t362-150 362 150 150 362z" fill="#fff"/></svg>'),
//        20 // позиция в меню
//    );
//}
//
//function gis_snt_page_callback(){
//
//    global $wpdb;
//
//    echo '<h1 style="">Выберите   Город -> Муниципалитет <br><br> после можете добавить СНТ</h1>';
//
//    // добавляем поле Город
//
//    $city = $wpdb->get_results("SELECT * FROM SAD_cities");
//
//    echo '<tr><th><label for="city">Город</label></th>
// 	<td><input type="text" name="city" id="city" value="' . esc_attr($city) . '" class="regular-text" /></td>
//	</tr>';
//
//    // добавляем поле Муниципалитет
//    $municipality = $wpdb->get_results("SELECT * FROM SAD_municipality");
//    echo '<tr><th><label for="municipality">Муниципалитет</label></th>
// 	<td><input type="text" name="municipality" id="municipality" value="' . esc_attr($municipality) . '" class="regular-text" /></td>
//	</tr>';
//
//}