<?php


include_once 'settings/callbacks.php';
include_once 'settings/sanitaze.php';

function lorybot_register_settings_page() {
    
    error_log('lorybot_register_settings_page');
    add_options_page('LoryBot Settings', 'LoryBot Settings', 'manage_options', 'lorybot-settings', 'lorybot_settings_page_content');
}
add_action('admin_menu', 'lorybot_register_settings_page');

function lorybot_settings_init() {
    
    error_log('lorybot_settings_init');
    register_setting('lorybot_settings', 'lorybot_options', 'lorybot_sanitize_options');
    add_settings_section('lorybot_main_section', 'Main Settings', 'lorybot_section_callback', 'lorybot-settings');
    include_once 'settings/fields.php';
}
add_action('admin_init', 'lorybot_settings_init');


function lorybot_settings_page_content() {
    
    error_log('lorybot_settings_page_content');
    ?>
    <div class="wrap">
        <h1>Chatbot Settings</h1>
        <?php settings_errors(); ?> <!-- Display settings errors here -->
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
    
    error_log('lorybot_function_after_update');
    
    $options = get_option('lorybot_options');
    $custom_id = isset($options['custom_id']) ? $options['custom_id'] : ''; 

    // Check if custom_id is empty
    if (empty($custom_id)) {
        // If custom_id is empty, delete all settings
        delete_option('lorybot_options');
        error_log('Deleted all Lorybot settings due to empty custom_id');
        return false; // Return false to indicate settings were not updated
    }

    error_log('Update Settings custom_id: ' . $custom_id);

    $json = [
        'embedding' => $updated_values['embedding'] ?? '',
        'prompt' => $updated_values['prompt'] ?? '',
        'custom_id' => $custom_id,
    ];

    $lorybot_server_url = get_option('lorybot_server_url');
    $response = wp_remote_post($lorybot_server_url . "settings", [
        'method'    => 'POST',
        'headers'   => [
            'Content-Type' => 'application/json',
            'LORYBOT-API-KEY' => $custom_id, // Add API key to the request header
        ],
        'body'      => json_encode($json),
        'sslverify' => false,
        'timeout'   => 60
    ]);

    if (is_wp_error($response)) {
        error_log("WP_Error when updating settings: " . $response->get_error_message());
        return false;
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        error_log("Received response code: " . $response_code . " - Body: " . $response_body);

        if ($response_code == 401) {
            error_log("Unauthorized access. API Key missing or incorrect.");
            return false; // Indicate failure due to unauthorized access
        }
        
        return ($response_code == 200); // Return true if response code is 200
    }
}


/*
function lorybot_function_after_update($updated_values) {
    error_log('lorybot_function_after_update');
    $options = get_option('lorybot_options');
    $custom_id = isset($options['custom_id']) ? $options['custom_id'] : ''; 

    error_log('Update Settings custom_id: ' . $custom_id);

    $json = [
        'embedding' => $updated_values['embedding'] ?? '',
        'prompt' => $updated_values['prompt'] ?? '',
        'custom_id' => $custom_id,
    ];

    $lorybot_server_url = get_option('lorybot_server_url');
    $response = wp_remote_post($lorybot_server_url . "settings", [
        'method'    => 'POST',
        'headers'   => [
            'Content-Type' => 'application/json',
            'LORYBOT-API-KEY' => $custom_id, // Add API key to the request header
        ],
        'body'      => json_encode($json),
        'sslverify' => false,
        'timeout'   => 60
    ]);
    

    if (is_wp_error($response)) {
        error_log("WP_Error when updating settings: " . $response->get_error_message());
        return false;
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        error_log("Received response code: " . $response_code . 
                  " - Body: " . $response_body);

        if ($response_code == 401) {
            error_log("Unauthorized access. API Key missing or incorrect.");
            return false; // Indicate failure due to unauthorized access
        }
        
        return ($response_code == 200); // Return true if response code is 200
    }
}
*/

function lorybot_option_updated($option_name, $old_value, $value) {

    error_log('lorybot_option_updated called for option: ' . $option_name);

    // Skip if activation or deactivation is in progress
    if (isset($GLOBALS['is_lorybot_activating']) && $GLOBALS['is_lorybot_activating']) {
        error_log('Skipping lorybot_option_updated during activation/deactivation');
        return;
    }

    if ($option_name === 'lorybot_options') {
        $update_success = lorybot_function_after_update($value);

        if (!$update_success) {
            // Add an error message to be displayed
            add_settings_error(
                'lorybot_options',
                'lorybot_update_failed',
                'There was an error updating the settings.',
                'error'
            );

            // Reset the option to its old value to prevent saving the new settings
            update_option('lorybot_options', $old_value);

            // Optionally, you might want to redirect back to the settings page
        }
    }
}
add_action('updated_option', 'lorybot_option_updated', 10, 3);
