<?php

/*  Пагинация
-----------------------------------------------*/

function pagenumber()
{
    $paged = absint(get_query_var('paged', 1));
    if (($paged ? $paged : 1) > 1)
        echo '<small class="page-number">' . sprintf(__('Page №%d', 'oneunion-records'), $paged) . '</small>';
}

if (!function_exists('pagination')) { // если ф-я уже есть в дочерней теме - нам не надо её определять
    function pagination()
    { // функция вывода пагинации

        // get_total
        global $wp_query;
        $total = isset($wp_query->max_num_pages) ? $wp_query->max_num_pages : 1;

        global $user_query;
        global $post_query;
        if (!$total and $user_query)
            $total = ceil($user_query->get_total() / $user_query->get('number'));
        elseif (!$total and $post_query)
            $total = $post_query->max_num_pages;

        $a['total'] = $total;
        $a['mid_size'] = 3; // сколько ссылок показывать слева и справа от текущей
        $a['end_size'] = 1; // сколько ссылок показывать в начале и в конце
        $a['prev_text'] = '<i class="i-arrow-left"></i>'; // текст ссылки "Предыдущая страница"
        $a['next_text'] = '<i class="i-arrow-right"></i>'; // текст ссылки "Следующая страница"

        if ($total > 1) echo '<nav class="pagination">';
        echo paginate_links($a);
        if ($total > 1) echo '</nav>';
    }
}
