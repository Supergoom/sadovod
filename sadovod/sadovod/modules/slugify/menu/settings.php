<?php

/**
 * Settings class file.
 *
 * @package cyr-to-lat
 */

namespace Cyr_To_Lat\Settings;


/**
 * Class Settings
 *
 * Central point to get settings from.
 */
class Settings implements SettingsInterface
{

    /**
     * Menu pages class instances.
     *
     * @var array
     */
    protected $menu_pages = [];

    /**
     * Screen ids of pages and tabs.
     *
     * @var array
     */
    private $screen_ids = [];

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /*---------------------------------------------------------------------------------------------
	-- Page Init
	--------------------------------------------------------------------------------------------- */

    /**
     * Init class.
     *
     * @noinspection UnnecessaryCastingInspection
     */
    protected function init()
    {

        $this->init_form_fields();
        $this->init_settings();

        if ($this->is_tab_active($this)) {
            $this->init_hooks();
        }

        // Allow to specify MENU_PAGES item as one class, not an array.
        $menu_pages = (array) self::MENU_PAGES;

        foreach ($menu_pages as $menu_page) {
            $tab_classes = (array) $menu_page;

            // Allow to specify menu page as one class, without tabs.
            $page_class  = $tab_classes[0];
            $tab_classes = array_slice($tab_classes, 1);

            $tabs = [];
            foreach ($tab_classes as $tab_class) {
                /**
                 * Tab.
                 *
                 * @var PluginSettingsBase $tab
                 */
                $tab                = new $tab_class(null);
                $tabs[]             = $tab;
                $this->screen_ids[] = $tab->screen_id();
            }

            /**
             * Page.
             *
             * @var PluginSettingsBase $page_class
             */
            $this->menu_pages[] = new $page_class($tabs);
        }
    }

    /**
     * Init class hooks.
     */
    protected function init_hooks()
    {
        add_action('in_admin_header', [$this, 'in_admin_header']);
        add_action('init', [$this, 'delayed_init_settings'], PHP_INT_MAX);

        add_action('plugins_loaded', [$this, 'load_plugin_textdomain']);

        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('current_screen', [$this, 'setup_sections']);
        add_action('current_screen', [$this, 'setup_fields']);

        add_filter('pre_update_option_' . $this->option_name(), [$this, 'pre_update_option_filter'], 10, 2);

        add_action('admin_enqueue_scripts', [$this, 'base_admin_enqueue_scripts']);

        if (!$this->is_tab()) {
            add_action('current_screen', [$this, 'setup_tabs_section'], 9);
        }
    }

    /**
     * Empty method. Do stuff in the delayed_init_form_fields.
     */
    public function init_form_fields()
    {
        $this->form_fields = [];

        $default_post_types = ['post', 'page', 'nav_menu_item'];

        $post_types = $default_post_types;

        $filtered_post_types = array_filter((array) apply_filters('ctl_post_types', $post_types));

        $this->form_fields['background_post_types'] = [
            'label'        => __('Post Types', 'cyr2lat'),
            'section'      => 'background_section',
            'type'         => 'checkbox',
            'placeholder'  => '',
            'helper'       => __('Post types included in the conversion.', 'cyr2lat'),
            'supplemental' => '',
            'options'      => [],
        ];

        foreach ($post_types as $post_type) {
            $label = $post_type;

            $this->form_fields['background_post_types']['options'][$post_type] = $label;
        }

        $this->form_fields['background_post_types']['default'] = $default_post_types;
        // @todo Mark as disabled.
        $this->form_fields['background_post_types']['disabled'] = array_diff($default_post_types, $filtered_post_types);

        $default_post_statuses = ['publish', 'future', 'private'];
        $post_statuses         = ['publish', 'future', 'private', 'draft', 'pending'];

        $this->form_fields['background_post_statuses'] = [
            'label'        => __('Post Statuses', 'cyr2lat'),
            'section'      => 'background_section',
            'type'         => 'checkbox',
            'placeholder'  => '',
            'helper'       => __('Post statuses included in the conversion.', 'cyr2lat'),
            'supplemental' => '',
            'options'      => [],
        ];

        foreach ($post_statuses as $post_status) {
            $label = $post_status;

            $this->form_fields['background_post_statuses']['options'][$post_status] = $label;
        }

        $this->form_fields['background_post_statuses']['default'] = $default_post_statuses;
    }

    /**
     * Get convertible post types.
     *
     * @return array
     */
    public static function get_convertible_post_types()
    {
        global $all_posts;

        $post_types = array_merge(
            $all_posts,
            array(
                (object)array('label' => 'nav_menu_item', 'name' => 'nav_menu_item')
            )
        );

        return array_merge($post_types, ['nav_menu_item' => 'nav_menu_item']);
    }

    /**
     * Init form fields.
     */
    public function delayed_init_form_fields()
    {
        $post_types = self::get_convertible_post_types();

        $filtered_post_types = array_filter((array) apply_filters('ctl_post_types', $post_types));

        $this->form_fields['background_post_types']['options'] = [];

        foreach ($post_types as $post_type) {
            $label = $post_type;

            $this->form_fields['background_post_types']['options'][$post_type] = $label;
        }

        // @todo Mark as disabled.
        $this->form_fields['background_post_types']['disabled'] = array_diff(
            $this->form_fields['background_post_types']['default'],
            $filtered_post_types
        );
    }

    /**
     * Init form fields and settings late, on 'init' hook with PHP_INT_MAX priority,
     * to allow all plugins to register post types.
     */
    public function delayed_init_settings()
    {
        $this->delayed_init_form_fields();

        $this->init_settings();
    }

    /*---------------------------------------------------------------------------------------------
	-- Settings Fields
	--------------------------------------------------------------------------------------------- */

    protected function init_settings()
    {
        $this->settings = get_option($this->option_name(), null);

        $form_fields = $this->form_fields();

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

        wp_enqueue_style('slugify', dirname(__FILE__) . '/assets/css/slugify.css');
        wp_enqueue_script('slugify', dirname(__FILE__) . '/assets/js/slugify.js');
    }

    /*---------------------------------------------------------------------------------------------
	-- Page Sections
	--------------------------------------------------------------------------------------------- */

    public function add_settings_page()
    {
        add_menu_page(
            $this->page_title(),
            $this->menu_title(),
            'manage_options',
            'tools.php',
            [$this, 'settings_base_page']
        );

        return;
    }

    /**
     * Setup settings sections.
     */
    public function setup_sections()
    {
        $tab = $this->get_active_tab();

        foreach ($this->form_fields as $form_field) {
            $title = isset($form_field['title']) ? $form_field['title'] : '';
            add_settings_section(
                $form_field['section'],
                $title,
                [$tab, 'section_callback'],
                $tab->option_page()
            );
        }
    }

    /**
     * Setup tabs section.
     */
    public function setup_tabs_section()
    {
        /**
         * Protection from the bug in \Automattic\Jetpack\Sync\Sender::get_items_to_send(),
         * which sets screen without loading of wp-admin/includes/template.php,
         * where add_settings_section() is defined.
         */
        if (!function_exists('add_settings_section')) {
            return;
        }

        $tab = $this->get_active_tab();

        add_settings_section(
            'tabs_section',
            '',
            [$this, 'tabs_callback'],
            $tab->option_page()
        );
    }

    /*---------------------------------------------------------------------------------------------
	-- Page Content
	--------------------------------------------------------------------------------------------- */

    /**
     * Show settings page.
     */
    public function settings_page()
    {
?>
        <div class="wrap">
            <h1>
                <?php
                esc_html_e('Cyr To Lat Plugin Options', 'cyr2lat');
                ?>
            </h1>

            <form id="ctl-options" action="<?php echo esc_url(admin_url('options.php')); ?>" method="post">
                <?php
                do_settings_sections($this->option_page()); // Sections with options.
                settings_fields($this->option_group()); // Hidden protection fields.
                submit_button();
                ?>
            </form>

            <form id="ctl-convert-existing-slugs" action="" method="post">
                <input type="hidden" name="ctl-convert" <?php
                                                        sadovod_nonce_field(self::NONCE);
                                                        submit_button(__('Convert Existing Slugs', 'cyr2lat'), 'secondary', 'ctl-convert-button');
                                                        ?> </form>
        </div>
        <?php
    }

    public function section_callback($arguments)
    {
        if ('background_section' === $arguments['id']) {
        ?>
            <h2 class="title">
                <?php
                esc_html_e('Existing Slugs Conversion Settings', 'cyr2lat');
                ?>
            </h2>
            <p>
                <?php
                echo wp_kses_post(
                    __(
                        'Existing <strong>product attribute</strong> slugs will <strong>NOT</strong> be converted.',
                        'cyr2lat'
                    )
                );
                ?>
            </p>
        <?php
        }
    }

    /**
     * Output convert confirmation popup.
     */
    public function in_admin_header()
    {
        if (!$this->is_options_screen()) {
            return;
        }

        ?>
        <div id="ctl-confirm-popup">
            <div id="ctl-confirm-content">
                <p>
                    <strong><?php esc_html_e('Important:', 'cyr2lat'); ?></strong>
                    <?php
                    esc_html_e(
                        'This operation is irreversible. Please make sure that you have made a backup copy of your database.',
                        'cyr2lat'
                    );
                    ?>
                </p>
                <p>
                    <?php
                    esc_html_e(
                        'Also, you have to make a copy of your media files if the attachment post type is selected for
				conversion.',
                        'cyr2lat'
                    );
                    ?>
                </p>
                <p>
                    <?php
                    esc_html_e(
                        'Upon conversion of attachments, please regenerate thumbnails.',
                        'cyr2lat'
                    );
                    ?>
                </p>
                <p><?php esc_html_e('Are you sure to continue?', 'cyr2lat'); ?></p>
                <div id="ctl-confirm-buttons">
                    <input type="button" id="ctl-confirm-ok" class="button button-primary" value="<?php esc_html_e('OK', 'cyr2lat'); ?>">
                    <button type="button" id="ctl-confirm-cancel" class="button button-secondary">
                        <?php esc_html_e('Cancel', 'cyr2lat'); ?>
                    </button>
                </div>
            </div>
        </div>
<?php
    }



    /* ---------------------------------------------------------------------------------------------
	-- Usefull Functions
	--------------------------------------------------------------------------------------------- */

    /**
     * Get screen ids of all settings pages and tabs.
     *
     * @return array
     */
    public function screen_ids()
    {
        return $this->screen_ids;
    }

    /**
     * Get transliteration table.
     *
     * @return array
     */
    public function get_table()
    {
        // List of locales: https://make.wordpress.org/polyglots/teams/.
        $locale = (string) apply_filters('ctl_locale', get_locale());
        $table  = $this->get($locale);
        if (empty($table)) {
            $table = $this->get('iso9');
        }

        return $this->transpose_chinese_table($table);
    }

    /**
     * Is current locale a Chinese one.
     *
     * @return bool
     */
    public function is_chinese_locale()
    {
        $chinese_locales = ['zh_CN', 'zh_HK', 'zh_SG', 'zh_TW'];

        return in_array(get_locale(), $chinese_locales, true);
    }

    /**
     * Transpose Chinese table.
     *
     * Chinese tables are stored in different way, to show them compact.
     *
     * @param array $table Table.
     *
     * @return array
     */
    protected function transpose_chinese_table($table)
    {
        if (!$this->is_chinese_locale()) {
            return $table;
        }

        $transposed_table = [];
        foreach ($table as $key => $item) {
            $hieroglyphs = Mbstring::mb_str_split($item);
            foreach ($hieroglyphs as $hieroglyph) {
                $transposed_table[$hieroglyph] = $key;
            }
        }

        return $transposed_table;
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
                esc_html($this->option_name()),
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
}
