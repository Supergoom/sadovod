<?php

/*  Путь к шаблонам
-----------------------------------------------*/
function change_template_path($templates)
{
    $custom_templates = array(
        'home.php' => 'templates/feed.php',
        'author.php' => 'profile.php',
    );

    $custom_sub_dir = 'layouts';
    if (empty($templates) || !is_array($templates)) {
        return $templates;
    }

    $page_template_id = 0;
    $count = count($templates);
    if ($templates[0] === get_page_template_slug()) {
        $page_template_id = 1;
    }

    for ($i = $page_template_id; $i < $count; $i++) {
        if (in_array($templates[$i], array_keys($custom_templates)))
            $templates[$i] = $custom_templates[$templates[$i]];

        $templates[$i] = $custom_sub_dir . '/' . $templates[$i];
    }

    return $templates;
}

add_filter('404_template_hierarchy', 'change_template_path');
add_filter('archive_template_hierarchy', 'change_template_path');
add_filter('attachment_template_hierarchy', 'change_template_path');
add_filter('author_template_hierarchy', 'change_template_path');
add_filter('category_template_hierarchy', 'change_template_path');
add_filter('date_template_hierarchy', 'change_template_path');
add_filter('frontpage_template_hierarchy', 'change_template_path');
add_filter('home_template_hierarchy', 'change_template_path');
add_filter('index_template_hierarchy', 'change_template_path');
add_filter('page_template_hierarchy', 'change_template_path');
add_filter('paged_template_hierarchy', 'change_template_path');
add_filter('privacypolicy_template_hierarchy', 'change_template_path');
add_filter('search_template_hierarchy', 'change_template_path');
add_filter('single_template_hierarchy', 'change_template_path');
add_filter('singular_template_hierarchy', 'change_template_path');
add_filter('tag_template_hierarchy', 'change_template_path');
add_filter('taxonomy_template_hierarchy', 'change_template_path');

function single_custom_template($single)
{

    global $post;
    global $public_posts;

    if (in_array($post->post_type, array_keys($public_posts))) {
        $name = basename($public_posts[get_post_type()]->rewrite['slug']);
        if (file_exists(get_template_directory() . '/layouts/single/' . $name . '.php')) {
            return get_template_directory() . '/layouts/single/' . $name . '.php';
        } else {
            return get_template_directory() . 'layouts/single/default.php';
        }
    }

    return $single;
}
//add_filter('single_template', 'single_custom_template');

function custom_comments_template()
{
    return get_template_directory() . '/layouts/widgets/comments.php';
}
add_filter('comments_template', 'custom_comments_template');

function load_record_templates($templates, $that, $post, $post_type)
{
    $custom_sub_dir = 'layouts/templates';

    $templates[$post_type] = is_array($templates) ? $templates : array();

	$dir = get_template_directory();
    $files = get_dir_files(get_template_directory() . '/' . $custom_sub_dir);
    foreach ($files as $full_path) {
        $file = str_replace($dir, '', $full_path);

        if (!preg_match('|Template Name:(.*)$|mi', file_get_contents($full_path), $header)) {
            continue;
        }

        $types = array('page');
        if (
            preg_match('|Template Post Type:(.*)$|mi', file_get_contents($full_path), $type)
        ) {
            $types = explode(',', _cleanup_header_comment($type[1]));
        }

        foreach ($types as $type) {
            $type = sanitize_key($type);
            if (!isset($templates[$type])) {
                $templates[$type] = array();
            }

            $templates[$type][$file] = _cleanup_header_comment($header[1]);
        }
    }

    if ($that->load_textdomain()) {
        foreach ($templates as &$post) {
            foreach ($post as &$template) {
                $template = translate($template, $that->get('TextDomain'));
            }
        }
    }

    $post_templates = isset($templates[$post_type]) ? $templates[$post_type] : array();

    return $post_templates;
};
add_filter('theme_templates', 'load_record_templates', 10, 4);

function get_dir_files($dir){
	$file_list = array();
	
	$files = (array) array_diff(scandir($dir), array('.', '..'));
	foreach ($files as $file) {
		$cur_location = $dir .'/'.$file;
		
		if (!is_dir($cur_location)) {
			$file_list[] = $cur_location;
		}else{
			$file_list = array_merge($file_list, get_dir_files($cur_location));
		}			
	}
	
	return $file_list;
}

// GET MY HEADER BACK
add_action( 'elementor/theme/register_locations', function(){
    $elementor_theme_support = \ElementorPro\Plugin::instance()->modules_manager->get_modules('theme-builder')->get_component( 'theme_support' );	
    remove_action( 'get_header', [ $elementor_theme_support, 'get_header' ] );
}, 100);

add_action( 'elementor/theme/register_locations', function(){
    $elementor_theme_support = \ElementorPro\Plugin::instance()->modules_manager->get_modules('theme-builder')->get_component( 'theme_support' );	
    remove_action( 'get_footer', [ $elementor_theme_support, 'get_footer' ] );
}, 100);
