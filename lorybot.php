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
update_option('lorybot_server_url', 'https://lorybot-q4yzxnb64q-uc.a.run.app/');
#update_option('lorybot_server_url', 'http://127.0.0.1:8002/');

  
require_once plugin_dir_path(__FILE__) . 'includes/utils.php';

// Ensure the 'user_id' cookie is set
if (!isset($_COOKIE['user_id'])) {
    set_user_id_cookie();
}

require_once plugin_dir_path(__FILE__) . 'includes/functions-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-chat-display.php';

    
// Global variable to track activation status
$GLOBALS['is_lorybot_activating'] = false;

// Function when the plugin is activated
function lorybot_activate() {

    // Set the flag to indicate activation is in progress
    $GLOBALS['is_lorybot_activating'] = true;

    $lorybot_server_url = get_option('lorybot_server_url');
    $customID = generate_uuid();
    $options = get_option('lorybot_options', array());
    $options['custom_id'] = $customID;  // Store custom_id in lorybot_options array
    update_option('lorybot_options', $options);

    error_log("Custom ID: " . $customID);

    $json = [
        'domain' => getMainDomain(),
        'custom_id' => $customID,
    ];

    $args = array(
        'method'    => 'POST',
        'headers'   => array(
            'Content-Type' => 'application/json',
        ),
        'body'      => json_encode($json),
        'sslverify' => false,
        'timeout'   => 60,
    );

    $response = wp_remote_post($lorybot_server_url . "activate", $args);

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

    // Reset the flag after activation is done
    $GLOBALS['is_lorybot_activating'] = false;

    add_option('lorybot_do_activation_redirect', true);

}



// Function when the plugin is activated
function lorybot_deactivate() {


    $lorybot_server_url = get_option('lorybot_server_url');
    $options = get_option('lorybot_options');
    $custom_id = isset($options['custom_id']) ? $options['custom_id'] : ''; 
   
    $json = [
        'domain' => getMainDomain(),
        'custom_id' => $custom_id,
    ];

    $args = array(
        'method'    => 'POST',
        'headers'   => [
            'Content-Type' => 'application/json',
            'LORYBOT-API-KEY' => $custom_id, // Add API key to the request header
        ],
        'body'      => json_encode($json),
        'sslverify' => false,
        'timeout'   => 60,
    );

    $response = wp_remote_post($lorybot_server_url . "deactivate", $args);

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

    delete_option('lorybot_options');

}



// Activation hook
register_activation_hook(__FILE__, 'lorybot_activate');

// Deactivation hook
register_deactivation_hook(__FILE__, 'lorybot_deactivate');



