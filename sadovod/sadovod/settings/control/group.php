<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_Customize_Control'))
    return NULL;

class WP_Customize_Group extends WP_Customize_Section
{

    /**
     * Customize control type.
     *
     * @since 4.9.0
     * @var string
     */
    public $type = 'group';

    /**
     * Constructor.
     *
     * Any supplied $args override class property defaults.
     *
     * @since 3.4.0
     *
     * @param WP_Customize_Manager $manager Customizer bootstrap instance.
     * @param string               $id      A specific ID of the group.
     * @param array                $args    {
     *     Optional. Array of properties for the new Group object. Default empty array.
     *
     *     @type int             $priority           Priority of the group, defining the display order
     *                                               of panels and sections. Default 160.
     *     @type string          $panel              The panel this group belongs to (if any).
     *                                               Default empty.
     *     @type string          $capability         Capability required for the group.
     *                                               Default 'edit_theme_options'
     *     @type string|string[] $theme_supports     Theme features required to support the group.
     *     @type string          $title              Title of the group to show in UI.
     *     @type string          $description        Description to show in the UI.
     *     @type string          $type               Type of the group.
     *     @type callable        $active_callback    Active callback.
     *     @type bool            $description_hidden Hide the description behind a help icon,
     *                                               instead of inline above the first control.
     *                                               Default false.
     * }
     */
    public function __construct($manager, $id, $args = array())
    {
        parent::__construct($manager, $id, $args);
        add_action('customize_controls_enqueue_scripts', array($this, 'enqueue'));
    }

    /**
     * Enqueue control related scripts/styles.
     *
     * @since 3.4.0
     */
    public function enqueue()
    {
        register_inline_style('section-group', null, "
            .in-sub-group #customize-theme-controls .customize-pane-parent{transform: translateX(-200%);}

            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group{padding: 0;}
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group .customize-section-title{margin: 0;}
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group .customize-section-title .customize-action{line-height: 1.8;}
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group .customize-section-title .customize-section-back{height: 72px}
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group .customize-section-title h3{margin-left: 48px; padding: 10px 10px 11px 14px;}
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group .accordion-section-title{border-color: #23282d; border-bottom-color: #23282d !important; color: #bbc8d4;}
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group .accordion-section-title:after{color: #787c82}
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group .accordion-section-title:hover,
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group:last-of-type .accordion-section-title:hover{border-color: #3858e9; border-bottom-color: #32373c !important;  color: #3858e9}
            #customize-theme-controls .customize-pane-child.accordion-section-content.control-section-group .accordion-section-title:hover:after{color: #3858e9}
        ");

        //-------------------------------------------------------

        register_inline_script('section-group', array('customize-views'), "
            (function( exports, $ ){
                var api = wp.customize;

                /**
                 * Class wp.customize.Group.
                 *
                 * @since 4.9.0
                 * @class    wp.customize.Group
                 * @augments wp.customize.Control
                 */
                api.Group = wp.customize.Section.extend(/** @lends wp.customize.Group.prototype */{
                    containerType: 'group',

                    isContextuallyActive: function() {
                        if (
                            'group' !== this.params.type
                        ) {
                            return api.Section.prototype.isContextuallyActive.call( this );
                        }

                        var section = this;
                        var children = this._children( 'section', 'control' );

                        api.section.each( function( child ) {
                            if ( ! child.params.group ) {
                                return;
                            }

                            if ( child.params.group !== section.id ) {
                                return;
                            }

                            children.push( child );
                        });

                        children.sort( api.utils.prioritySort );

                        var activeCount = 0;
                        _( children ).each( function ( child ) {
                            if ( 'undefined' !== typeof child.isContextuallyActive ) {
                                if ( child.active() && child.isContextuallyActive() ) {
                                    activeCount += 1;
                                }
                            } else {
                                if ( child.active() ) {
                                    activeCount += 1;
                                }
                            }
                        });

                        return ( activeCount !== 0 );
                    }
                });

                $.extend( api.sectionConstructor, {
                    group: api.Group
                } );

            })( wp, jQuery );
        ");
    }

    public function print_template()
    {
?>
        <script type="text/html" id="tmpl-customize-<?php echo $this->type; ?>-default">
            <?php $this->render_template(); ?>
        </script>
<?php
    }
}


function customizer_group_init(WP_Customize_Manager $wp_customize)
{
    $wp_customize->register_section_type('WP_Customize_Group');
}
add_action('customize_register', 'customizer_group_init', 1);
