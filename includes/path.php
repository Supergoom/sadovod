<?php

/*  Крошки
-----------------------------------------------*/

function outputBreadcrumbs()
{
    $crumbs = new Breadcrumbs();
    echo $crumbs->get_crumbs(array(), array());
}

class Breadcrumbs
{

    public $arg;
    static $l10n = array();
    static $args = array();

    function __construct()
    {
        self::$l10n = array(
            'home'       => __('Home', 'sadovod-dialogs'),
            'paged'      => __('Page %d', 'sadovod-dialogs'),
            '_404'       => __('Error 404', 'sadovod-dialogs'),
            'search'     => __('Search results for query: <b>%s</b>', 'sadovod-dialogs'),
            'author'     => __('Users', 'sadovod-dialogs'),
            'year'       => __('Records by <b>%d</b> year', 'sadovod-dialogs'),
            'month'      => __('Records by <b>%s</b>', 'sadovod-dialogs'),
            'attachment' => __('Media %s', 'sadovod-dialogs'),
            'theme'      => __('Theme', 'sadovod-dialogs'),
            'cat'        => __('Category', 'sadovod-dialogs'),
            'tag'        => __('Tag', 'sadovod-dialogs'),
            'tax_tag'    => __('%1$s from "%2$s" by tag <b>%3$s</b>', 'sadovod-dialogs'),
            // tax_tag выведет: 'тип_записи из "название_таксы" по тегу: имя_термина'.
            // Если нужны отдельные холдеры, например только имя термина, пишем так: 'записи по тегу: %3$s'
        );

        // Параметры по умолчанию
        self::$args = array(
            'on_front_page'      => true,  // выводить крошки на главной странице
            'show_home_link'     => true,  // выводить ссылку на главную
            'show_cat_link'      => true,  // выводить ссылку на категорию
            'show_tax_link'      => true,  // выводить ссылку на тахономию
            'show_post_title'    => true,  // показывать ли название записи в конце (последний элемент). Для записей, страниц, вложений
            'show_term_title'    => true,  // показывать ли название элемента таксономии в конце (последний элемент). Для меток, рубрик и других такс
            'last_sep'           => false,  // показывать последний разделитель, когда заголовок в конце не отображается
            'markup'             => 'schema.org', // 'markup' - микроразметка. Может быть: 'rdf.data-vocabulary.org', 'schema.org', '' - без микроразметки
            // или можно указать свой массив разметки:
            // array( 'wrappatt'=>'<div class="Breadcrumbs">%s</div>', 'linkpatt'=>'<a href="%s">%s</a>', 'sep_after'=>'', )
            'priority_tax'    => array('category'), // приоритетные таксономии, нужно когда запись в нескольких таксах
            'priority_terms'  => array(), // 'priority_terms' - приоритетные элементы таксономий, когда запись находится в нескольких элементах одной таксы одновременно.
            // Например: array( 'category'=>array(45,'term_name'), 'tax_name'=>array(1,2,'name') )
            // 'category' - такса для которой указываются приор. элементы: 45 - ID термина и 'term_name' - ярлык.
            // порядок 45 и 'term_name' имеет значение: чем раньше тем важнее. Все указанные термины важнее неуказанных...
            'nofollow' => false, // добавлять rel=nofollow к ссылкам?

            // служебные
            'linkpatt'        => '',
            'pg_end'          => '',
        );
    }

    function get_crumbs($l10n = array(), $args = array())
    {
        global $post, $wp_query, $wp_post_types;

        // Фильтрует дефолты и сливает
        $loc = (object) array_merge(apply_filters('breadcrumbs_default_loc', self::$l10n), $l10n);
        $arg = (object) array_merge(apply_filters('breadcrumbs_default_args', self::$args), $args);

        // упростим
        $this->arg = &$arg;
        $this->arg->cur_pos = 20;

        // микроразметка ---
        if (1) {
            $mark = array(
                'wrappatt'   => '<ul class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">%s</ul>',
                'linkpatt'   => '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">' .
                    '<a id="breadcrumb_%2$d" itemprop="item" href="%1$s"> ' .
                    '<link itemprop="url" href="%1$s">' .
                    '<meta itemprop="position" content="%2$d">' .
                    '<span itemprop="name">%3$s</span>' .
                    '</a>' .
                    '</li>',
            );

            $wrappatt  = $mark['wrappatt'];
            $arg->linkpatt  = $arg->nofollow ? str_replace('<a ', '<a rel="nofollow"', $mark['linkpatt']) : $mark['linkpatt'];
        }

        $linkpatt = $arg->linkpatt; // упростим

        $q_obj = get_queried_object();

        // может это архив пустой таксы?
        $ptype = null;
        if (empty($post)) {
            if (isset($q_obj->taxonomy))
                $ptype = &$wp_post_types[get_taxonomy($q_obj->taxonomy)->object_type[0]];
        } else $ptype = &$wp_post_types[$post->post_type];

        // paged
        $arg->pg_end = '';
        if (($paged_num = get_query_var('paged')) || ($paged_num = get_query_var('page'))) {
            $title = sprintf($loc->paged, (int) $paged_num);
            $link = get_permalink($post);
            $arg->pg_end = sprintf($arg->linkpatt, $link, 49, $title);
        }

        $pg_end = $arg->pg_end; // упростим
        $out = '';
        if (is_front_page()) {
            return $arg->on_front_page ? sprintf($wrappatt, ($paged_num ? sprintf($linkpatt, get_home_url(), ($this->arg->cur_pos--), $loc->home) . $pg_end : $loc->home)) : '';
        }
        // страница записей, когда для главной установлена отдельная страница.
        elseif (is_home()) {
            if ($arg->show_post_title) {
                $out = sprintf($linkpatt, get_permalink($q_obj), ($this->arg->cur_pos--), esc_html($q_obj->post_title));
            }
        } elseif (is_search()) {
            $out = sprintf($loc->search, esc_html($GLOBALS['s']));
            $out = sprintf($linkpatt, get_home_url(null, '?s=' . $GLOBALS['s']), ($this->arg->cur_pos--), $out);
        } elseif (is_author()) {
            $tmp_post = get_page_by_path('users');
            $out = sprintf($linkpatt, get_home_url(null, $tmp_post->post_name, 'https'), ($this->arg->cur_pos--), $tmp_post->post_title);

            $out .= sprintf($linkpatt, get_author_posts_url($q_obj->ID, $q_obj->user_nicename), ($this->arg->cur_pos--), $q_obj->display_name) . $pg_end;
        } elseif (is_year() || is_month() || is_day()) {
            $y_url  = get_year_link($year = get_the_time('Y'));

            if (is_year()) {
                $tit = sprintf($loc->year, $year);
                $out = ($paged_num ? sprintf($linkpatt, $y_url, ($this->arg->cur_pos--), $tit) . $pg_end : $tit);
            }
            // month day
            else {
                $y_link = sprintf($linkpatt, $y_url, ($this->arg->cur_pos--), $year);
                $m_url  = get_month_link($year, get_the_time('m'));

                if (is_month()) {
                    $tit = sprintf($loc->month, get_the_time('F'));
                    $out = $y_link . ($paged_num ? sprintf($linkpatt, $m_url, ($this->arg->cur_pos--), $tit) . $pg_end : $tit);
                } elseif (is_day()) {
                    $m_link = sprintf($linkpatt, $m_url, ($this->arg->cur_pos--), get_the_time('F'));
                    $out = $y_link . $m_link . get_the_time('l');
                }
            }
        }
        // Древовидные записи
        elseif (is_singular() && $ptype->hierarchical) {
            $out = $this->_add_title($this->_page_crumbs($post), $post);
        }
        // Таксы, плоские записи и вложения
        else {
            $term = $q_obj; // таксономии

            // определяем термин для записей (включая вложения attachments)
            if (is_singular()) {
                // изменим $post, чтобы определить термин родителя вложения
                if (is_attachment() && $post->post_parent) {
                    $save_post = $post; // сохраним
                    $post = get_post($post->post_parent);
                }

                // учитывает если вложения прикрепляются к таксам древовидным - все бывает :)
                $taxonomies = get_object_taxonomies($post->post_type);
                // оставим только древовидные и публичные, мало ли...
                $taxonomies = array_intersect($taxonomies, get_taxonomies(array('public' => true)));

                if ($taxonomies) {
                    // сортируем по приоритету
                    if (!empty($arg->priority_tax)) {
                        usort($taxonomies, function ($a, $b) use ($arg) {
                            $a_index = array_search($a, $arg->priority_tax);
                            if ($a_index === false) $a_index = 9999999;

                            $b_index = array_search($b, $arg->priority_tax);
                            if ($b_index === false) $b_index = 9999999;

                            return ($b_index === $a_index) ? 0 : ($b_index < $a_index ? 1 : -1); // меньше индекс - выше
                        });
                    }
                    // пробуем получить термины, в порядке приоритета такс
                    foreach ($taxonomies as $taxname) {
                        if ($terms = get_the_terms($post->ID, $taxname)) {
                            // проверим приоритетные термины для таксы
                            $prior_terms = &$arg->priority_terms[$taxname];
                            if ($prior_terms && count($terms) > 2) {
                                foreach ((array) $prior_terms as $term_id) {
                                    $filter_field = is_numeric($term_id) ? 'term_id' : 'slug';
                                    $_terms = wp_list_filter($terms, array($filter_field => $term_id));

                                    if ($_terms) {
                                        $term = array_shift($_terms);
                                        break;
                                    }
                                }
                            } else
                                $term = array_shift($terms);

                            break;
                        }
                    }
                }

                if (isset($save_post)) $post = $save_post; // вернем обратно (для вложений)
            }

            // вывод

            // все виды записей с терминами или термины
            if ($term && isset($term->term_id)) {
                $term = apply_filters('sadovod_breadcrumbs_term', $term);

                // attachment
                if (is_attachment()) {
                    if (!$post->post_parent)
                        $out = sprintf($loc->attachment, esc_html($post->post_title));
                    else {
                        if (!$out = apply_filters('attachment_tax_crumbs', '', $term, $this)) {
                            $_crumbs    = $this->_tax_crumbs($term, 'self');
                            $parent_tit = sprintf($linkpatt, get_permalink($post->post_parent), ($this->arg->cur_pos--), get_the_title($post->post_parent));
                            $_out = implode('', array($_crumbs, $parent_tit));
                            $out = $this->_add_title($_out, $post);
                        }
                    }
                }
                // single
                elseif (is_single()) {
                    $_crumbs = '';
                    if (!$out = apply_filters('post_tax_crumbs', '', $term, $this)) {
                        if ($arg->show_term_title) {
                            $_crumbs .= $this->_tax_crumbs($term, 'self');
                            $out = $this->_add_title($_crumbs, $post);
                        }
                    }
                } elseif (is_tag() or is_category() or is_tax('theme')) {
                    $tmp_post = get_page_by_path('community');

                    $out = sprintf($linkpatt, get_home_url(null, $tmp_post->post_name, 'https'), ($this->arg->cur_pos--), $tmp_post->post_title);
                    $out .= $this->_add_title('', $term, esc_html($term->name));
                }
                //  не древовидная такса
                elseif (!is_taxonomy_hierarchical($term->taxonomy)) {

                    if (is_tax()) {
                        $_out = '';

                        $post_label = $ptype->labels->name;
                        $tax_label = $GLOBALS['wp_taxonomies'][$term->taxonomy]->labels->name;
                        $out = $this->_add_title($_out, $term, esc_html($term->name));
                    }
                }
                // древовидная такса (рибрики)
                else {
                    if (!$out = apply_filters('term_tax_crumbs', '', $term, $this)) {
                        $_crumbs = $this->_tax_crumbs($term, 'parent');
                        $out = $this->_add_title($_crumbs, $term, esc_html($term->name));
                    }
                }
            }
            // влоежния от записи без терминов
            elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $parent_link = sprintf($linkpatt, get_permalink($parent), ($this->arg->cur_pos--), esc_html($parent->post_title));
                $_out = $parent_link;

                // вложение от записи древовидного типа записи
                if (is_post_type_hierarchical($parent->post_type)) {
                    $parent_crumbs = $this->_page_crumbs($parent);
                    $_out = implode('', array($parent_crumbs, $parent_link));
                }

                $out = $this->_add_title($_out, $post);
            }
            // записи без терминов
            elseif (is_singular()) {
                $_crumbs = '';
                $out = $this->_add_title($_crumbs, $post);
            }
        }

        // замена ссылки на архивную страницу для типа записи
        $home_after = apply_filters('sadovod_breadcrumbs_home_after', '', $linkpatt, '', $ptype);

        if ($arg->show_cat_link and '' === $home_after) {
            // Ссылка на архивную страницу типа записи для: отдельных страниц этого типа; архивов этого типа; таксономий связанных с этим типом.
            if (
                $ptype && !in_array($ptype->name, array('page', 'attachment'))
                && (is_post_type_archive() || is_singular() || (is_tax() && in_array($term->taxonomy, $ptype->taxonomies)))
            ) {
                $pt_title = $ptype->labels->name;

                // первая страница архива типа записи
                if (is_post_type_archive() && !$paged_num) {
                    $home_after = sprintf($this->arg->linkpatt, ($this->arg->cur_pos--), $pt_title);
                    // singular, paged post_type_archive, tax
                } else if (is_post_type_archive() && $paged_num) {
                    $home_after = sprintf($linkpatt, get_post_type_archive_link($ptype->name), ($this->arg->cur_pos--), $pt_title);

                    $home_after .= (($paged_num && !is_tax() || empty($out))  ? $pg_end : ''); // пагинация
                } else {
                    if ($ptype->name != 'post') {
                        $home_after = sprintf($linkpatt, get_home_url(null, $ptype->name, 'https'), ($this->arg->cur_pos--), $pt_title);
                    } else {
                        $tmp_post = get_page_by_path('community');
                        $home_after = sprintf($linkpatt, get_home_url(null, $tmp_post->post_name, 'https'), ($this->arg->cur_pos--), $tmp_post->post_title);
                    }
                }
            }
        }

        $before_out = "";
        if ($arg->show_home_link) {
            $before_out = sprintf($linkpatt, home_url(), ($this->arg->cur_pos--), $loc->home);
        }

        if ($arg->show_cat_link) {
            $before_out .=  $home_after;
        }
        $out = apply_filters('sadovod_breadcrumbs_pre_out', $out, '', $loc, $arg);

        $out = sprintf($wrappatt, $before_out . $out);
        return apply_filters('sadovod_breadcrumbs', $out, '', $loc, $arg);
    }

    function _page_crumbs($post)
    {
        $parent = $post->post_parent;

        $crumbs = array();
        while ($parent) {
            $page = get_post($parent);
            $crumbs[] = sprintf($this->arg->linkpatt, get_permalink($page), ($this->arg->cur_pos--), esc_html($page->post_title));
            $parent = $page->post_parent;
        }

        return implode('', array_reverse($crumbs));
    }

    function _tax_crumbs($term, $start_from = 'self')
    {
        $termlinks = array();
        $term_id = ($start_from === 'parent') ? $term->parent : $term->term_id;
        while ($term_id) {
            $term       = get_term($term_id, $term->taxonomy);
            $termlinks[] = sprintf($this->arg->linkpatt, get_term_link($term), ($this->arg->cur_pos--), esc_html($term->name));
            $term_id    = $term->parent;
        }

        if ($termlinks)
            return implode('', array_reverse($termlinks)) /*. ''*/;

        return '';
    }

    // добалвяет заголовок к переданному тексту, с учетом всех опций. Добавляет разделитель в начало, если надо.
    function _add_title($add_to, $obj, $term_title = '')
    {
        $arg = &$this->arg; // упростим...
        $title = $term_title ? $term_title : esc_html($obj->post_title); // $term_title чиститься отдельно, теги моугт быть...
        $show_title = $term_title ? $arg->show_term_title : $arg->show_post_title;

        // пагинация
        $link = $term_title ? get_term_link($obj) : get_permalink($obj);
        if ($arg->pg_end) {
            $add_to .= sprintf($arg->linkpatt, $link, 50, $title) . $arg->pg_end;
        }
        // дополняем - ставим sep
        elseif ($add_to) {
            if ($show_title)
                $add_to .= sprintf($arg->linkpatt, $link, 50, $title);
        }
        // sep будет потом...
        elseif ($show_title)
            $add_to = sprintf($arg->linkpatt, $link, 50, $title);

        return $add_to;
    }
}
