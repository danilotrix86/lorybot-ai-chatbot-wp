<?php
/**
 * Enqueue styles and scripts for the Chatbot plugin
 */

function my_chatbot_enqueue_scripts() {
    // Enqueue the chatbot's CSS
    wp_enqueue_style('my-chatbot-style', plugin_dir_url(__FILE__) . '../assets/css/style.css');

    // Enqueue the chatbot's JavaScript, dependent on jQuery
    wp_enqueue_script('my-chatbot-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', array('jquery'), '1.0', true);

    wp_enqueue_style('material-symbols-outlined', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0');
    wp_enqueue_style('material-symbols-rounded', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0');

    // Pass some PHP variables to JavaScript
    wp_localize_script('my-chatbot-script', 'chatbot_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'plugin_url' => plugin_dir_url(dirname(__FILE__)),
        'prompt' => get_option('my_chatbot_options')['prompt'],
        'client_id' => getMainDomain(),
        'main_color' => get_option('my_chatbot_options')['main_color'],
        'background_color' => get_option('my_chatbot_options')['background_color'],
        'chat_display' => get_option('my_chatbot_options')['chat_display'],
    ));
}

// Hook the function to enqueue scripts and styles
add_action('wp_enqueue_scripts', 'my_chatbot_enqueue_scripts');


