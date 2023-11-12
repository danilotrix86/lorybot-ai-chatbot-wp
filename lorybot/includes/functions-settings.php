<?php


include_once 'settings/callbacks.php';
include_once 'settings/sanitaze.php';

function lorybot_register_settings_page() {
    add_options_page('LoryBot Settings', 'LoryBot Settings', 'manage_options', 'lorybot-settings', 'lorybot_settings_page_content');
}
add_action('admin_menu', 'lorybot_register_settings_page');

function lorybot_settings_init() {
    register_setting('lorybot_settings', 'lorybot_options', 'lorybot_sanitize_options');
    add_settings_section('lorybot_main_section', 'Main Settings', 'lorybot_section_callback', 'lorybot-settings');
    include_once 'settings/fields.php';
}
add_action('admin_init', 'lorybot_settings_init');


function lorybot_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Chatbot Settings</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('lorybot_settings');
            do_settings_sections('lorybot-settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

function lorybot_function_after_update($updated_values) {
    $client_id = getMainDomain();
    $json = [
        'lorybot_api' => $updated_values['lorybot_api'] ?? '',
        'embedding' => $updated_values['embedding'] ?? '',
        'client_id' => $client_id,
        'prompt' => $updated_values['prompt'] ?? '',
    ];

    $lorybot_server_url = get_option('lorybot_server_url');
    $response = wp_remote_post($lorybot_server_url . "/updatesettings", [
        'method'    => 'POST',
        'headers'   => ['Content-Type' => 'application/json'],
        'body'      => json_encode($json),
        'sslverify' => false,
        'timeout'   => 60
    ]);

    if (is_wp_error($response)) {
        error_log("WP_Error when updating settings: " . $response->get_error_message());
    } else {
        error_log("Received response code: " . wp_remote_retrieve_response_code($response) . 
                  " - Body: " . wp_remote_retrieve_body($response));
    }
}

function lorybot_option_updated($option_name, $old_value, $value) {
    if ($option_name === 'lorybot_options') {
        lorybot_function_after_update($value);
    }
}
add_action('updated_option', 'lorybot_option_updated', 10, 3);
