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
#update_option('lorybot_server_url', 'https://lorybot-q4yzxnb64q-uc.a.run.app/');
update_option('lorybot_server_url', 'http://127.0.0.1:8002/');


// Global variable to track activation status
$GLOBALS['is_lorybot_activating'] = false;
error_log("is_lorybot_activating: " . $GLOBALS['is_lorybot_activating']);
  
require_once plugin_dir_path(__FILE__) . 'includes/utils.php';

// Functions
require_once plugin_dir_path(__FILE__) . 'includes/functions-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions-chat-display.php';


// Routes
require_once plugin_dir_path(__FILE__) . 'routes/activate.php';
require_once plugin_dir_path(__FILE__) . 'routes/deactivate.php';



// Activation hook
register_activation_hook(__FILE__, 'lorybot_activate');
// Deactivation hook
register_deactivation_hook(__FILE__, 'lorybot_deactivate');

