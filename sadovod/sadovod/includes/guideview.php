<?php

/* Отображение сетки -----------------------------------------------*/
function setup_theme_guide_data()
{
    if (isset($_GET['guide']))
        $_SESSION['show_guide'] = $_GET['guide'] === '1';
}
add_filter('init', 'setup_theme_guide_data');

function devmode_clear_guide_args($args)
{
    $args['guide'] = 0;

    return $args;
}
add_filter('devmode_clear_args', 'devmode_clear_guide_args');

function setup_theme_guide_class($classes)
{
    if ($_SESSION['show_guide'] ?? false) {
        $classes[] = "enable-guide";
        add_devmode_detail(__('Guide enabled', 'sadovod-misc'));
    }

    return $classes;
}
add_filter('body_class', 'setup_theme_guide_class');

function setup_guide_buttons()
{
    if ($_SESSION['show_guide'] ?? false) {
        echo '<a class="btn btn-secondary-alt devmode-guide-details" data-bs-toggle="modal" data-bs-target="#guideModal">';
        echo '<i class="i-category"></i>' . __('Guide', 'sadovod-misc');
        echo '</a>';
    }
}
add_action('wp_body_open', 'setup_guide_buttons');

function setup_guide_modal()
{
    if ($_SESSION['show_guide'] ?? false) {
?>
        <div id="guideModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?= __('Element guide', 'sadovod-misc'); ?></h4>
                        <a class="close" data-bs-dismiss="modal" aria-label="Close"><i class="i-close"></i></a>
                    </div>
                    <div class="modal-body">
                        <h2><?= __('Colors', 'sadovod-misc'); ?></h2>
                        <div class="element-group color-group">
                            <div style="background-color: var(--color-bright);"><?= __('Bright', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-constant-bright); color: var(--color-constant-dark)"><?= __('Constant Bright', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-dark); color: var(--color-bright)"><?= __('Dark', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-constant-dark); color: var(--color-constant-bright)"><?= __('Constant Dark', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-foreground);  color: var(--color-bright)"><?= __('Foreground', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-background);"><?= __('Background', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-primary); color: var(--color-bright)"><?= __('Primary', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-secondary);"><?= __('Secondary', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-tertiary);"><?= __('Tertiary', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-success);"><?= __('Success', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-alert);"><?= __('Alert', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-danger);"><?= __('Danger', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-warning);"><?= __('Warning', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-notif);"><?= __('Notif', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-info);"><?= __('Info', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-slider);"><?= __('Slider', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-actions);"><?= __('Actions', 'sadovod-misc'); ?></div>
                            <div style="background-color: var(--color-bage);"><?= __('Bage', 'sadovod-misc'); ?></div>
                        </div>
                        <h2><?= __('Radiuses', 'sadovod-misc'); ?></h2>
                        <div class="element-group radius-group">
                            <div style="border-radius: var(--default-border-radius);"><?= __('Default border radius', 'sadovod-misc'); ?></div>
                            <div style="border-radius: var(--big-border-radius);"><?= __('Big border radius', 'sadovod-misc'); ?></div>
                        </div>
                        <h2><?= __('Shadows', 'sadovod-misc'); ?></h2>
                        <div class="element-group shadow-group">
                            <div>
                                <div style="background: var(--box-shadow-color);"><?= __('Shadow color', 'sadovod-misc'); ?></div>
                                <div style="box-shadow: var(--box-shadow);"><?= __('Shadow', 'sadovod-misc'); ?></div>
                                <div style="background: var(--box-shadow-hover-color);"><?= __('Shadow hover color', 'sadovod-misc'); ?></div>
                                <div style="box-shadow: var(--box-shadow-hover);"><?= __('Shadow hover', 'sadovod-misc'); ?></div>
                            </div>
                            <div>
                                <div style="background: var(--box-shadow-small-color);"><?= __('Shadow small color', 'sadovod-misc'); ?></div>
                                <div style="box-shadow: var(--box-shadow-small);"><?= __('Shadow small', 'sadovod-misc'); ?></div>
                                <div style="background: var(--box-shadow-small-hover-color);"><?= __('Shadow small hover color', 'sadovod-misc'); ?></div>
                                <div style="box-shadow: var(--box-shadow-small-hover);"><?= __('Shadow small hover', 'sadovod-misc'); ?></div>
                            </div>
                            <div>
                                <div style="background: var(--box-shadow-inset-color);"><?= __('Shadow inset color', 'sadovod-misc'); ?></div>
                                <div style="box-shadow: inset 0 -5px 10px -5px var(--box-shadow-inset-color);"><?= __('Shadow inset', 'sadovod-misc'); ?></div>
                            </div>
                        </div>

                        <h2><?= __('Headers', 'sadovod-misc'); ?></h2>
                        <div class=" element-group">
                            <h1><?= __('H1', 'sadovod-misc'); ?></h1>
                            <h2><?= __('H2', 'sadovod-misc'); ?></h2>
                            <h3><?= __('H3', 'sadovod-misc'); ?></h3>
                            <h4><?= __('H4', 'sadovod-misc'); ?></h4>
                            <h5><?= __('H5', 'sadovod-misc'); ?></h5>
                            <h6><?= __('H6', 'sadovod-misc'); ?></h6>
                        </div>
                        <h2><?= __('Text', 'sadovod-misc'); ?></h2>
                        <div class="element-group">
                            <b><?= __('Bold', 'sadovod-misc'); ?></b>
                            <i><?= __('Italic', 'sadovod-misc'); ?></i>
                            <small><?= __('Small', 'sadovod-misc'); ?></small>
                            <a href="#"><?= __('Link', 'sadovod-misc'); ?></a>
                            <div class="w-100"></div>
                            <div class="form-group m-0">
                                <label><?= __('Label', 'sadovod-misc'); ?></label>
                            </div>
                            <p><?= __('Paragraph', 'sadovod-misc'); ?></p>
                        </div>
                        <h2><?= __('Buttons', 'sadovod-misc'); ?></h2>
                        <div class="element-group button-group">
                            <div class="w-100 mt-2"><?= __('Standart', 'sadovod-misc'); ?></div>
                            <button class="btn"><?= __('Default', 'sadovod-misc'); ?></button>
                            <button class="btn btn-primary"><?= __('Primary', 'sadovod-misc'); ?></button>
                            <button class="btn btn-secondary"><?= __('Secondary', 'sadovod-misc'); ?></button>
                            <button class="btn btn-tertiary"><?= __('Tertiary', 'sadovod-misc'); ?></button>
                            <button class="btn btn-danger"><?= __('Danger', 'sadovod-misc'); ?></button>

                            <div class="w-100 mt-2"><?= __('Alternative', 'sadovod-misc'); ?></div>
                            <button class="btn invisible"><?= __('Default', 'sadovod-misc'); ?></button>
                            <button class="btn btn-primary-alt"><?= __('Primary', 'sadovod-misc'); ?></button>
                            <button class="btn btn-secondary-alt"><?= __('Secondary', 'sadovod-misc'); ?></button>
                            <button class="btn btn-tertiary-alt"><?= __('Tertiary', 'sadovod-misc'); ?></button>
                            <button class="btn btn-danger-alt"><?= __('Danger', 'sadovod-misc'); ?></button>

                            <div class="w-100 mt-2"><?= __('Outline', 'sadovod-misc'); ?></div>
                            <button class="btn invisible"><?= __('Default', 'sadovod-misc'); ?></button>
                            <button class="btn btn-outline-primary"><?= __('Primary', 'sadovod-misc'); ?></button>
                            <button class="btn btn-outline-secondary"><?= __('Secondary', 'sadovod-misc'); ?></button>
                            <button class="btn invisible"><?= __('Tertiary', 'sadovod-misc'); ?></button>
                            <button class="btn btn-outline-danger"><?= __('Danger', 'sadovod-misc'); ?></button>

                            <div class="w-100 mt-2"><?= __('Links', 'sadovod-misc'); ?></div>
                            <a class="btn btn-link"><?= __('Default', 'sadovod-misc'); ?></a>
                            <a class="btn btn-link text-danger"><?= __('Danger', 'sadovod-misc'); ?></a>
                        </div>
                        <h2><?= __('Selects', 'sadovod-misc'); ?></h2>
                        <div class=" element-group">
                            <div class="form-group">
                                <select autocomplete="off" class="form-control selectpicker" placeholder="<?= __('Default', 'sadovod-misc') ?>" required>
                                    <option><?= __('Option 1', 'sadovod-misc'); ?></option>
                                    <option><?= __('Option 2', 'sadovod-misc'); ?></option>
                                    <option><?= __('Option 3', 'sadovod-misc'); ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select autocomplete="off" class="form-control selectpicker" placeholder="<?= __('Multiple', 'sadovod-misc') ?>" multiple required>
                                    <option><?= __('Option 1', 'sadovod-misc'); ?></option>
                                    <option><?= __('Option 2', 'sadovod-misc'); ?></option>
                                    <option><?= __('Option 3', 'sadovod-misc'); ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select autocomplete="off" class="form-control selectpicker" placeholder="<?= __('With search', 'sadovod-misc') ?>" data-live-search="true" required>
                                    <option><?= __('Option 1', 'sadovod-misc'); ?></option>
                                    <option><?= __('Option 2', 'sadovod-misc'); ?></option>
                                    <option><?= __('Option 3', 'sadovod-misc'); ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select autocomplete="off" class="form-control selectpicker" placeholder="<?= __('With create', 'sadovod-misc') ?>" data-add-new="theme" required>
                                    <option><?= __('Option 1', 'sadovod-misc'); ?></option>
                                    <option><?= __('Option 2', 'sadovod-misc'); ?></option>
                                    <option><?= __('Option 3', 'sadovod-misc'); ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select autocomplete="off" class="form-control selectpicker" placeholder="<?= __('With groups', 'sadovod-misc') ?>" required>
                                    <optgroup label="<?= __('Group 1', 'sadovod-misc') ?>">
                                        <option><?= __('Option 1', 'sadovod-misc'); ?></option>
                                        <option><?= __('Option 2', 'sadovod-misc'); ?></option>
                                        <option><?= __('Option 3', 'sadovod-misc'); ?></option>
                                    </optgroup>
                                    <optgroup label="<?= __('Group 2', 'sadovod-misc') ?>">
                                        <option><?= __('Option 4', 'sadovod-misc'); ?></option>
                                        <option><?= __('Option 5', 'sadovod-misc'); ?></option>
                                        <option><?= __('Option 6', 'sadovod-misc'); ?></option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="form-group">
                                <select autocomplete="off" class="form-control selectpicker" placeholder="<?= __('Combined', 'sadovod-misc') ?>" multiple data-live-search="true" data-add-new="theme" required>
                                    <optgroup label="<?= __('Group 1', 'sadovod-misc') ?>">
                                        <option><?= __('Option 1', 'sadovod-misc'); ?></option>
                                        <option><?= __('Option 2', 'sadovod-misc'); ?></option>
                                        <option><?= __('Option 3', 'sadovod-misc'); ?></option>
                                    </optgroup>
                                    <optgroup label="<?= __('Group 2', 'sadovod-misc') ?>">
                                        <option><?= __('Option 4', 'sadovod-misc'); ?></option>
                                        <option><?= __('Option 5', 'sadovod-misc'); ?></option>
                                        <option><?= __('Option 6', 'sadovod-misc'); ?></option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <h2><?= __('Fields', 'sadovod-misc'); ?></h2>
                        <div class="element-group">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="<?= __('Text field', 'sadovod-misc'); ?>">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control number" placeholder="<?= __('Number field', 'sadovod-misc'); ?>">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control datepicker-inline" name="date" placeholder="<?= __('Date field', 'sadovod-misc'); ?>">
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-multiline has-validation">
                                    <input id="testPass" class="form-control form-floating password password-strength" type="password" placeholder="<?= __('Password field', 'sadovod-misc'); ?>" maxlength="100" name="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-multiline has-validation">
                                    <input class="form-control form-floating password-repeat" type="password" data-comparant="#testPass" placeholder="<?= __('Repeat password field', 'sadovod-misc'); ?>" maxlength="100" name="password_repeat" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-catacomplete">
                                    <input type="text" class="form-control" placeholder="<?= __('Search field', 'sadovod-misc'); ?>">
                                    <div class="input-catacomplete-result"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <textarea class="form-control" rows="3" placeholder="<?= __('Textarea', 'sadovod-misc'); ?>" required></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="message" class="form-control has-emoji" rows="3" placeholder="<?= __('Textarea with emoji', 'sadovod-misc'); ?>" required></textarea>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="<?= __('Input group', 'sadovod-misc'); ?>" required></textarea>
                                    <div class="input-group-append">
                                        <span class="input-group-text">₽</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <textarea name="message" class="form-control has-emoji" rows="2" placeholder="<?= __('Textarea with emoji and button', 'sadovod-misc'); ?>" required></textarea>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary-alt btn-icon"><i class="i-send"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h2><?= __('Switches', 'sadovod-misc'); ?></h2>
                        <div class="element-group">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                        <?= __('Checkbox', 'sadovod-misc'); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                                    <label class="form-check-label" for="exampleRadios1">
                                        <?= __('Radio 1', 'sadovod-misc'); ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                                    <label class="form-check-label" for="exampleRadios2">
                                        <?= __('Radio 2', 'sadovod-misc'); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sample-switch">
                                    <label class="form-check-label" for="sample-switch"><?= __('Switch', 'sadovod-misc') ?></label>
                                </div>
                            </div>
                        </div>

                        <h2><?= __('Popups', 'sadovod-misc'); ?></h2>
                        <div class="element-group">
                            <div class="form-group">
                                <a data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <span><?= __('Dropdown', 'sadovod-misc'); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="/office">
                                        <span><?= __('Item 1', 'sadovod-misc'); ?></span>
                                    </a>
                                    <a class="dropdown-item" href="/office">
                                        <span><?= __('Item 2', 'sadovod-misc'); ?></span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/office">
                                        <span><?= __('Item 3', 'sadovod-misc'); ?></span>
                                    </a>
                                </ul>
                            </div>
                            <div class="form-group">
                                <a class="btn btn-primary-alt" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <span><?= __('Dropdown button', 'sadovod-misc'); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="/office">
                                        <span><?= __('Item 1', 'sadovod-misc'); ?></span>
                                    </a>
                                    <a class="dropdown-item" href="/office">
                                        <span><?= __('Item 2', 'sadovod-misc'); ?></span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/office">
                                        <span><?= __('Item 3', 'sadovod-misc'); ?></span>
                                    </a>
                                </ul>
                            </div>
                            <div class="form-group">
                                <a data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="i-filter"></i>
                                    <span><?= __('Dropdown with icons', 'sadovod-misc'); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="/office">
                                        <i class="i-home"></i>
                                        <span><?= __('Item 1', 'sadovod-misc'); ?></span>
                                    </a>
                                    <a class="dropdown-item" href="/office">
                                        <i class="i-profile"></i>
                                        <span><?= __('Item 2', 'sadovod-misc'); ?></span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="/office">
                                        <i class="i-message"></i>
                                        <span><?= __('Item 3', 'sadovod-misc'); ?></span>
                                    </a>
                                </ul>
                            </div>
                            <div class="form-group">
                                <a data-bs-toggle="tooltip" title="<?= __('Some tooltip text', 'sadovod-misc'); ?>">
                                    <span><?= __('Tooltip', 'sadovod-misc'); ?></span>
                                </a>
                            </div>
                            <div class="form-group">
                                <a data-bs-toggle="popover" title="<?= __('Notice title', 'sadovod-misc'); ?>" data-bs-content="<?= __('Notice content', 'sadovod-misc'); ?>">
                                    <span><?= __('Notice', 'sadovod-misc'); ?></span>
                                </a>
                            </div>
                            <div class="form-group">
                                <div class="toast fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="15000" data-id="demo" data-type="demo-type">
                                    <div class="toast-header">
                                        <div class="toast-title">
                                            <img data-src="<?= wp_get_attachment_image_url(get_theme_mod('custom_logo_dark'), 'tiny'); ?>" class="rounded me-2 lazyload" alt="<?= __('Notification', 'sadovod-misc'); ?>">
                                            <strong class="me-auto"><?= __('Notification title', 'sadovod-misc'); ?></strong>
                                        </div>
                                        <small><?= wp_date('d.m.Y') ?></small>
                                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                    <div class="toast-body">
                                        <p><?= __('Notification message', 'sadovod-misc'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h2><?= __('Alerts', 'sadovod-misc'); ?></h2>
                        <div class="element-group">
                            <div class="alert alert-success alert-dismissible mb-2" role="alert">
                                <span class="alert-content">
                                    <?= __('Alert success', 'sadovod-misc'); ?>
                                </span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <div class="alert alert-danger alert-dismissible mb-2" role="alert">
                                <span class="alert-content">
                                    <?= __('Alert danger', 'sadovod-misc'); ?>
                                </span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>

                        <h2><?= __('Modals', 'sadovod-misc'); ?></h2>
                        <div class="element-group">

                            <div class="form-group">
                                <a class="btn btn-link" data-bs-activate="modal" data-bs-target="#sampleModal"><?= __('Sample modal', 'sadovod-misc'); ?></a>
                            </div>
                            <div class="form-group">
                                <a class="btn btn-link text-danger" data-confirmation="sample_action"><?= __('Action confirmation modal', 'sadovod-misc'); ?></a>
                            </div>
                            <div class="form-group">
                                <a class="btn btn-link" data-bs-activate="modal" data-bs-target="#resultModal"><?= __('Success modal', 'sadovod-misc'); ?></a>
                            </div>
                            <div class="form-group">
                                <a class="btn btn-link" data-bs-activate="modal" data-bs-target="#errorModal"><?= __('Fail modal', 'sadovod-misc'); ?></a>
                            </div>
                        </div>

                        <h2><?= __('Features', 'sadovod-misc'); ?></h2>
                        <div class="element-group">
                            <div class="single-select-blocks">
                                <div class="single-select-block">
                                    <div class="select-block-title"><?= __('Image', 'sadovod-misc'); ?></div>
                                    <div class="select-block-body">
                                        <div class="select-block-item active">
                                            <a title="<?= __('Type 1', 'sadovod-misc'); ?>">
                                                <img class="select-block select-block-img lazyload" src="" alt="<?= __('Type 1', 'sadovod-misc'); ?>" title="<?= __('Type 1', 'sadovod-misc'); ?>" data-popover="tip" data-trigger="hover" data-text="<?= __('Type 1', 'sadovod-misc'); ?>" data-src="<?= wp_get_attachment_image_url(get_theme_mod('custom_logo_dark'), 'tiny'); ?>">
                                            </a>
                                        </div>
                                        <div class="select-block-item">
                                            <a title="<?= __('Type 2', 'sadovod-misc'); ?>">
                                                <img class="select-block select-block-img lazyload" src="" alt="<?= __('Type 2', 'sadovod-misc'); ?>" title="<?= __('Type 2', 'sadovod-misc'); ?>" data-popover="tip" data-trigger="hover" data-text="<?= __('Type 1', 'sadovod-misc'); ?>" data-src="<?= wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'tiny'); ?>">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-select-block">
                                    <div class="select-block-title"><?= __('Color', 'sadovod-misc'); ?></div>
                                    <div class="select-block-body">
                                        <div class="select-block-item">
                                            <a title="<?= __('Type 1', 'sadovod-misc'); ?>">
                                                <div class="select-block select-block-color" data-popover="tip" data-trigger="hover" data-text="<?= __('Type 1', 'sadovod-misc'); ?>" style="background:var(--color-secondary)"></div>
                                            </a>
                                        </div>
                                        <div class="select-block-item active">
                                            <a title="<?= __('Type 2', 'sadovod-misc'); ?>">
                                                <div class="select-block select-block-color" data-popover="tip" data-trigger="hover" data-text="<?= __('Type 2', 'sadovod-misc'); ?>" style="background:var(--color-primary)"></div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="single-select-block">
                                    <div class="select-block-title"><?= __('Text', 'sadovod-misc'); ?></div>
                                    <div class="select-block-body">
                                        <div class="select-block-item">
                                            <a title="<?= __('Type 1', 'sadovod-misc'); ?>">
                                                <div class="select-block select-block-text"><?= __('Type 1', 'sadovod-misc'); ?></div>
                                            </a>
                                        </div>
                                        <div class="select-block-item active">
                                            <a title="<?= __('Type 2', 'sadovod-misc'); ?>">
                                                <div class="select-block select-block-text"><?= __('Type 2', 'sadovod-misc'); ?></div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h2><?= __('Icons', 'sadovod-misc'); ?></h2>
                        <div class="element-group icon-group">
                            <div>
                                <i class="i-home"></i><span>Home</span>
                            </div>
                            <div>
                                <i class="i-category"></i><span>Category</span>
                            </div>
                            <div>
                                <i class="i-profile"></i><span>Profile</span>
                            </div>
                            <div>
                                <i class="i-users"></i><span>Users</span>
                            </div>
                            <div>
                                <i class="i-user-group"></i><span>User group</span>
                            </div>
                            <div>
                                <i class="i-add-user"></i><span>Add-user</span>
                            </div>
                            <div>
                                <i class="i-login"></i><span>Login</span>
                            </div>
                            <div>
                                <i class="i-logout"></i><span>Logout</span>
                            </div>
                            <div>
                                <i class="i-notification"></i><span>Notification</span>
                            </div>
                            <div>
                                <i class="i-chat"></i><span>Chat</span>
                            </div>
                            <div>
                                <i class="i-message"></i><span>Message</span>
                            </div>
                            <div>
                                <i class="i-send"></i><span>Send</span>
                            </div>
                            <div>
                                <i class="i-password"></i><span>Password</span>
                            </div>
                            <div>
                                <i class="i-unlock"></i><span>Unlock</span>
                            </div>
                            <div>
                                <i class="i-lock"></i><span>Lock</span>
                            </div>
                            <div>
                                <i class="i-wallet"></i><span>Wallet</span>
                            </div>
                            <div>
                                <i class="i-cog"></i><span>Cog</span>
                            </div>
                            <div>
                                <i class="i-chevron-down"></i><span>Chevron down</span>
                            </div>
                            <div>
                                <i class="i-chevron-up"></i><span>Chevron up</span>
                            </div>
                            <div>
                                <i class="i-chevron-left"></i><span>Chevron left</span>
                            </div>
                            <div>
                                <i class="i-chevron-right"></i><span>Chevron right</span>
                            </div>
                            <div>
                                <i class="i-arrow-triangle-up"></i><span>Arrow triangleup</span>
                            </div>
                            <div>
                                <i class="i-arrow-triangle-down"></i><span>Arrow triangle down</span>
                            </div>
                            <div>
                                <i class="i-arrow-triangle-left"></i><span>Arrow triangle left</span>
                            </div>
                            <div>
                                <i class="i-arrow-triangle-right"></i><span>Arrow triangle right</span>
                            </div>
                            <div>
                                <i class="i-shop-bag"></i><span>Shop bag</span>
                            </div>
                            <div>
                                <i class="i-goods-bag"></i><span>Goods bag</span>
                            </div>
                            <div>
                                <i class="i-cart"></i><span>Cart</span>
                            </div>
                            <div>
                                <i class="i-bookmark"></i><span>Bookmark</span>
                            </div>
                            <div>
                                <i class="i-graph"></i><span>Graph</span>
                            </div>
                            <div>
                                <i class="i-chart"></i><span>Chart</span>
                            </div>
                            <div>
                                <i class="i-discount"></i><span>Discount</span>
                            </div>
                            <div>
                                <i class="i-ticket-star"></i><span>Ticket star</span>
                            </div>
                            <div>
                                <i class="i-ticket"></i><span>Ticket</span>
                            </div>
                            <div>
                                <i class="i-settings"></i><span>Settings</span>
                            </div>
                            <div>
                                <i class="i-filter"></i><span>Filter</span>
                            </div>
                            <div>
                                <i class="i-arrow-circle-up"></i><span>Arrow circle up</span>
                            </div>
                            <div>
                                <i class="i-arrow-circle-down"></i><span>Arrow circle down</span>
                            </div>
                            <div>
                                <i class="i-arrow-circle-left"></i><span>Arrow circle left</span>
                            </div>
                            <div>
                                <i class="i-arrow-circle-right"></i><span>Arrow circle right</span>
                            </div>
                            <div>
                                <i class="i-arrow-square-up"></i><span>Arrow square up</span>
                            </div>
                            <div>
                                <i class="i-arrow-square-down"></i><span>Arrow square down</span>
                            </div>
                            <div>
                                <i class="i-arrow-square-left"></i><span>Arrow square left</span>
                            </div>
                            <div>
                                <i class="i-arrow-square-right"></i><span>Arrow square right</span>
                            </div>
                            <div>
                                <i class="i-arrow-up"></i><span>Arrow up</span>
                            </div>
                            <div>
                                <i class="i-arrow-down"></i><span>Arrow down</span>
                            </div>
                            <div>
                                <i class="i-arrow-left"></i><span>Arrow left</span>
                            </div>
                            <div>
                                <i class="i-arrow-right"></i><span>Arrow right</span>
                            </div>
                            <div>
                                <i class="i-arrow-swap"></i><span>Arrow swap</span>
                            </div>
                            <div>
                                <i class="i-call-missed"></i><span>Call missed</span>
                            </div>
                            <div>
                                <i class="i-call-silent"></i><span>Call silent</span>
                            </div>
                            <div>
                                <i class="i-call"></i><span>Call</span>
                            </div>
                            <div>
                                <i class="i-calling"></i><span>Calling</span>
                            </div>
                            <div>
                                <i class="i-voice-enable"></i><span>Voice enable</span>
                            </div>
                            <div>
                                <i class="i-voice-disable"></i><span>Voice disable</span>
                            </div>
                            <div>
                                <i class="i-volume-down"></i><span>Volume down</span>
                            </div>
                            <div>
                                <i class="i-volume-up"></i><span>Volume up</span>
                            </div>
                            <div>
                                <i class="i-volume-off"></i><span>Volume off</span>
                            </div>
                            <div>
                                <i class="i-image"></i><span>Image</span>
                            </div>
                            <div>
                                <i class="i-camera"></i><span>Camera</span>
                            </div>
                            <div>
                                <i class="i-video"></i><span>Video</span>
                            </div>
                            <div>
                                <i class="i-play"></i><span>Play</span>
                            </div>
                            <div>
                                <i class="i-game"></i><span>Game</span>
                            </div>
                            <div>
                                <i class="i-discovery"></i><span>Discovery</span>
                            </div>
                            <div>
                                <i class="i-location"></i><span>Location</span>
                            </div>
                            <div>
                                <i class="i-search"></i><span>Search</span>
                            </div>
                            <div>
                                <i class="i-danger-circle"></i><span>Danger circle</span>
                            </div>
                            <div>
                                <i class="i-danger-triangle"></i><span>Danger triangle</span>
                            </div>
                            <div>
                                <i class="i-time-circle"></i><span>Time circle</span>
                            </div>
                            <div>
                                <i class="i-time-square"></i><span>Time square</span>
                            </div>
                            <div>
                                <i class="i-tick-square"></i><span>Tick square</span>
                            </div>
                            <div>
                                <i class="i-download"></i><span>Download</span>
                            </div>
                            <div>
                                <i class="i-upload"></i><span>Upload</span>
                            </div>
                            <div>
                                <i class="i-document"></i><span>Document</span>
                            </div>
                            <div>
                                <i class="i-edit-square"></i><span>Edit square</span>
                            </div>
                            <div>
                                <i class="i-edit"></i><span>Edit</span>
                            </div>
                            <div>
                                <i class="i-calendar"></i><span>Calendar</span>
                            </div>
                            <div>
                                <i class="i-delete"></i><span>Delete</span>
                            </div>
                            <div>
                                <i class="i-star"></i><span>Star</span>
                            </div>
                            <div>
                                <i class="i-star-1"></i><span>Star 1</span>
                            </div>
                            <div>
                                <i class="i-star-2"></i><span>Star 2</span>
                            </div>
                            <div>
                                <i class="i-star-3"></i><span>Star 3</span>
                            </div>
                            <div>
                                <i class="i-star-4"></i><span>Star 4</span>
                            </div>
                            <div>
                                <i class="i-heart"></i><span>Heart</span>
                            </div>
                            <div>
                                <i class="i-show"></i><span>Show</span>
                            </div>
                            <div>
                                <i class="i-hide"></i><span>Hide</span>
                            </div>
                            <div>
                                <i class="i-activity"></i><span>Activity</span>
                            </div>
                            <div>
                                <i class="i-more-circle"></i><span>More circle</span>
                            </div>
                            <div>
                                <i class="i-more-square"></i><span>More square</span>
                            </div>
                            <div>
                                <i class="i-plus-square"></i><span>Plus square</span>
                            </div>
                            <div>
                                <i class="i-info-square"></i><span>Info square</span>
                            </div>
                            <div>
                                <i class="i-close-square"></i><span>Close square</span>
                            </div>
                            <div>
                                <i class="i-folder"></i><span>Folder</span>
                            </div>
                            <div>
                                <i class="i-paper"></i><span>Paper</span>
                            </div>
                            <div>
                                <i class="i-paper-fail"></i><span>Paper fail</span>
                            </div>
                            <div>
                                <i class="i-paper-minus"></i><span>Paper minus</span>
                            </div>
                            <div>
                                <i class="i-paper-plus"></i><span>Paper plus</span>
                            </div>
                            <div>
                                <i class="i-paper-download"></i><span>Paper download</span>
                            </div>
                            <div>
                                <i class="i-paper-upload"></i><span>Paper upload</span>
                            </div>
                            <div>
                                <i class="i-scan"></i><span>Scan</span>
                            </div>
                            <div>
                                <i class="i-shield-done"></i><span>Shield done</span>
                            </div>
                            <div>
                                <i class="i-shield-fail"></i><span>Shield fail</span>
                            </div>
                            <div>
                                <i class="i-work"></i><span>Work</span>
                            </div>
                            <div>
                                <i class="i-check"></i><span>Check</span>
                            </div>
                            <div>
                                <i class="i-close"></i><span>Close</span>
                            </div>
                            <div>
                                <i class="i-minus"></i><span>Minus</span>
                            </div>
                            <div>
                                <i class="i-plus"></i><span>Plus</span>
                            </div>
                            <div>
                                <i class="i-reply"></i><span>Reply</span>
                            </div>
                            <div>
                                <i class="i-day"></i><span>Day</span>
                            </div>
                            <div>
                                <i class="i-night"></i><span>Night</span>
                            </div>
                            <div>
                                <i class="i-copy"></i><span>Copy</span>
                            </div>
                            <div>
                                <i class="i-share-square"></i><span>Share square</span>
                            </div>
                            <div>
                                <i class="i-bulb"></i><span>Bulb</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="sampleModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?= __('Modal', 'sadovod-misc'); ?></h4>
                        <a class="close" data-bs-dismiss="modal" aria-label="Close"><i class="i-close"></i></a>
                    </div>
                    <div class="modal-body">
                        <?= __('Modal content', 'sadovod-misc'); ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
add_action('before_modals', 'setup_guide_modal');


function setup_sample_confirmation()
{
    global $confirmation_dialogs;

    $confirmation_dialogs['sample_action'] = array(
        'nonce' => wp_create_nonce('action'),
        'text' => '<h4>' . __('Do you really want to do this?', 'sadovod-misc') . '</h4>' .
            '<span>' . __('It’s very dangerous', 'sadovod-misc') . '</span>',
        'fields' => array(
            'active_password' => array(
                'placeholder' => __('Specify password', 'sadovod-misc'),
                'type' => 'password',
                'required' => true
            ),
        ),
        'actions' => array(
            array(
                'title' => __('Revert', 'sadovod-misc'),
            ),
            array(
                'title' => __('Confirm', 'sadovod-misc'),
            )
        ),
    );
}
add_filter('init', 'setup_sample_confirmation');

/*  Очистка после выхода -----------------------------------------------*/
function clear_guide_data_on_logout()
{
    unset($_SESSION['show_guide']);
}
//add_action('wp_logout', 'clear_guide_data_on_logout');
