<?php

/**
 * Converter class file.
 */

namespace Slugify\Converter;


/**
 * Class Settings
 *
 * Central point to get settings from.
 */
class Settings
{

    /**
     * Form fields.
     *
     * @var array
     */
    protected $form_fields;

    /**
     * Plugin options.
     *
     * @var array
     */
    protected $settings;

    /**
     * Converter constructor.
     */
    public function __construct()
    {
        add_action('init', [$this, 'setup_settings'], PHP_INT_MAX);

        add_action('admin_menu', [$this, 'setup_converter_page']);
        add_action('in_admin_header', [$this, 'setup_converter_notif']);

        add_action('current_screen', [$this, 'setup_sections']);
        add_action('current_screen', [$this, 'setup_fields']);

        add_filter('pre_update_option_translit_settings', [$this, 'pre_update_options'], 10, 2);

        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);

        add_action('tool_box', [$this, 'setup_slugify_toolbox']);
    }

    /*---------------------------------------------------------------------------------------------
	-- Setup Fields
	--------------------------------------------------------------------------------------------- */

    /**
     * Init form fields and settings late, on 'init' hook with PHP_INT_MAX priority,
     * to allow all plugins to register post types.
     */
    public function setup_settings()
    {
        $this->init_form_fields();
        $this->init_settings();
    }

    /**
     * Init form fields.
     */
    public function init_form_fields()
    {
        global $all_posts;

        $post_types = array_merge(
            $all_posts,
            array(
                (object)array('label' => 'nav_menu_item', 'name' => 'nav_menu_item')
            )
        );
        $filtered_post_types = array_filter((array) apply_filters('slugify_post_types', array_keys($post_types)));

        $this->form_fields['background_post_types'] = [
            'label'        => __('Post Types', 'sadovod'),
            'section'      => 'background_section',
            'type'         => 'checkbox',
            'placeholder'  => '',
            'helper'       => __('Post types included in the conversion.', 'sadovod'),
            'supplemental' => '',
            'options'      => [],
        ];
        $this->form_fields['background_post_types']['default'] = ['post', 'page', 'nav_menu_item'];

        foreach ($post_types as $post_type) {
            $this->form_fields['background_post_types']['options'][$post_type->name] = $post_type->label;
        }

        $default_post_types = ['post', 'page', 'nav_menu_item'];

        $this->form_fields['background_post_types']['default'] = $default_post_types;
        // @todo Mark as disabled.
        $this->form_fields['background_post_types']['disabled'] = array_diff($default_post_types, $filtered_post_types);

        $core_post_statuses = ['publish', 'future', 'private'];
        $post_statuses      = ['publish', 'future', 'private', 'draft', 'pending'];

        $this->form_fields['background_post_statuses'] = [
            'label'        => __('Post Statuses', 'sadovod'),
            'section'      => 'background_section',
            'type'         => 'checkbox',
            'placeholder'  => '',
            'helper'       => __('Post statuses included in the conversion.', 'sadovod'),
            'supplemental' => '',
            'options'      => [],
        ];

        foreach ($post_statuses as $post_status) {
            $label = $post_status;

            $this->form_fields['background_post_statuses']['options'][$post_status] = $label;
        }

        $this->form_fields['background_post_statuses']['default'] = $core_post_statuses;
    }


    /**
     * Init settings.
     */
    protected function init_settings()
    {
        $this->settings = get_option('translit_settings', null);

        $form_fields = array_map([$this, 'set_defaults'], $this->form_fields);

        if (is_array($this->settings)) {
            $this->settings = array_merge(wp_list_pluck($form_fields, 'default'), $this->settings);
            return;
        }

        // If there are no settings defined, use defaults.
        $this->settings = array_merge(
            array_fill_keys(array_keys($form_fields), ''),
            wp_list_pluck($form_fields, 'default')
        );
    }

    /**
     * Filter plugin option update.
     *
     * @param mixed $value     New option value.
     * @param mixed $old_value Old option value.
     *
     * @return mixed
     */
    public function pre_update_options($value, $old_value)
    {
        if ($value === $old_value) {
            return $value;
        }

        // We save only one table, so merge with all existing tables.
        if (is_array($old_value) && (is_array($value))) {
            $value = array_merge($old_value, $value);
        }

        $form_fields = $this->form_fields();
        foreach ($form_fields as $key => $form_field) {
            if ('checkbox' === $form_field['type']) {
                $form_field_value = isset($value[$key]) ? $value[$key] : 'no';
                $form_field_value = '1' === $form_field_value || 'yes' === $form_field_value ? 'yes' : 'no';
                $value[$key]    = $form_field_value;
            }
        }

        return $value;
    }


    /*---------------------------------------------------------------------------------------------
	-- Page Scripts
	--------------------------------------------------------------------------------------------- */

    /**
     * Enqueue class scripts.
     */
    public function admin_enqueue_scripts()
    {
        if (!$this->is_options_screen()) {
            return;
        }

        wp_enqueue_style('slugify', get_stylesheet_directory_uri() . '/modules/slugify/assets/css/converter.css');
        wp_enqueue_script('slugify', get_stylesheet_directory_uri() . '/modules/slugify/assets/js/converter.js');
    }

    /*---------------------------------------------------------------------------------------------
	-- Page Sections
	--------------------------------------------------------------------------------------------- */

    public function setup_converter_page()
    {
        add_management_page(
            __('Slug Converter', 'sadovod'),
            __('Transliteration of post slugs', 'sadovod'),
            'manage_options',
            'slugify',
            [$this, 'converter_body']
        );
    }

    /**
     * Setup settings sections.
     */
    public function setup_sections()
    {

        foreach ($this->form_fields as $form_field) {

            $title = isset($form_field['title']) ? $form_field['title'] : '';
            add_settings_section(
                $form_field['section'],
                $title,
                [$this, 'converter_intro'],
                'slugify'
            );
        }
    }

    /**
     * Setup settings fields.
     */
    public function setup_fields()
    {
        if (!$this->is_options_screen()) {
            return;
        }

        register_setting('slugify_group', 'slugify_settings');

        foreach ($this->form_fields as $key => $field) {
            $field['field_id'] = $key;

            add_settings_field(
                $key,
                $field['label'],
                [$this, 'field_callback'],
                'slugify',
                $field['section'],
                $field
            );
        }
    }


    /*---------------------------------------------------------------------------------------------
	-- Page Content
	--------------------------------------------------------------------------------------------- */

    /**
     * Output convert confirmation popup.
     */
    public function setup_converter_notif()
    {
        if (!$this->is_options_screen()) {
            return;
        }

?>
        <div id="slugify-confirm-popup">
            <div id="slugify-confirm-content">
                <p>
                    <strong><?php esc_html_e('Important:', 'sadovod'); ?></strong>
                    <?php
                    esc_html_e(
                        'This operation is irreversible. Please make sure that you have made a backup copy of your database.',
                        'sadovod'
                    );
                    ?>
                </p>
                <p>
                    <?php
                    esc_html_e(
                        'Also, you have to make a copy of your media files if the attachment post type is selected for
				conversion.',
                        'sadovod'
                    );
                    ?>
                </p>
                <p>
                    <?php
                    printf(
                        __(
                            'Upon conversion of attachments, please <a href="%s" aria-label="Open Thumbnail Tool page" target="_blank">regenerate thumbnails.</a>',
                            'sadovod'
                        ),
                        admin_url('tools.php?page=thumbnaily')
                    );

                    ?>
                </p>
                <p><?php esc_html_e('Are you sure to continue?', 'sadovod'); ?></p>
                <div id="slugify-confirm-buttons">
                    <input type="button" id="slugify-confirm-ok" class="button button-primary" value="<?php esc_html_e('OK', 'sadovod'); ?>">
                    <button type="button" id="slugify-confirm-cancel" class="button button-secondary">
                        <?php esc_html_e('Cancel', 'sadovod'); ?>
                    </button>
                </div>
            </div>
        </div>
    <?php
    }

    public function converter_intro()
    {
    ?>
        <p>
            <?php
            echo wp_kses_post(
                __(
                    'On this page you can choose post types which slugs you want to be transliterated.',
                    'sadovod'
                )
            );
            ?>
        </p>
    <?php
    }

    /**
     * Show converter page.
     */
    public function converter_body()
    {

    ?>

        <div class="wrap">
            <h1>
                <?php
                esc_html_e('Transliteration Options', 'sadovod');
                ?>
            </h1>

            <form id="slugify-options" action="" method="post">
                <input type="hidden" name="slug-convert" <?php
                                                            settings_fields('slugify_group'); // Hidden protection fields.
                                                            do_settings_sections('slugify'); // Sections with options.				
                                                            submit_button(__('Convert Existing Slugs', 'sadovod'), 'secondary', 'slugify-convert-button');
                                                            ?> </form>
        </div>
        <?php
    }


    /* ---------------------------------------------------------------------------------------------
	-- Setup toolbox
	--------------------------------------------------------------------------------------------- */

    public function setup_slugify_toolbox()
    {

        if (current_user_can('import')) :
        ?>
            <div class="card">
                <h2 class="title"><?= __('Slug Transliteration and Optimization', 'sadovod'); ?></h2>
                <p>
                    <?php
                    printf(
                        /* translators: %s: URL to Import screen. */
                        __('Used to convert cyrillic post slugs to latin or generate it automatically. It`s available at submenu <a href="%s">Slug Converter</a>.', 'sadovod'),
                        'tools.php?page=slugify'
                    );
                    ?>
                </p>
            </div>
<?php
        endif;
    }

    /* ---------------------------------------------------------------------------------------------
	-- Usefull Functions
	--------------------------------------------------------------------------------------------- */

    /**
     * Is current admin screen the plugin options screen.
     *
     * @return bool
     */
    protected function is_options_screen()
    {
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $current_screen = get_current_screen();
        return $current_screen && ('tools_page_slugify' === $current_screen->id);
    }

    /**
     * Get the form fields after initialization.
     *
     * @return array of options
     */
    protected function form_fields()
    {
        if (empty($this->form_fields)) {
            $this->init_form_fields();
        }

        return array_map([$this, 'set_defaults'], $this->form_fields);
    }

    /**
     * Set default required properties for each field.
     *
     * @param array $field Settings field.
     *
     * @return array
     */
    protected function set_defaults($field)
    {
        if (!isset($field['default'])) {
            $field['default'] = '';
        }

        return $field;
    }

    /**
     * Output settings field.
     *
     * @param array $arguments Field arguments.
     */
    public function field_callback(array $arguments)
    {
        if (!isset($arguments['field_id'])) {
            return;
        }

        $types = [
            'text'     => 'print_text_field',
            'password' => 'print_text_field',
            'number'   => 'print_number_field',
            'textarea' => 'print_text_area_field',
            'checkbox' => 'print_check_box_field',
            'radio'    => 'print_radio_field',
            'select'   => 'print_select_field',
            'multiple' => 'print_multiple_select_field',
            'table'    => 'print_table_field',
        ];

        $type = $arguments['type'];

        if (!array_key_exists($type, $types)) {
            return;
        }

        // If there is help text.
        $helper = $arguments['helper'];
        if ($helper) {
            printf(
                '<span class="helper"><span class="helper-content">%s</span></span>',
                wp_kses_post($helper)
            );
        }

        $this->{$types[$type]}($arguments);

        // If there is supplemental text.
        $supplemental = $arguments['supplemental'];
        if ($supplemental) {
            printf('<p class="description">%s</p>', wp_kses_post($supplemental));
        }
    }

    /**
     * Print checkbox field.
     *
     * @param array $arguments Field arguments.
     *
     * @noinspection PhpUnusedPrivateMethodInspection
     * @noinspection HtmlUnknownAttribute
     */
    private function print_check_box_field(array $arguments)
    {
        $value = (array) $this->get($arguments['field_id']);

        if (empty($arguments['options']) || !is_array($arguments['options'])) {
            $arguments['options'] = ['yes' => ''];
        }

        $options_markup = '';
        $iterator       = 0;
        foreach ($arguments['options'] as $key => $label) {
            $iterator++;
            $checked = false;
            if (is_array($value) && in_array($key, $value, true)) {
                $checked = checked($key, $key, false);
            }
            $options_markup .= sprintf(
                '<label for="%2$s_%7$s">' .
                    '<input id="%2$s_%7$s" name="%1$s[%2$s][]" type="%3$s" value="%4$s" %5$s ' .
                    ' %6$s' .
                    '</label>' .
                    '<br',
                'translit_settings',
                $arguments['field_id'],
                $arguments['type'],
                $key,
                $checked,
                $label,
                $iterator
            );
        }

        printf(
            '<fieldset>%s</fieldset>',
            wp_kses(
                $options_markup,
                [
                    'label' => [
                        'for' => [],
                    ],
                    'input' => [
                        'id'      => [],
                        'name'    => [],
                        'type'    => [],
                        'value'   => [],
                        'checked' => [],
                    ],
                    'br'    => [],
                ]
            )
        );
    }

    /**
     * Get plugin option.
     *
     * @param string $key         Setting name.
     * @param mixed  $empty_value Empty value for this setting.
     *
     * @return string|array The value specified for the option or a default value for the option.
     */
    public function get($key, $empty_value = null)
    {
        if (empty($this->settings)) {
            $this->init_settings();
        }

        // Get option default if unset.
        if (!isset($this->settings[$key])) {
            $form_fields            = $this->form_fields();
            $this->settings[$key] = isset($form_fields[$key]) ? $this->field_default($form_fields[$key]) : '';
        }

        if ('' === $this->settings[$key] && !is_null($empty_value)) {
            $this->settings[$key] = $empty_value;
        }

        return $this->settings[$key];
    }

    /**
     * Get a field default value. Defaults to '' if not set.
     *
     * @param array $field Setting field default value.
     *
     * @return string
     */
    protected function field_default(array $field)
    {
        return empty($field['default']) ? '' : $field['default'];
    }
}
