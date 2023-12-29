<?php
/**
 * Enqueue styles and scripts for the Chatbot plugin
 */



function lorybot_enqueue_scripts() {
    // Enqueue the chatbot's CSS
    wp_enqueue_style('lorybot-style', plugin_dir_url(__FILE__) . '../assets/css/style.css');

    // Enqueue the chatbot's JavaScript, dependent on jQuery
    wp_enqueue_script('lorybot-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', array('jquery'), '1.0', true);

    wp_enqueue_style('material-symbols-outlined', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0');
    wp_enqueue_style('material-symbols-rounded', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0');

    // Pass some PHP variables to JavaScript
    wp_localize_script('lorybot-script', 'chatbot_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'plugin_url' => plugin_dir_url(dirname(__FILE__)),
        'prompt' => get_option('lorybot_options')['prompt'],
        'client_id' => getMainDomain(),
        'main_color' => get_option('lorybot_options')['main_color'],
        'background_color' => get_option('lorybot_options')['background_color'],
        'title_color' => get_option('lorybot_options')['title_color'],
        'chat_display' => get_option('lorybot_options')['chat_display'],
        'custom_id' => get_option('lorybot_options')['custom_id'],
        'server_url' => get_option('lorybot_server_url'),

    ));
}

// Hook the function to enqueue scripts and styles
add_action('wp_enqueue_scripts', 'lorybot_enqueue_scripts');
