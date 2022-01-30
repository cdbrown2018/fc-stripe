<?php
add_action('admin_menu', 'fcs_options_page');
function fcs_options_page()
{
    add_menu_page("Stripe Options", "FC Stripe", "manage_options", 'fcs', 'fcs_options_page_html');
}

add_action('admin_init', 'fcs_settings_init');
function fcs_settings_init()
{
    add_settings_section('fcs_keys', 'Stripe API Keys', 'fcs_settings_section_callback', 'fcs');

    register_setting('fcs', 'fcs_test_key');
    register_setting('fcs', 'fcs_test_key_publishable');
    register_setting('fcs', 'fcs_prod_key');
    register_setting('fcs', 'fcs_prod_key_publishable');
    register_setting('fcs', 'fcs_success_slug');
    register_setting('fcs', 'fcs_test_mode');

    add_settings_field('fcs_test_key', 'Test Key', 'fcs_do_nothing_callback', 'fcs', 'fcs_keys', array('option_name' => 'fcs_test_key', 'isSensitive' => true));
    add_settings_field('fcs_test_key_publishable', 'Publishable Test Key', 'fcs_do_nothing_callback', 'fcs', 'fcs_keys', array('option_name' => 'fcs_test_key_publishable', 'isSensitive' => true));
    add_settings_field('fcs_prod_key', 'Live Key', 'fcs_do_nothing_callback', 'fcs', 'fcs_keys', array('option_name' => 'fcs_prod_key', 'isSensitive' => true));
    add_settings_field('fcs_prod_key_publishable', 'Publishable Live Key', 'fcs_do_nothing_callback', 'fcs', 'fcs_keys', array('option_name' => 'fcs_prod_key_publishable', 'isSensitive' => true));

    add_settings_section('fcs_public', 'Non-Sensitive Properties', 'fcs_non_sensitive_callback', 'fcs');

    add_settings_field('fcs_success_slug', 'Success Page Slug', 'fcs_do_nothing_callback', 'fcs', 'fcs_public', array('option_name' => 'fcs_success_slug', 'isSensitive' => false));
    add_settings_field('fcs_test_mode', 'Test Mode', 'fcs_checkbox_input', 'fcs', 'fcs_public', array('option_name' => 'fcs_test_mode', 'isSensitive' => false));
}

function fcs_settings_section_callback()
{
    echo 'These keys are secret. Do not reveal them to anyone.';
}

function fcs_non_sensitive_callback()
{
    echo "These settings aren't sensitive. Feel free to share them with your grandma.";
}

function fcs_do_nothing_callback(array $args)
{
    // get the value of the setting we've registered with register_setting()
    $setting = get_option($args['option_name']);
    // output the field
?>
    <input type=<?php echo $args['isSensitive'] ? 'password' : 'text'; ?> name="<?php echo $args['option_name']; ?>" value="<?php echo isset($setting) ? esc_attr($setting) : ''; ?>" style="width: 100%;" />
<?php
}

function fcs_checkbox_input(array $args) {
    $setting = get_option($args['option_name']);
    ?>
    <input type="checkbox" name="<?php echo $args['option_name']; ?>" <?php echo checked(1, $setting, false) ?> value="1" />
    <?php
}

function fcs_options_page_html()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['settings-updated'])) {
        add_settings_error('fcs_messages', 'fcs_message', __('Settings Saved', 'fcs'), 'updated');
    }

    settings_errors('fcs_messages');
?>
    <div class='wrap'>
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('fcs');
            do_settings_sections('fcs');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
<?php
}
