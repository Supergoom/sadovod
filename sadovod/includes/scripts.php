<?php

class WP_Locale_Scripts extends WP_Scripts
{

    private $type_attr;

    /**
     * Executes the parent class constructor and initialization, then copies in the 
     * pre-existing $wp_scripts contents
     */
    public function __construct()
    {
        if (
            function_exists('is_admin') && !is_admin()
            &&
            function_exists('current_theme_supports') && !current_theme_supports('html5', 'script')
        ) {
            $this->type_attr = " type='text/javascript'";
        }

        /**
         * Copy the contents of existing $wp_scripts into the new one.
         * This is needed for numerous plug-ins that do not play nice.
         *
         * https://wordpress.stackexchange.com/a/284495/198117
         */
        global $wp_scripts;
        if ($wp_scripts instanceof WP_Scripts) {
            $missing_scripts = array_diff_key($wp_scripts->registered, $this->registered);
            foreach ($missing_scripts as $mscript) {
                $this->registered[$mscript->handle] = $mscript;
            }
        }
    }

    /**
     * Adapted from wp-includes/class.wp-scripts.php and added the
     * filter `wp_filterable_script_extra_tag`
     *
     * @param string $handle
     * @param bool $echo
     *
     * @return bool|mixed|string|void
     */
    public function print_extra_script($handle, $echo = true)
    {
        $output = $this->get_data($handle, 'data');
        if (!$output) {
            return;
        }

        if (!$echo) {
            return $output;
        }


        $tag = sprintf("<script%s id='%s-js-extra'>\n", $this->type_attr, esc_attr($handle));

        /**
         * Filters the entire inline script tag.
         *
         * @param string $tag    <script type="text/javascript" id="plug-js-extra">...</script>
         * @param string $handle Script handle.
         */
        $tag = apply_filters('script_locale_loader_tag', $tag, $handle);

        // CDATA is not needed for HTML 5.
        if ($this->type_attr) {
            $tag .= "/* <![CDATA[ */\n";
        }

        $tag .= "$output\n";

        if ($this->type_attr) {
            $tag .= "/* ]]> */\n";
        }

        $tag .= "</script>\n";

        echo $tag;

        return true;
    }
}

function register_locale_scripts()
{
    global $wp_scripts;
    $wp_scripts = new WP_Locale_Scripts();
}
add_action('init', 'register_locale_scripts', 1000);
