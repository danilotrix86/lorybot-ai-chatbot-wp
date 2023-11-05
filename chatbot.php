<?php
/**
 * Plugin Name: Lorybot: AI chatbot
 * Plugin URI: https://www.lorybot.com
 * Description: Lorybot is an innovative AI chatbot plugin for WordPress, expertly designed to generate informed and accurate responses from user-provided text. Tailoring its replies to individual inquiries, Lorybot offers a personalized and engaging user experience. This plugin is ideal for delivering immediate, relevant answers, enhancing user interaction and satisfaction on your website through the power of AI-driven conversation.
 * Version: 1.0
 * Author: Danilo Vaccalluzzo
 * Author URI: https://www.danilovaccalluzzo.it
 
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

global $lorybot_server_url;
#$lorybot_server_url = 'http://127.0.0.1:5000/';
$lorybot_server_url = 'https://chat2all.pythonanywhere.com/';



function getMainDomain() {
    // Extract host from current running script
    $host = $_SERVER['HTTP_HOST'];

    // Check if the host is 'localhost'
    if ($host === 'localhost') {
        return 'localhost';
    }

    // Split host into parts
    $hostParts = explode('.', $host);

    // If there's a subdomain like 'www.', then the main domain will be the second to the last part
    // If not, it will be the third to the last part.
    // This works for most cases, but may need adjustments for URLs with more complex subdomains.
    $mainDomain = $hostParts[count($hostParts) - 2];

    return $mainDomain;
}


// Include files
require_once plugin_dir_path(__FILE__) . 'includes/functions-enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-chat-display.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-chat-process.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-settings.php';


function lorybot_activate() {
    global $lorybot_server_url;  // Access the global variable
    
    add_option('lorybot_do_activation_redirect', true);

    // URL to send the POST request to
    $url = $lorybot_server_url . "activate/"; 

    // Data to send in the POST request
    $body = array(
        'domain' => getMainDomain(),
    );

    // Send the POST request
    $response = wp_remote_post($url, array(
        'body' => $body,
    ));

    // Check for errors
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: $error_message";
    } else {
        echo 'POST request sent successfully!';
    }
}
register_activation_hook(__FILE__, 'lorybot_activate');


function lorybot_redirect() {
    if (get_option('lorybot_do_activation_redirect', false)) {
        delete_option('lorybot_do_activation_redirect');
        wp_redirect(admin_url('options-general.php?page=lorybot-settings'));
        exit;
    }
}
add_action('admin_init', 'lorybot_redirect');