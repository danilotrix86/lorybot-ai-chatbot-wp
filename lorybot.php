<?php
/**
 * Plugin Name: Lorybot: AI chatbot
 * Plugin URI: https://www.lorybot.com
 * Description: Lorybot is an innovative AI chatbot plugin for WordPress, designed to generate informed and accurate responses from user-provided text.
 * Version: 1.0
 * Author: Danilo Vaccalluzzo
 * Author URI: https://www.danilovaccalluzzo.it
 */

 defined('ABSPATH') or exit;

// Set the server URL as an option
#update_option('lorybot_server_url', 'https://lorybot.pythonanywhere.com/');
update_option('lorybot_server_url', 'http://127.0.0.1:5000/');

  
require_once plugin_dir_path(__FILE__) . 'includes/utils.php';

// Ensure the 'user_id' cookie is set
if (!isset($_COOKIE['user_id'])) {
    set_user_id_cookie();
}


require_once plugin_dir_path(__FILE__) . 'includes/functions-enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-chat-display.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-settings.php';
    

// Function when the plugin is activated
function lorybot_activate() {

    $lorybot_server_url = get_option('lorybot_server_url');
    $customID = generate_uuid();
    update_option('lorybot_custom_id', $customID);

    $json = [
        'domain' => getMainDomain(),
        'custom_id' => $customID,
    ];

    $args = array(
        'method'    => 'POST',
        'headers'   => array(
            'Content-Type' => 'application/json',
            'API-KEY' => $customID, // Add API key to the request header
        ),
        'body'      => json_encode($json),
        'sslverify' => false,
        'timeout'   => 60,
    );

    $response = wp_remote_post($lorybot_server_url . "/activate/", $args);

    // Check if the option already exists
    if (false === get_option('lorybot_options')) {
        // Option does not exist, so add the default value
        add_option('lorybot_options', array());
    }


    if (is_wp_error($response)) {
        echo "Something went wrong: " . $response->get_error_message();
    } else {
        echo 'POST request sent successfully!';
    }

    add_option('lorybot_do_activation_redirect', true);

}

// Activation hook
register_activation_hook(__FILE__, 'lorybot_activate');




