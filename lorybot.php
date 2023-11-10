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
update_option('lorybot_server_url', 'https://lorybot.pythonanywhere.com/');

  
require_once plugin_dir_path(__FILE__) . 'includes/utils.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-chat-display.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-chat-process.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-settings.php';
 
 // Activation hook
 register_activation_hook(__FILE__, 'lorybot_activate');
 
// Function when the plugin is activated
function lorybot_activate() {

    $lorybot_server_url = get_option('lorybot_server_url');

    if (empty($lorybot_server_url)) {
        error_log("lorybot_server_url is not set or empty.");
        return;
    }

    // Ensure that the server URL ends with a slash
    $formatted_server_url = rtrim($lorybot_server_url, '/') . '/';

    // Construct the full URL for the POST request
    $full_url = $formatted_server_url . "activate/";
    error_log("Full URL: " . $full_url); // Log the full URL for debugging

    // Send the POST request
    $response = wp_remote_post($full_url, array(
        'body' => array('domain' => getMainDomain()),
    ));

    // Check for errors in the response
    if (is_wp_error($response)) {
        error_log("Something went wrong: " . $response->get_error_message());
    } else {
        error_log('POST request sent successfully!');
    }
}




