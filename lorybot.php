<?php
/**
 * Plugin Name: LoryBot | Advanced AI Chatbot
 * Plugin URI: https://www.lorybot.com
 * Description: Build better customer relationships and boost sales with LoryBot's AI chatbot. Using your unique business data, LoryBot provides personalized, helpful responses, creating meaningful conversations that keep visitors engaged. Optimize the return from your website traffic with tailored, individualized interactions powered by leading-edge AI technology.
 * Version: 1.0
 * Author: Danilo Vaccalluzzo
 * Author URI: https://www.linkedin.com/in/danilovaccalluzzo/
 */

defined('ABSPATH') or exit;

$plugin_dir = plugin_dir_path(__FILE__);

// Set server URL
initialize_server_url();

// Include necessary files
require_once $plugin_dir . 'includes/utils.php';
require_once $plugin_dir . 'includes/functions-settings.php';
require_once $plugin_dir . 'includes/functions-enqueue-scripts.php';
require_once $plugin_dir . 'includes/functions-chat-display.php';
require_once $plugin_dir . 'routes/activate.php';
require_once $plugin_dir . 'routes/deactivate.php';

register_activation_hook(__FILE__, 'Lorybot\Activate\lorybot_activate');
register_deactivation_hook(__FILE__, 'Lorybot\Deactivate\lorybot_deactivate');

function initialize_server_url() {
    // Set the server URL as an option
    update_option('lorybot_server_url', 'https://lorybot-q4yzxnb64q-uc.a.run.app/');
}
