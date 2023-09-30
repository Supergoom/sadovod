<?php

// Регистрируем новый виджет
add_action('widgets_init', 'register_popular_terms_widget');
function register_popular_terms_widget()
{
    register_widget('Popular_Terms_Widget');
}

// Добавляем новый виджет
class Popular_Terms_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'popular_terms_widget',
            __('Popular Terms', "sadovod"),
            array('description' => __('Shows popular terms', "sadovod"),)
        );
    }

    /** Вывод виджета популярных категорий
     *
     *  @param array $args     аргументы виджета.
     *  @param array $instance сохраненные данные из настроек
     */
    public function widget($args, $instance)
    {
        // Получим опции виджета
        $title = apply_filters('widget_title', $instance['title']);
        $term_date_limit = empty(parse_int($instance['period'])) ? 0 : date('Y-m-d', strtotime(current_time('Y-m-d') . '+' . $instance['period'] . 'days'));
        $term_count_limit = parse_int($instance['quantity']) ?: 15;

        // Аргументы для проверки даты сброса
        $query = array(
            'parent'            => 0,
            'number'            => $term_count_limit,
            'hide_empty'        => 1,
            'meta_query'        => array(
                'views'  => array(
                    'key' => 'term_views',
                ),
            ),
            'orderby'           => 'post_date views',
            'order'             => 'DESC'
        );

        if ($term_date_limit) {
            $query['date_query'] = array(
                'before' => $term_date_limit
            );
        }

        global $public_posts;
        $post_type = get_page_post_type();
        if (isset($public_posts[$post_type])) {
            if (!empty($public_posts[$post_type]->taxonomies)) {
                $taxonomy = current(array_remove_val($public_posts[$post_type]->taxonomies, 'post_tag'));
                $query['taxonomy'] = $taxonomy;
            }
        }

        if (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            $query['taxonomy'] = $term->taxonomy;
            $query['parent'] = $term->term_id;
        }

        $term_query = new WP_Term_Query($query);
        if (empty($term_query->terms))
            return;

        echo str_replace('>', ' itemscope itemtype="http://www.schema.org/SiteNavigationElement">', $args['before_widget']);

        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        foreach ($term_query->terms as $term) :
            $image = get_term_image_url($term); ?>

            <a class="btn btn-secondary cat-wrap" itemprop="url" href="<?= get_term_link($term->term_id, $term->taxonomy); ?>">
                <img alt="<?= $term->name; ?>" title="<?= $term->name; ?>" data-src="<?= $image ? $image : get_stylesheet_directory_uri() . '/assets/img/placeholder-small.jpg'; ?>" class="lazyload"><span itemprop="name"><?= $term->name; ?></span>
            </a>
        <?php endforeach;

        echo $args['after_widget'];

        wp_reset_postdata(); ?>
    <?php
    }

    /**
     * Админ-часть виджета
     *
     * @param array $instance сохраненные данные из настроек
     */
    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : __('Popular Terms', "sadovod");
        $period = isset($instance['period']) ? $instance['period'] : '1';
        $quantity = isset($instance['quantity']) ? $instance['quantity'] : '15';
    ?>

        <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('Title', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_html($title); ?>">

        <label for="<?php echo $this->get_field_id('period'); ?>"><?= __('Period', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('period'); ?>" name="<?php echo $this->get_field_name('period'); ?>" type="text" value="<?php echo esc_attr($period); ?>">

        <label for="<?php echo $this->get_field_id('quantity'); ?>"><?= __('Quanity', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('quantity'); ?>" name="<?php echo $this->get_field_name('quantity'); ?>" type="text" value="<?php echo esc_attr($quantity); ?>">

<?php
    }

    /**
     * Сохранение настроек виджета. Здесь данные должны быть очищены и возвращены для сохранения их в базу данных.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance новые настройки
     * @param array $old_instance предыдущие настройки
     *
     * @return array данные которые будут сохранены
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? ($new_instance['title']) : "";
        $instance['period'] = (!empty($new_instance['period'])) ? strip_tags($new_instance['period']) : 0;
        $instance['quantity'] = (!empty($new_instance['quantity'])) ? strip_tags($new_instance['quantity']) : 15;

        return $instance;
    }
}
