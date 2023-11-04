<?php
/**
 * Settings related functionalities for the Chatbot plugin
 */

// Register the Settings Page
function my_chatbot_register_settings_page() {
    add_options_page(
        'LoryBot Settings',
        'LoryBot Settings',
        'manage_options',
        'lorybot-settings',
        'my_chatbot_settings_page_content'
    );
}
add_action('admin_menu', 'my_chatbot_register_settings_page');

// Initialize the settings
function my_chatbot_settings_init() {
    // Register the "my_chatbot_options" setting.
    register_setting('my_chatbot_settings', 'my_chatbot_options', 'my_chatbot_sanitize_options');
    
    // Define main settings section.
    add_settings_section(
        'my_chatbot_main_section',
        'Main Settings',
        'my_chatbot_section_callback',
        'lorybot-settings'
    );

    include_once('settings/fields.php');
    
}
add_action('admin_init', 'my_chatbot_settings_init');


include_once('settings/callbacks.php');



function my_chatbot_enqueue_color_picker($hook_suffix) {
    if ($hook_suffix === 'settings_page_lorybot-settings') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'my_chatbot_enqueue_color_picker');


include_once('settings/sanitaze.php');

// Display the Settings Page Content
function my_chatbot_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Chatbot Settings</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('my_chatbot_settings');
            do_settings_sections('lorybot-settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}


// Handle post-update actions
function my_custom_function_after_update($updated_values) {

    error_log('inside my_custom_function_after_update');

    // Get the values from the updated options
    $chat_enabled = isset($updated_values['enabled']) ? $updated_values['enabled'] : '';
    $lorybot_api = isset($updated_values['lorybot_api']) ? $updated_values['lorybot_api'] : '';
    $embedding = isset($updated_values['embedding']) ? $updated_values['embedding'] : '';
    $client_id = getMainDomain();
    $prompt = isset($updated_values['prompt']) ? $updated_values['prompt'] : '';
    $chat_display = isset($updated_values['chat_display']) ? $updated_values['chat_display'] : '';
    $main_color = isset($updated_values['main_color']) ? $updated_values['main_color'] : '';
    $background_color = isset($updated_values['background_color']) ? $updated_values['background_color'] : '';


    $json = array(

        "chat_enabled" => $chat_enabled,
        "prompt" => $prompt,
        "lorybot_api" => $lorybot_api,
        "embedding" => $embedding,
        "client_id" => $client_id,
        "chat_display" => $chat_display,
        "main_color" => $main_color,
        "background_color" => $background_color


    );

    error_log('Sending data to external server: ' . print_r($json, true));

    global $lorybot_server_url;

    $url = $lorybot_server_url."/updatesettings";
    $response = wp_remote_post($url, array(
        'method' => 'POST',
        'headers' => array(
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($json),
        'sslverify' => false,  // Disable SSL verification temporarily to test
        'timeout' => 60  // Increase timeout to wait longer for a response
    ));
    
    if ( is_wp_error($response) ) {
        $error_message = $response->get_error_message();
        error_log("WP_Error when updating settings: $error_message");
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        error_log("Received response code: $response_code - Body: $response_body");
    }
}

// Hook to option update
function my_chatbot_option_updated($option_name, $old_value, $value) {
    if ($option_name === 'my_chatbot_options') {
        my_custom_function_after_update($value);
    }
}
add_action('updated_option', 'my_chatbot_option_updated', 10, 3);
