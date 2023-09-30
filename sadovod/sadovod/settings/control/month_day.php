<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_Customize_Control'))
    return NULL;

class WP_Customize_Month_Day_Control extends WP_Customize_Control
{

    /**
     * Customize control type.
     *
     * @since 4.9.0
     * @var string
     */
    public $type = 'month_day';

    /**
     * Whether hours, minutes, and meridian should be shown.
     *
     * @since 4.9.0
     * @var bool
     */
    public $include_time = true;

    /**
     * If set to false the control will appear in 24 hour format,
     * the value will still be saved in Y-m-d H:i:s format.
     *
     * @since 4.9.0
     * @var bool
     */
    public $twelve_hour_format = true;

    /**
     * Don't render the control's content - it's rendered with a JS template.
     *
     * @since 4.9.0
     */
    public function render_content()
    {
    }

    /**
     * Enqueue control related scripts/styles.
     *
     * @since 3.4.0
     */
    public function enqueue()
    {
        register_inline_script('month-day', array('customize-views'), "
            (function( exports, $ ){
                var api = wp.customize;

                /**
                 * Class wp.customize.MonthDayControl.
                 *
                 * @since 4.9.0
                 * @class    wp.customize.MonthDayControl
                 * @augments wp.customize.Control
                 */
                api.MonthDayControl = api.Control.extend(/** @lends wp.customize.MonthDayControl.prototype */{

                    /**
                     * Default params.
                     *
                     * @since 4.9.0
                     * @var {object}
                     */
                    defaults: {
                        transport: 'postMessage',
                        dirty: false
                    },

                    preview: function preview() {
                        return;
                    },

                    /**
                     * Initialize behaviors.
                     *
                     * @since 4.9.0
                     * @return {void}
                     */
                    ready: function ready() {
                        var control = this;

                        control.inputElements = {};
                        control.invalidDate = false;

                        _.bindAll( control, 'populateSetting', 'updateDaysForMonth', 'populateDateInputs' );

                        if ( ! control.setting ) {
                            throw new Error( 'Missing setting' );
                        }

                        control.container.find( '.date-input' ).each( function() {
                            var input = $( this ), component, element;
                            component = input.data( 'component' );
                            element = new api.Element( input );
                            control.inputElements[ component ] = element;
                            control.elements.push( element );

                            // Add zero-padding when blurring field.
                            input.on( 'blur', _.debounce( function() {
                                if ( ! control.invalidDate ) {
                                    control.populateDateInputs();
                                }
                            } ) );
                        } );

                        control.inputElements.month.bind( control.updateDaysForMonth );
                        control.populateDateInputs();
                        control.setting.bind( control.populateDateInputs );

                        // Start populating setting after inputs have been populated.
                        _.each( control.inputElements, function( element ) {
                            element.bind( control.populateSetting );
                        } );
                    },

                    /**
                     * Parse MonthDay string.
                     *
                     * @since 4.9.0
                     *
                     * @param {string} MonthDay - Date/Time string. Accepts Y-m-d[ H:i[:s]] format.
                     * @return {Object|null} Returns object containing date components or null if parse error.
                     */
                    parseMonthDay: function parseMonthDay( MonthDay ) {
                        var control = this, matches, date, midDayHour = 12;

                        if ( MonthDay ) {
                            matches = MonthDay.match( /^(?:(\d\d\d\d)-)?(\d\d)-(\d\d)(?: (\d\d):(\d\d)(?::(\d\d))?)?$/ );
                        }
                        
                        if ( ! matches ) {
                            matches = ['01-01', '01', '01', null, null, null];
                        }

                        matches.shift(); //Match
                        matches.shift(); //Year

                        date = {                            
                            month: matches.shift(),  
                            day: matches.shift(),                         
                            hour: matches.shift() || '00',
                            minute: matches.shift() || '00',
                            second: matches.shift() || '00'
                        };

                        if ( control.params.includeTime && control.params.twelveHourFormat ) {
                            date.hour = parseInt( date.hour, 10 );
                            date.meridian = date.hour >= midDayHour ? 'pm' : 'am';
                            date.hour = date.hour % midDayHour ? String( date.hour % midDayHour ) : String( midDayHour );
                            delete date.second; // @todo Why only if twelveHourFormat?
                        }

                        return date;
                    },

                    /**
                     * Validates if input components have valid date and time.
                     *
                     * @since 4.9.0
                     * @return {boolean} If date input fields has error.
                     */
                    validateInputs: function validateInputs() {
                        var control = this, components, validityInput;

                        control.invalidDate = false;

                        components = [ 'day' ];
                        if ( control.params.includeTime ) {
                            components.push( 'hour', 'minute' );
                        }

                        _.find( components, function( component ) {
                            var element, max, min, value;

                            element = control.inputElements[ component ];
                            validityInput = element.element.get( 0 );
                            max = parseInt( element.element.attr( 'max' ), 10 );
                            min = parseInt( element.element.attr( 'min' ), 10 );
                            value = parseInt( element(), 10 );
                            control.invalidDate = isNaN( value ) || value > max || value < min;

                            if ( ! control.invalidDate ) {
                                validityInput.setCustomValidity( '' );
                            }

                            return control.invalidDate;
                        } );

                        if ( control.inputElements.meridian && ! control.invalidDate ) {
                            validityInput = control.inputElements.meridian.element.get( 0 );
                            if ( 'am' !== control.inputElements.meridian.get() && 'pm' !== control.inputElements.meridian.get() ) {
                                control.invalidDate = true;
                            } else {
                                validityInput.setCustomValidity( '' );
                            }
                        }

                        if ( control.invalidDate ) {
                            validityInput.setCustomValidity( api.l10n.invalidValue );
                        } else {
                            validityInput.setCustomValidity( '' );
                        }
                        if ( ! control.section() || api.section.has( control.section() ) && api.section( control.section() ).expanded() ) {
                            _.result( validityInput, 'reportValidity' );
                        }

                        return control.invalidDate;
                    },

                    /**
                     * Updates number of days according to the month selected.
                     *
                     * @since 4.9.0
                     * @return {void}
                     */
                    updateDaysForMonth: function updateDaysForMonth() {
                        var control = this, daysInMonth, month, day;

                        month = parseInt( control.inputElements.month(), 10 );
                        day = parseInt( control.inputElements.day(), 10 );

                        if ( month ) {
                            daysInMonth = control.getDaysInMonth(month);
                            control.inputElements.day.element.attr( 'max', daysInMonth );

                            if ( day > daysInMonth ) {
                                control.inputElements.day( String( daysInMonth ) );
                            }
                        }
                    },

                    /**
                     * Get number of days according to the month selected.
                     *
                     * @since 4.9.0
                     * @return {int}
                     */
                    getDaysInMonth: function(m, y){
                        if( /9|4|6|11/.test( m ) ) return 30;
                        if( m != 2 ) return 31;
                        return 29;
                    },

                    /**
                     * Populate setting value from the inputs.
                     *
                     * @since 4.9.0
                     * @return {boolean} If setting updated.
                     */
                    populateSetting: function populateSetting() {
                        var control = this, date;

                        if ( control.validateInputs() ) {
                            return false;
                        }

                        date = control.convertInputDateToString();
                        control.setting.set( date );
                        return true;
                    },

                    /**
                     * Converts input values to string in Y-m-d H:i:s format.
                     *
                     * @since 4.9.0
                     * @return {string} Date string.
                     */
                    convertInputDateToString: function convertInputDateToString() {
                        var control = this, date = '', dateFormat, hourInTwentyFourHourFormat,
                            getElementValue, pad;

                        pad = function( number, padding ) {
                            var zeros;
                            if ( String( number ).length < padding ) {
                                zeros = padding - String( number ).length;
                                number = Math.pow( 10, zeros ).toString().substr( 1 ) + String( number );
                            }
                            return number;
                        };

                        getElementValue = function( component ) {
                            var value = parseInt( control.inputElements[ component ].get(), 10 );

                            if ( _.contains( [ 'month', 'day', 'hour', 'minute' ], component ) ) {
                                value = pad( value, 2 );
                            }

                            return value;
                        };

                        dateFormat = ['0000', '-', 'month', '-', 'day' ];
                        if ( control.params.includeTime ) {
                            hourInTwentyFourHourFormat = control.inputElements.meridian ? control.convertHourToTwentyFourHourFormat( control.inputElements.hour(), control.inputElements.meridian() ) : control.inputElements.hour();
                            dateFormat = dateFormat.concat( [ ' ', pad( hourInTwentyFourHourFormat, 2 ), ':', 'minute', ':', '00' ] );
                        }

                        _.each( dateFormat, function( component ) {
                            date += control.inputElements[ component ] ? getElementValue( component ) : component;
                        } );

                        return date;
                    },

                    /**
                     * Convert hour in twelve hour format to twenty four hour format.
                     *
                     * @since 4.9.0
                     * @param {string} hourInTwelveHourFormat - Hour in twelve hour format.
                     * @param {string} meridian - Either 'am' or 'pm'.
                     * @return {string} Hour in twenty four hour format.
                     */
                    convertHourToTwentyFourHourFormat: function convertHour( hourInTwelveHourFormat, meridian ) {
                        var hourInTwentyFourHourFormat, hour, midDayHour = 12;

                        hour = parseInt( hourInTwelveHourFormat, 10 );
                        if ( isNaN( hour ) ) {
                            return '';
                        }

                        if ( 'pm' === meridian && hour < midDayHour ) {
                            hourInTwentyFourHourFormat = hour + midDayHour;
                        } else if ( 'am' === meridian && midDayHour === hour ) {
                            hourInTwentyFourHourFormat = hour - midDayHour;
                        } else {
                            hourInTwentyFourHourFormat = hour;
                        }

                        return String( hourInTwentyFourHourFormat );
                    },

                    /**
                     * Populates date inputs in date fields.
                     *
                     * @since 4.9.0
                     * @return {boolean} Whether the inputs were populated.
                     */
                    populateDateInputs: function populateDateInputs() {
                        var control = this, parsed;

                        parsed = control.parseMonthDay( control.setting.get() );

                        if ( ! parsed ) {
                            return false;
                        }

                        _.each( control.inputElements, function( element, component ) {
                            var value = parsed[ component ]; // This will be zero-padded string.

                            // Set month and meridian regardless of focused state since they are dropdowns.
                            if ( 'month' === component || 'meridian' === component ) {
                                // Options in dropdowns are not zero-padded.
                                value = value.replace( /^0/, '' );

                                element.set( value );
                            } else {

                                value = parseInt( value, 10 );
                                if ( ! element.element.is( document.activeElement ) ) {

                                    // Populate element with zero-padded value if not focused.
                                    element.set( parsed[ component ] );
                                } else if ( value !== parseInt( element(), 10 ) ) {

                                    // Forcibly update the value if its underlying value changed, regardless of zero-padding.
                                    element.set( String( value ) );
                                }
                            }
                        } );

                        return true;
                    }
                
                });

                $.extend( api.controlConstructor, {
                    month_day: api.MonthDayControl,
                } );

            })( wp, jQuery );
        ");
    }

    /**
     * Export data to JS.
     *
     * @since 4.9.0
     * @return array
     */
    public function json()
    {
        $data = parent::json();

        $data['twelveHourFormat'] = (bool) $this->twelve_hour_format;
        $data['includeTime']      = (bool) $this->include_time;

        return $data;
    }

    /**
     * Renders a JS template for the content of date time control.
     *
     * @since 4.9.0
     */
    public function content_template()
    {
        $data          = array_merge($this->json(), $this->get_month_choices());
        $timezone_info = $this->get_timezone_info();

        $date_format = get_option('date_format');
        $date_format = preg_replace('/(?<!\\\\)[Yyo]/', '', $date_format);
        $date_format = preg_replace('/(?<!\\\\)[jd]/', '%1$s', $date_format);
        $date_format = preg_replace('/(?<!\\\\)[FmMn]/', '%2$s', $date_format);
        $date_format = trim($date_format);

        // Fallback to default date format if mont or day are missing from the date format.
        if (1 !== substr_count($date_format, '%1$s') || 1 !== substr_count($date_format, '%2$s')) {
            $date_format = '%1$s %2$s';
        }
?>

        <# _.defaults( data, <?php echo wp_json_encode($data); ?> ); #>
            <# var idPrefix=_.uniqueId( 'el' ) + '-' ; #>

                <# if ( data.label ) { #>
                    <span class="customize-control-title">
                        {{ data.label }}
                    </span>
                    <# } #>
                        <div class="customize-control-notifications-container"></div>
                        <# if ( data.description ) { #>
                            <span class="description customize-control-description">{{ data.description }}</span>
                            <# } #>
                                <div class="date-time-fields {{ data.includeTime ? 'includes-time' : '' }}">
                                    <fieldset class="day-row">
                                        <legend class="title-day {{ ! data.includeTime ? 'screen-reader-text' : '' }}"><?php esc_html_e('Date'); ?></legend>
                                        <div class="day-fields clear">
                                            <?php ob_start(); ?>
                                            <label for="{{ idPrefix }}date-time-month" class="screen-reader-text"><?php esc_html_e('Month'); ?></label>
                                            <select id="{{ idPrefix }}date-time-month" class="date-input month" data-component="month">
                                                <# _.each( data.month_choices, function( choice ) { if ( _.isObject( choice ) && ! _.isUndefined( choice.text ) && ! _.isUndefined( choice.value ) ) { text=choice.text; value=choice.value; } #>
                                                    <option value="{{ value }}">
                                                        {{ text }}
                                                    </option>
                                                    <# } ); #>
                                            </select>
                                            <?php $month_field = trim(ob_get_clean()); ?>

                                            <?php ob_start(); ?>
                                            <label for="{{ idPrefix }}date-time-day" class="screen-reader-text"><?php esc_html_e('Day'); ?></label>
                                            <input id="{{ idPrefix }}date-time-day" type="number" size="3" autocomplete="off" class="date-input day" data-component="day" min="1" max="31" />
                                            <?php $day_field = trim(ob_get_clean()); ?>

                                            <?php printf($date_format, $month_field, $day_field); ?>
                                        </div>
                                    </fieldset>
                                    <# if ( data.includeTime ) { #>
                                        <fieldset class="time-row clear">
                                            <legend class="title-time"><?php esc_html_e('Time'); ?></legend>
                                            <div class="time-fields clear">
                                                <label for="{{ idPrefix }}date-time-hour" class="screen-reader-text"><?php esc_html_e('Hour'); ?></label>
                                                <# var maxHour=data.twelveHourFormat ? 12 : 23; #>
                                                    <# var minHour=data.twelveHourFormat ? 1 : 0; #>
                                                        <input id="{{ idPrefix }}date-time-hour" type="number" size="2" autocomplete="off" class="date-input hour" data-component="hour" min="{{ minHour }}" max="{{ maxHour }}">
                                                        :
                                                        <label for="{{ idPrefix }}date-time-minute" class="screen-reader-text"><?php esc_html_e('Minute'); ?></label>
                                                        <input id="{{ idPrefix }}date-time-minute" type="number" size="2" autocomplete="off" class="date-input minute" data-component="minute" min="0" max="59">
                                                        <# if ( data.twelveHourFormat ) { #>
                                                            <label for="{{ idPrefix }}date-time-meridian" class="screen-reader-text"><?php esc_html_e('Meridian'); ?></label>
                                                            <select id="{{ idPrefix }}date-time-meridian" class="date-input meridian" data-component="meridian">
                                                                <option value="am"><?php esc_html_e('AM'); ?></option>
                                                                <option value="pm"><?php esc_html_e('PM'); ?></option>
                                                            </select>
                                                            <# } #>
                                                                <p><?php echo $timezone_info['description']; ?></p>
                                            </div>
                                        </fieldset>
                                        <# } #>
                                </div>
                        <?php
                    }

                    /**
                     * Generate options for the month Select.
                     *
                     * Based on touch_time().
                     *
                     * @since 4.9.0
                     *
                     * @see touch_time()
                     *
                     * @global WP_Locale $wp_locale WordPress date and time locale object.
                     *
                     * @return array
                     */
                    public function get_month_choices()
                    {
                        global $wp_locale;
                        $months = array();
                        for ($i = 1; $i < 13; $i++) {
                            $month_text = $wp_locale->get_month($i);

                            /* translators: 1: Month number (01, 02, etc.), 2: Month abbreviation. */
                            $months[$i]['text']  = sprintf(__('%2$s (%1$s)'), $i, $month_text);
                            $months[$i]['value'] = $i;
                        }
                        return array(
                            'month_choices' => $months,
                        );
                    }

                    /**
                     * Get timezone info.
                     *
                     * @since 4.9.0
                     *
                     * @return array {
                     *     Timezone info. All properties are optional.
                     *
                     *     @type string $abbr        Timezone abbreviation. Examples: PST or CEST.
                     *     @type string $description Human-readable timezone description as HTML.
                     * }
                     */
                    public function get_timezone_info()
                    {
                        $tz_string     = get_option('timezone_string');
                        $timezone_info = array();

                        if ($tz_string) {
                            try {
                                $tz = new DateTimeZone($tz_string);
                            } catch (Exception $e) {
                                $tz = '';
                            }

                            if ($tz) {
                                $now                   = new DateTime('now', $tz);
                                $formatted_gmt_offset  = $this->format_gmt_offset($tz->getOffset($now) / 3600);
                                $tz_name               = str_replace('_', ' ', $tz->getName());
                                $timezone_info['abbr'] = $now->format('T');

                                $timezone_info['description'] = sprintf(
                                    /* translators: 1: Timezone name, 2: Timezone abbreviation, 3: UTC abbreviation and offset, 4: UTC offset. */
                                    __('Your timezone is set to %1$s (%2$s), currently %3$s (Coordinated Universal Time %4$s).'),
                                    $tz_name,
                                    '<abbr>' . $timezone_info['abbr'] . '</abbr>',
                                    '<abbr>UTC</abbr>' . $formatted_gmt_offset,
                                    $formatted_gmt_offset
                                );
                            } else {
                                $timezone_info['description'] = '';
                            }
                        } else {
                            $formatted_gmt_offset = $this->format_gmt_offset((int) get_option('gmt_offset', 0));

                            $timezone_info['description'] = sprintf(
                                /* translators: 1: UTC abbreviation and offset, 2: UTC offset. */
                                __('Your timezone is set to %1$s (Coordinated Universal Time %2$s).'),
                                '<abbr>UTC</abbr>' . $formatted_gmt_offset,
                                $formatted_gmt_offset
                            );
                        }

                        return $timezone_info;
                    }

                    /**
                     * Format GMT Offset.
                     *
                     * @since 4.9.0
                     *
                     * @see wp_timezone_choice()
                     *
                     * @param float $offset Offset in hours.
                     * @return string Formatted offset.
                     */
                    public function format_gmt_offset($offset)
                    {
                        if (0 <= $offset) {
                            $formatted_offset = '+' . (string) $offset;
                        } else {
                            $formatted_offset = (string) $offset;
                        }
                        $formatted_offset = str_replace(
                            array('.25', '.5', '.75'),
                            array(':15', ':30', ':45'),
                            $formatted_offset
                        );
                        return $formatted_offset;
                    }
                }


                function customizer_month_day_init(WP_Customize_Manager $wp_customize)
                {
                    $wp_customize->register_control_type('WP_Customize_Month_Day_Control');
                }
                add_action('customize_register', 'customizer_month_day_init', 1);
