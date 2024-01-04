<?php
/**
 * Enqueue styles and scripts for the Chatbot plugin.
 */
function lorybot_enqueue_scripts() {
    // Enqueue the chatbot's CSS
    wp_enqueue_style('lorybot-style', plugin_dir_url(__FILE__) . '../assets/css/style.css');

    // Enqueue the chatbot's JavaScript, dependent on jQuery
    wp_enqueue_script('lorybot-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', array('jquery'), '1.0', true);

    // Enqueue Showdown.js from CDN
    wp_enqueue_script('showdown-script', 'https://cdn.jsdelivr.net/npm/showdown@2.1.0/dist/showdown.min.js', array(), '2.1.0', true);


    // Enqueue Google Material Symbols fonts
    wp_enqueue_style('material-symbols-outlined', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0');
    wp_enqueue_style('material-symbols-rounded', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0');

    // Retrieve the lorybot options and set default values for certain keys if they are not set
    $lorybot_options = get_option('lorybot_options');
    $main_color = isset($lorybot_options['main_color']) ? $lorybot_options['main_color'] : '#0e456c';
    $background_color = isset($lorybot_options['background_color']) ? $lorybot_options['background_color'] : '#ffffff';
    $title_color = isset($lorybot_options['title_color']) ? $lorybot_options['title_color'] : '#ffffff';

    // Pass PHP variables to JavaScript, ensuring no undefined index errors occur
    wp_localize_script('lorybot-script', 'chatbot_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'plugin_url' => plugin_dir_url(dirname(__FILE__)),
        'prompt' => isset($lorybot_options['prompt']) ? $lorybot_options['prompt'] : '',
        'client_id' => getMainDomain(),
        'main_color' => $main_color,
        'background_color' => $background_color,
        'title_color' => $title_color,
        'chat_display' => isset($lorybot_options['chat_display']) ? $lorybot_options['chat_display'] : '',
        'custom_id' => isset($lorybot_options['custom_id']) ? $lorybot_options['custom_id'] : '',
        'server_url' => get_option('lorybot_server_url'),
    ));
}

// Hook the function to enqueue scripts and styles
add_action('wp_enqueue_scripts', 'lorybot_enqueue_scripts');
