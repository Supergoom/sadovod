<?php

// Регистрируем новый виджет
add_action('widgets_init', 'register_popular_posts_widget');
function register_popular_posts_widget()
{
    register_widget('Popular_Posts_Widget');
}

// Добавляем новый виджет
class Popular_Posts_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'popular_posts_widget',
            __('Popular Posts', "sadovod"),
            array('description' => __('Shows popular posts', "sadovod"),)
        );
    }

    /** Вывод виджета популярных записей
     *
     *  @param array $args     аргументы виджета.
     *  @param array $instance сохраненные данные из настроек
     */
    public function widget($args, $instance)
    {
        global $public_posts;

        // Получим опции виджета
        $title = apply_filters('widget_title', $instance['title']);
        $post_date_limit = empty(parse_int($instance['period'])) ? 0 : date('Y-m-d', strtotime(current_time('Y-m-d') . '+' . $instance['period'] . 'days'));
        $post_count_limit = parse_int($instance['quantity']) ?: 3;
        $post_type = isset($instance['type']) ? $instance['type'] : 'current';

        $post_array = array_keys($public_posts);
        $posts = $post_type !== 'all' ? (isset($public_posts[$post_type]) ? $post_type : get_page_post_type()) : $post_array;
        $posts = in_array($posts, array('page', 'attachment')) ? $post_array : $posts;

        // Аргументы для проверки даты сброса
        $query = array(
            'post_status'       => 'publish',
            'post_type'         => $posts,
            'posts_per_page'    => $post_count_limit,
            'meta_query'        => array(
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'parent',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key' => 'parent',
                        'value' => 0,
                    ),
                ),
                'views'  => array(
                    'key' => 'post_views',
                ),
            ),
            'orderby'           => 'post_date views',
            'order'             => 'DESC'
        );

        if ($post_date_limit) {
            $query['date_query'] = array(
                'before' => $post_date_limit
            );
        }

        if (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            $query['tax_query'] = array(
                array(
                    'taxonomy' => $term->taxonomy,
                    'terms'    => $term->term_id
                )
            );
        }

        echo str_replace('>', ' itemscope itemtype="http://schema.org/Blog">', $args['before_widget']);

        if (!empty($title)) {
            echo str_replace('>', ' itemprop="description">', $args['before_title']) . $title . $args['after_title'];
        }

        $populars_query = new WP_Query($query);
        while ($populars_query->have_posts()) {
            $populars_query->the_post();

            $type = 'BlogPosting';
            $post_type = get_post_type();
            if ($post_type == 'resources')
                $type = 'Product';
            else if ($post_type = 'projects')
                $type = 'Project';
            else if ($post_type = 'vacancy')
                $type = 'JobPosting';
?>

            <div class="popular-posts-item card_wrap card_widget" itemscope itemtype="http://schema.org/<?= $type ?>">
                <div class="card_body_wrap">
                    <meta itemprop="articleBody" content="<?php wp_strip_all_tags(the_excerpt()); ?>">
                    <meta itemprop="dateModified" content="<?php the_modified_date('c') ?>">
                    <meta itemprop="datePublished" content="<?php the_date('c') ?>">
                    <link itemprop="publisher" href="#sadovod">
                    <link itemprop="mainEntityOfPage" href="<?= get_post_permalink(); ?>">
                    <link itemprop="image" href="<?= get_the_post_thumbnail_uri(get_the_ID()); ?>">

                    <?php
                    $record_type = get_post_type();

                    $query = array(
                        'public'    => true,
                        'rewrite'   => true,
                        'object_type' => array($record_type)
                    );

                    $taxomomy = current(get_taxonomies($query));
                    $terms = wp_get_post_terms(get_the_ID(), $taxomomy);

                    $category_list = array();
                    foreach ($terms as $term) {
                        $category_list[] = '<a href="' . get_term_link($term, $taxomomy) . '">' . $term->name . '</a>';
                    }
                    ?>

                    <?php if (count($category_list)) : ?>
                        <small class="card_subtitle">
                            <?= implode(', ', $category_list); ?>
                        </small>
                    <?php endif; ?>

                    <h5 itemprop="headline" class="card_title"><a itemprop="url" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                    <div class="card_desc" itemprop="description"><?php the_excerpt(); ?></div>
                    <div class="card_footer">
                        <div class="card_reviews"><?php the_reviews() ?></div>
                        <div class="card_views"><?php the_views(); ?></div>
                    </div>
                    <?php add_record_creator(get_the_ID()); ?>
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

        $title = isset($instance['title']) ? $instance['title'] : __('Popular Posts', "sadovod");
        $period = isset($instance['period']) ? $instance['period'] : '1';
        $quantity = isset($instance['quantity']) ? $instance['quantity'] : '3';
        $post_type = isset($instance['type']) ? $instance['type'] : 'current';
    ?>

        <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('Title', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_html($title); ?>">

        <label for="<?php echo $this->get_field_id('period'); ?>"><?= __('Period', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('period'); ?>" name="<?php echo $this->get_field_name('period'); ?>" type="text" value="<?php echo esc_attr($period); ?>">

        <label for="<?php echo $this->get_field_id('quantity'); ?>"><?= __('Quanity', "sadovod"); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('quantity'); ?>" name="<?php echo $this->get_field_name('quantity'); ?>" type="text" value="<?php echo esc_attr($quantity); ?>">

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
        $instance['period'] = (!empty($new_instance['period'])) ? strip_tags($new_instance['period']) : 0;
        $instance['quantity'] = (!empty($new_instance['quantity'])) ? strip_tags($new_instance['quantity']) : 3;
        $instance['type'] = (!empty($new_instance['type'])) ? strip_tags($new_instance['type']) : 'current';

        return $instance;
    }
}
