<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_Customize_Control'))
    return NULL;
/**
 * Class to create a custom tags control
 */
class Slider_Control extends WP_Customize_Control
{

    /**
     * Relation to elements with the children class
     */
    public $relation = '';

    /**
     * Enable image
     */
    public $img = '';

    /**
     * Enable icon
     */
    public $ico = '';

    /**
     * Enable title
     */
    public $title = '';

    /**
     * Enable link
     */
    public $link = '';

    /**
     * Enable type
     */
    public $type = '';

    /**
     * Enable text
     */
    public $text = '';

    /**
     * Enable additional text
     */
    public $subtext = '';

    /**
     * Enable check
     */
    public $check = '';

    public function enqueue()
    {
        parent::enqueue();

        wp_enqueue_script('slider-control-js', get_template_directory_uri() . '/settings/assets/js/slider.js', array('jquery', 'wp-i18n'));
        wp_enqueue_style('slider-control-css', get_template_directory_uri() . '/settings/assets/css/slider.css', array('dashicons'));
    }


    public function render_content()
    {
        global $all_posts;

        $additional_posts = array(
            'users' => __('Users', 'sadovod')
        );
?>
        <div class="slider-arr <?php echo $this->relation; ?>">
            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?><span class="cody-help" data-title="<?php echo wp_kses_post($this->text); ?>"></span></span>
                <input type="hidden" id="slider-arr" <?php $this->link(); ?> value="<?php echo htmlspecialchars($this->value()); ?>">
            </label>
            <div class="slides-box">
                <div class="slide-default">
                    <div class="slide-expand"><span class="slide-name">Новый слайд</span><span class="dashicons dashicons-arrow-down"></span></div>
                    <div class="slide-content active">
                        <?php if ($this->img == true) : ?>
                            <span class="slide-img">
                                <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDI5NyAyOTciIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDI5NyAyOTc7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMjU2cHgiIGhlaWdodD0iMjU2cHgiPgo8Zz4KCTxwYXRoIGQ9Ik0yOTQuMDYxLDEwMS4zOTVjLTEuODUxLTEuODQ5LTQuMzU5LTIuODg3LTYuOTc2LTIuODg3bC04OC42MTcsMC4wMTJsMC4wMTMtODguNjU1YzAtMi42MTctMS4wMzgtNS4xMjYtMi44ODgtNi45NzcgICBDMTkzLjc0MiwxLjAzOCwxOTEuMjMzLDAsMTg4LjYxNiwwbC04MC4xODgsMC4wMTJjLTUuNDQ1LDAuMDAyLTkuODU5LDQuNDE1LTkuODYsOS44NmwtMC4wMTYsODguNjYyTDkuOTI2LDk4LjU0OCAgIGMtNS40NDYsMC05Ljg2LDQuNDE1LTkuODYxLDkuODZMMC4wNTEsMTg4LjYzYzAsMi42MTgsMS4wMzgsNS4xMjYsMi44ODksNi45NzdjMS44NSwxLjg1LDQuMzU5LDIuODg4LDYuOTc2LDIuODg4bDg4LjYyMS0wLjAxMiAgIGwtMC4wMTQsODguNjUzYzAsMi42MTcsMS4wNCw1LjEyNiwyLjg4OSw2Ljk3N2MxLjg1MSwxLjg1LDQuMzYsMi44OSw2Ljk3NywyLjg4OGw4MC4xODctMC4wMTZjNS40NDUsMCw5Ljg1OS00LjQxNSw5Ljg2LTkuODYgICBsMC4wMTQtODguNjU4bDg4LjYyOS0wLjAxNmM1LjQ0NSwwLDkuODU5LTQuNDE1LDkuODYtOS44NmwwLjAxMi04MC4yMkMyOTYuOTQ5LDEwNS43NTQsMjk1LjkxMSwxMDMuMjQ2LDI5NC4wNjEsMTAxLjM5NXoiIGZpbGw9IiNiZGJkYmQiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" data-id="">
                            </span>
                        <?php endif; ?>
                        <div class="slide-info">
                            <?php if ($this->ico == true) : ?>
                                <span class="slide-ico"><i class=""></i><input type="text" placeholder="<?php _e('Icon', 'sadovod'); ?>"></span>
                            <?php endif; ?>
                            <?php if ($this->title == true) : ?>
                                <span class="slide-title"><input type="text" value="Новый слайд" placeholder="<?php _e('Title', 'sadovod'); ?>"></span>
                            <?php endif; ?>
                            <?php if ($this->link == true) : ?>
                                <span class="slide-link"><input type="text" placeholder="<?php _e('Link', 'sadovod'); ?>"></span>
                            <?php endif; ?>
                            <?php if ($this->type == true) : ?>
                                <span class="slide-type"><select>
                                        <option disabled selected><?php _e('Select type', 'sadovod'); ?></option>
                                        <?php foreach ($all_posts as $post) : ?>
                                            <option value="<?= $post->name; ?>"><?= $post->label; ?></option>
                                        <?php endforeach; ?>

                                        <?php foreach ($additional_posts as $name => $label) : ?>
                                            <option value="<?= $name; ?>"><?= $label; ?></option>
                                        <?php endforeach; ?>
                                    </select></span>
                            <?php endif; ?>
                            <?php if ($this->text == true) : ?>
                                <span class="slide-text"><textarea placeholder="<?php _e('Text', 'sadovod'); ?>"></textarea></span>
                            <?php endif; ?>
                            <?php if ($this->subtext == true) : ?>
                                <span class="slide-subtext"><textarea placeholder="<?php _e('Additional text', 'sadovod'); ?>"></textarea></span>
                            <?php endif; ?>
                            <?php if ($this->check == true) : ?>
                                <span class="slide-check"><input type="checkbox"></span>
                            <?php endif; ?>
                            <span class="slide-remove dashicons dashicons-trash"></span>
                        </div>
                    </div>
                </div>
        <?php
        $slides = '';
        if ($this->value() != '') {
            $slides = json_decode('[' . $this->value() . ']', true);
        }
        if ($slides) {
            foreach ($slides as $slide) {
                foreach ($slide as $sl) {
                    if ($this->title == true) {
                        if (isset($sl['title'])) {
                            $title = $sl['title'];
                        } else {
                            $title = __('Title', 'sadovod');
                        }
                    }
                    echo '<div class="slide-box">
                        <div class="slide-expand"><span class="slide-name">' . $title . '</span><span class="dashicons dashicons-arrow-down"></span></div>
                        <div class="slide-content">';
                    if ($this->img == true) {
                        if (isset($sl['img'])) {
                            echo '<span class="slide-img">
                                        <img src="' . wp_get_attachment_image_url($sl['img'], 'medium') . '" data-id="' . $sl['img'] . '">
                                    </span>';
                        } else {
                            echo '<span class="slide-img">
                                        <img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDI5NyAyOTciIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDI5NyAyOTc7IiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iMjU2cHgiIGhlaWdodD0iMjU2cHgiPgo8Zz4KCTxwYXRoIGQ9Ik0yOTQuMDYxLDEwMS4zOTVjLTEuODUxLTEuODQ5LTQuMzU5LTIuODg3LTYuOTc2LTIuODg3bC04OC42MTcsMC4wMTJsMC4wMTMtODguNjU1YzAtMi42MTctMS4wMzgtNS4xMjYtMi44ODgtNi45NzcgICBDMTkzLjc0MiwxLjAzOCwxOTEuMjMzLDAsMTg4LjYxNiwwbC04MC4xODgsMC4wMTJjLTUuNDQ1LDAuMDAyLTkuODU5LDQuNDE1LTkuODYsOS44NmwtMC4wMTYsODguNjYyTDkuOTI2LDk4LjU0OCAgIGMtNS40NDYsMC05Ljg2LDQuNDE1LTkuODYxLDkuODZMMC4wNTEsMTg4LjYzYzAsMi42MTgsMS4wMzgsNS4xMjYsMi44ODksNi45NzdjMS44NSwxLjg1LDQuMzU5LDIuODg4LDYuOTc2LDIuODg4bDg4LjYyMS0wLjAxMiAgIGwtMC4wMTQsODguNjUzYzAsMi42MTcsMS4wNCw1LjEyNiwyLjg4OSw2Ljk3N2MxLjg1MSwxLjg1LDQuMzYsMi44OSw2Ljk3NywyLjg4OGw4MC4xODctMC4wMTZjNS40NDUsMCw5Ljg1OS00LjQxNSw5Ljg2LTkuODYgICBsMC4wMTQtODguNjU4bDg4LjYyOS0wLjAxNmM1LjQ0NSwwLDkuODU5LTQuNDE1LDkuODYtOS44NmwwLjAxMi04MC4yMkMyOTYuOTQ5LDEwNS43NTQsMjk1LjkxMSwxMDMuMjQ2LDI5NC4wNjEsMTAxLjM5NXoiIGZpbGw9IiNiZGJkYmQiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" data-id="">
                                    </span>';
                        }
                    }
                    echo '<div class="slide-info">';
                    if ($this->ico == true) {
                        if (isset($sl['ico'])) {
                            echo '<span class="slide-ico"><i class="i-' . $sl['ico'] . '"></i><input type="text" value="' . $sl['ico'] . '" ></span>';
                        } else {
                            echo '<span class="slide-ico"><i class=""></i><input type="text" placeholder="' . __('Icon', 'sadovod') . '" ></span>';
                        }
                    }
                    if ($this->title == true) {
                        if (isset($sl['title'])) {
                            echo '<span class="slide-title"><input type="text" value="' . $sl['title'] . '" ></span>';
                        } else {
                            echo '<span class="slide-title"><input type="text" placeholder="' . __('Title', 'sadovod') . '" ></span>';
                        }
                    }
                    if ($this->link == true) {
                        if (isset($sl['url'])) {
                            echo '<span class="slide-link"><input type="text" value="' . $sl['url'] . '" ></span>';
                        } else {
                            echo '<span class="slide-link"><input type="text" placeholder="' . __('Link', 'sadovod') . '" ></span>';
                        }
                    }
                    if ($this->type == true) {
                        echo '<span class="slide-type"><select>';
                        foreach ($all_posts as $post) {
                            echo '<option value="' . $post->name . '" ' . ($sl['type'] != $post->name ?: 'selected') . '>' . $post->label . '</option>';
                        }
                        foreach ($additional_posts as $name => $label) {
                            echo '<option value="' . $name . '" ' . ($sl['type'] != $name ?: 'selected') . '>' . $label . '</option>';
                        }
                        echo '</select></span>';
                    }
                    if ($this->text == true) {
                        if (isset($sl['text'])) {
                            echo '<span class="slide-text"><textarea>' . $sl['text'] . '</textarea></span>';
                        } else {
                            echo '<span class="slide-text"><textarea placeholder="' . __('Text', 'sadovod') . '"></textarea></span>';
                        }
                    }
                    if ($this->subtext == true) {
                        if (isset($sl['subtext'])) {
                            echo '<span class="slide-subtext"><textarea>' . $sl['subtext'] . '</textarea></span>';
                        } else {
                            echo '<span class="slide-subtext"><textarea placeholder="' . __('Additional text', 'sadovod') . '"></textarea></span>';
                        }
                    }
                    if ($this->check == true) {
                        if (isset($sl['check'])) {
                            echo '<span class="slide-check"><input type="checkbox" checked="checked" ></span>';
                        } else {
                            echo '<span class="slide-check"><input type="checkbox" ></span>';
                        }
                    }
                    echo '<span class="slide-remove dashicons dashicons-trash"></span>
                            </div>
                        </div>
                    </div>';
                }
            }
        } else {
            echo __('Slides not found.', 'sadovod-dialogs');
        }
        echo '</div>
			<input type="button" class="button button-primary slide-adding" value="' . __('Add', 'sadovod') . '">
		</div>';
    }
}
