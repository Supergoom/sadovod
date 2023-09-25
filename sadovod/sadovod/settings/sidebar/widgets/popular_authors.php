<?php

// Регистрируем новый виджет
add_action('widgets_init', 'register_popular_authors_widget');
function register_popular_authors_widget()
{
    register_widget('Popular_Authors_Widget');
}

// Добавляем новый виджет
class Popular_Authors_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'popular_authors_widget',
            __('Popular Authors', "sadovod"),
            array('description' => __('Shows popular authors', "sadovod"),)
        );
    }

    /** Вывод виджета популярных авторов
     *
     *  @param array $args     аргументы виджета.
     *  @param array $instance сохраненные данные из настроек
     */
    public function widget($args, $instance)
    {
        global $public_posts;

        // Получим опции виджета
        $title = apply_filters('widget_title', $instance['title']);
        $limit = parse_int($instance['quantity']) ?: 3;
        $date_limit = empty(parse_int($instance['period'])) ? 0 : date('Y-m-d', strtotime(current_time('Y-m-d') . '+' . $instance['period'] . 'days'));
        $post_type = isset($instance['type']) ? $instance['type'] : 'current';

        $post_array = array_keys($public_posts);
        $posts = $post_type !== 'all' ? (isset($public_posts[$post_type]) ? $post_type : get_page_post_type()) : $post_array;
        $posts = in_array($posts, array('page', 'attachment')) ? $post_array : $posts;

        // Аргументы для проверки даты сброса
        $query = array(
            'number'                => $limit,
            'has_published_posts'   => true,
            'meta_query'            => array(
                'rating' => array(
                    'key'         => 'rating',
                    'type'        => 'NUMERIC'
                ),
            ),
            'orderby'               => 'post_count rating registered',
            'order'                 => 'DESC'
        );

        if ($date_limit || (is_category() || is_tag() || is_tax())) {

            $post_query = array(
                'post_status'       => 'publish',
                'post_type'         => $posts,
                'posts_per_page'    => 200,
            );

            if ($date_limit) {
                $post_query['date_query'] = array(
                    'before' => $date_limit
                );
            }

            if (is_category() || is_tag() || is_tax()) {
                $term = get_queried_object();
                $post_query['tax_query'] = array(
                    array(
                        'taxonomy' => $term->taxonomy,
                        'terms'    => $term->term_id
                    )
                );
            }

            $posted_users = array();
            $populars_query = new WP_Query($post_query);
            foreach ($populars_query->posts as $post) {
                $posted_users[] = $post->post_author;
            }

            if (!empty($posted_users)) {
                $query['include'] = $posted_users;
            }
        }



        if (!empty($title)) {
            echo $args['before_title'];
            echo $title;
            echo '<a class="more-link" href="/users" target="_blank">' . __('All authors', 'sadovod') . '</a>';
            echo $args['after_title'];
        }

        echo str_replace('>', ' itemscope="itemscope" itemtype="http://www.schema.org/SiteNavigationElement">', $args['before_widget']);

        $popular_query = get_users($query);
        foreach ($popular_query as $user) {
            $author_url = get_author_posts_url($user->ID, $user->user_nicename);
?>

            <div class="popular-authors-item">
                <div class="popular-author-avatar">
                    <a itemprop="url" class="popular-author-name" href="<?= $author_url; ?>" target="_blank">
                        <?= get_avatar($user->ID, 40, '', '', array('class' => 'lazyload')); ?>
                    </a>
                </div>
                <div class="popular-author-info">
                    <div class="popular-author-name">
                        <a href="<?= $author_url; ?>" target="_blank" itemprop="name"><?= $user->display_name; ?></a>
                    </div>
                    <span class="popular-author-tag">
                        <a href="<?= $author_url; ?>" target="_blank">
                            @<?= $user->user_nicename; ?>
                        </a>
                    </span>
                </div>
            </div>

        <?php
        }

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
        global $public_posts;

        $title = isset($instance['title']) ? $instance['title'] : __('Popular Authors', "sadovod");
        $quantity = isset($instance['quantity']) ? $instance['quantity'] : '3';
        $period = isset($instance['period']) ? $instance['period'] : '1';
        $post_type = isset($instance['type']) ? $instance['type'] : 'current';
    ?>

        <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('Title', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_html($title); ?>">

        <label for="<?php echo $this->get_field_id('quantity'); ?>"><?= __('Quanity', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('quantity'); ?>" name="<?php echo $this->get_field_name('quantity'); ?>" type="text" value="<?php echo esc_attr($quantity); ?>">

        <label for="<?php echo $this->get_field_id('period'); ?>"><?= __('Period', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('period'); ?>" name="<?php echo $this->get_field_name('period'); ?>" type="text" value="<?php echo esc_attr($period); ?>">

        <?php
        $type_options = array_merge(array('current' => (object) array('label' => __('Current post type', 'sadovod'))), $public_posts);
        $type_options = array_merge(array('all' => (object) array('label' => __('All post types', 'sadovod'))), $type_options);
        ?>
        <label for="<?php echo $this->get_field_id('type'); ?>"><?= __('Post Type', "sadovod"); ?></label>
        <select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
            <?php foreach ($type_options as $name => $post) : ?>
                <option value="<?= $name; ?>" <?= $name == $post_type ? ' selected' : ''; ?>><?= $post->label; ?></option>
            <?php endforeach; ?>
        </select>
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
        $instance['quantity'] = (!empty($new_instance['quantity'])) ? strip_tags($new_instance['quantity']) : 3;
        $instance['period'] = (!empty($new_instance['period'])) ? strip_tags($new_instance['period']) : 0;
        $instance['type'] = (!empty($new_instance['type'])) ? strip_tags($new_instance['type']) : 'current';

        return $instance;
    }
}
