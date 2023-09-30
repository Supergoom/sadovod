<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_Customize_Control'))
    return NULL;

class WP_Customize_Group_Item extends WP_Customize_Section
{

    /**
     * Customize control type.
     *
     * @since 4.9.0
     * @var string
     */
    public $type = 'group_item';

    /**
     * Parent group id.
     *
     * @since 4.9.0
     * @var string
     */
    public $group;

    /**
     * Constructor.
     *
     * Any supplied $args override class property defaults.
     *
     * @since 3.4.0
     *
     * @param WP_Customize_Manager $manager Customizer bootstrap instance.
     * @param string               $id      A specific ID of the section.
     * @param array                $args    {
     *     Optional. Array of properties for the new Section object. Default empty array.
     *
     *     @type int             $priority           Priority of the section, defining the display order
     *                                               of panels and sections. Default 160.
     *     @type string          $panel              The panel this section belongs to (if any).
     * 
     *     @type string          $group              The group this section belongs to (if any).
     *                                               Default empty.
     *     @type string          $capability         Capability required for the section.
     *                                               Default 'edit_theme_options'
     *     @type string|string[] $theme_supports     Theme features required to support the section.
     *     @type string          $title              Title of the section to show in UI.
     *     @type string          $description        Description to show in the UI.
     *     @type string          $type               Type of the section.
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
        register_inline_style('section-group-item', null, "
            #customize-theme-controls .customize-pane-parent .control-section-group_item{display: none !important;}
            .in-sub-group #customize-theme-controls .customize-pane-child:not(.control-section-group_item){transform: translateX(-100%); }

            #customize-theme-controls .customize-pane-child.control-section-group_item .customize-section-title{display: flex; flex-wrap: wrap; align-items: center;}
            #customize-theme-controls .customize-pane-child.control-section-group_item .customize-section-title .customize-section-back{width: 16%;}
            #customize-theme-controls .customize-pane-child.control-section-group_item .customize-section-title h3{width: 84%}
            #customize-theme-controls .customize-pane-child.control-section-group_item .customize-section-title .customize-action{white-space: normal; line-height: 1.2; margin-bottom: 5px;}
            #customize-theme-controls .customize-pane-child.control-section-group_item .customize-section-title .customize-control-notifications-container{width: 100%;}
        ");

        //-------------------------------------------------------

        register_inline_script('section-group-item', array('customize-views'), "
            (function( exports, $ ){
                var api = wp.customize;

                //--------------------------------
                api.state.create( 'expandedGroup' );

                /**
                 * Class wp.customize.GroupItem.
                 *
                 * @since 4.9.0
                 * @class    wp.customize.GroupItem
                 * @augments wp.customize.Control
                 */
                api.GroupItem = wp.customize.Section.extend(/** @lends wp.customize.GroupItem.prototype */{
                    containerType: 'group_item',

                    /**
                     * @constructs wp.customize.GroupItem
                     * @augments   wp.customize~Container
                     *
                     * @since 4.1.0
                     *
                     * @param {string}  id - The ID for the Group Item.
                     * @param {Object}  options - Options.
                     * @param {string}  options.title - Title shown when item is collapsed and expanded.
                     * @param {string}  [options.description] - Description shown at the top of the item.
                     * @param {number}  [options.priority=100] - The sort priority for the item.
                     * @param {string}  [options.type=default] - The type of the group item. See wp.customize.sectionConstructor.
                     * @param {string}  [options.content] - The markup to be used for the item container. If empty, a JS template is used.
                     * @param {boolean} [options.active=true] - Whether the item is active or not.
                     * @param {string}  options.panel - The ID for the panel this section is associated with.
                     * @param {string}  options.group - The ID for the group this this is associated with.
                     * @param {string}  [options.customizeAction] - Additional context information shown before the item title when expanded.
                     * @param {Object}  [options.params] - Deprecated wrapper for the above properties.
                     */
                    initialize: function ( id, options ) {
                        var groupItem = this, param;
                        params = options.params || options;

                        // Look up the type if one was not supplied.
                        if ( ! params.type ) {
                            _.find( api.panelConstructor, function( Constructor, type ) {
                                if ( Constructor === groupItem.constructor ) {
                                    params.type = type;
                                    return true;
                                }
                                return false;
                            } );
                        }
                        
                        groupItem.group = new api.Value();
                        groupItem.group.bind( function ( id ) {
                            $( groupItem.headContainer ).toggleClass( 'control-subsection', !! id );
                        });
                        groupItem.group.set( params.group || '' );
                        api.utils.bubbleChildValueChanges( groupItem, [ 'group' ] );

                        api.Section.prototype.initialize.call( groupItem, id, params );
                    },

                    /**
                     * Embed the container in the DOM when any parent group is ready.
                     *
                     * @since 4.1.0
                     */
                    embed: function () {
                        var groupItem = this;

                        if (
                            'group_item' !== groupItem.params.type ||
                            'undefined' === typeof groupItem.group
                        ) 
                            return;

                        groupItem.containerParent = api.ensure( groupItem.containerParent );

                        // Watch for changes to the section state.
                        var inject = function ( groupId ) {
                            var parentContainer;

                            if ( groupId ) {
                                // The section has been supplied, so wait until the panel object is registered.
                                api.section( groupId, function ( group ) {
                                    // The section has been registered, wait for it to become ready/initialized.
                                    group.deferred.embedded.done( function () {
                                        parentContainer = group.contentContainer;
                                        if ( ! groupItem.headContainer.parent().is( parentContainer ) ) {
                                            parentContainer.append( groupItem.headContainer );
                                        }
                                        if ( ! groupItem.contentContainer.parent().is( groupItem.headContainer ) ) {
                                            groupItem.containerParent.append( groupItem.contentContainer );
                                        }
                                        groupItem.deferred.embedded.resolve();
                                    });
                                } );
                            } else {
                                // There is no section, so embed the groupItem in the root of the customizer.
                                parentContainer = api.ensure( groupItem.containerPaneParent );
                                if ( ! groupItem.headContainer.parent().is( parentContainer ) ) {
                                    parentContainer.append( groupItem.headContainer );
                                }
                                if ( ! groupItem.contentContainer.parent().is( groupItem.headContainer ) ) {
                                    groupItem.containerParent.append( groupItem.contentContainer );
                                }
                                groupItem.deferred.embedded.resolve();
                            }
                        };

                        groupItem.group.bind( inject );
                        inject( groupItem.group.get() ); // Since a section may never get a group, assume that it won't ever get one.
                    },

                    attachEvents: function() {
                        var groupItem = this,
                            groupId = groupItem.group.get(),
                            group = api.section( groupId );

                        api.Section.prototype.attachEvents.call( groupItem );

                        if (
                            'group_item' !== groupItem.params.type ||
                            'undefined' === typeof groupItem.group
                        ) {                            
                            return;
                        }

                        groupItem.container.find( '.customize-section-back' )
                            .off( 'click keydown' )
                            .on( 'click keydown', function( event ) {
                                if ( api.utils.isKeydownButNotEnterEvent( event ) )
                                    return;

                                event.preventDefault(); // Keep this AFTER the key filter above

                                if ( groupItem.expanded() ) {
                                    groupItem.collapse();
                                    group.expand();
                                }                                
                            });

                        // Move back group item title, whenever a reflow happens.
                        api.bind( 'pane-contents-reflowed', function () {
                            if ( ! groupItem.headContainer.parent().is( group.contentContainer ) ) {
                                group.contentContainer.append( groupItem.headContainer );
                            }
                            if ( ! groupItem.contentContainer.parent().is( groupItem.headContainer ) ) {
                                groupItem.containerParent.append( groupItem.contentContainer );
                            }
                        });
                    },

                    onChangeExpanded: function ( expanded, args ) {
                        var groupItem = this,
                            groupId = groupItem.group.get(),
                            group = api.section( groupId );

                        if (
                            'group_item' !== groupItem.params.type ||
                            'undefined' === typeof groupItem.group
                        ) {                   
                            api.Section.prototype.onChangeExpanded.call( groupItem, expanded, args );         
                            return;
                        }

                        if ( args.unchanged ) {
                            if ( args.completeCallback ) {
                                args.completeCallback();
                            }
                            return;
                        }

                        var expand,
                            content = groupItem.contentContainer,
				            overlay = content.closest( '.wp-full-overlay' ),
                            container = groupItem.headContainer.closest( '.wp-full-overlay-sidebar-content' ),
                            backBtn = content.find( '.customize-section-back' ),
				            groupItemTitle = groupItem.headContainer.find( '.accordion-section-title' ).first();

                        if ( expanded && ! content.hasClass( 'open' ) ) {

                            if ( args.unchanged ) {
                                expand = args.completeCallback;
                            } else {
                                expand = function() {
                                    groupItem._animateChangeExpanded( function() {
                                        groupItemTitle.attr( 'tabindex', '-1' );
                                        backBtn.attr( 'tabindex', '0' );

                                        backBtn.trigger( 'focus' );
                                        content.css( 'top', '' );
                                        container.scrollTop( 0 );

                                        if ( args.completeCallback ) {
                                            args.completeCallback();
                                        }
                                    } );

                                    content.addClass( 'open' );
                                    overlay.addClass( 'in-sub-group' );
                                    api.state( 'expandedGroup' ).set( groupItem );
                                }.bind( this );
                            }

                            if ( group ) {
                                group.expand({
                                    duration: args.duration,
                                    completeCallback: expand
                                });
                            }

                        } else if ( ! expanded && content.hasClass( 'open' ) ) {
                            if ( group ) {
                                if ( group.contentContainer.hasClass( 'skip-transition' ) ) {
                                    group.collapse();
                                }
                            }
                            groupItem._animateChangeExpanded( function() {
                                backBtn.attr( 'tabindex', '-1' );
                                groupItemTitle.attr( 'tabindex', '0' );

                                groupItemTitle.trigger( 'focus' );
                                content.css( 'top', '' );

                                if ( args.completeCallback ) {
                                    args.completeCallback();
                                }
                            } );

                            content.removeClass( 'open' );
                            overlay.removeClass( 'in-sub-group' );
                            if ( groupItem === api.state( 'expandedGroup' ).get() ) {
                                api.state( 'expandedGroup' ).set( false );
                            }

                        } else {
                            if ( args.completeCallback ) {
                                args.completeCallback();
                            }
                        }

                    },
                });

                $.extend( api.sectionConstructor, {
                    group_item: api.GroupItem
                } );

                var areElementListsEqual = api.utils.areElementListsEqual;
                api.utils.areElementListsEqual = function ( listA, listB ) {
                    var appendContainer = '#customize-theme-controls .customize-pane-parent';
                    listA = _.filter(listA, function(item){
                        return item.parent(appendContainer).length;
                    });

                    var themesId = 'accordion-section-themes';
                    listB = _.without(listB, _.findWhere(listB, {
                        id: themesId
                    }));

                    return areElementListsEqual(listA, listB);                    
                };

                api.state.create( 'expandedGroup' );

            })( wp, jQuery );
        ");
    }

    public function json()
    {
        $data = parent::json();

        $data['group'] = $this->group;

        $data['customizeAction'] = __('Customizing');
        if ($this->group) {
            $section = $this->manager->get_section($this->group);
            if (isset($section)) {
                $panel = $this->manager->get_panel($section->panel);

                /* translators: %s: Panel title in the Customizer. &#9656; is the unicode right-pointing triangle. %s: Section title in the Customizer. */
                $data['customizeAction'] = sprintf(__('<span>%s</span> &#9656; <span>%s</span>'), esc_html($panel->title), esc_html($section->title));
            }
        }

        return $data;
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


function customizer_group_item_init(WP_Customize_Manager $wp_customize)
{
    $wp_customize->register_section_type('WP_Customize_Group_Item');
}
add_action('customize_register', 'customizer_group_item_init', 1);
