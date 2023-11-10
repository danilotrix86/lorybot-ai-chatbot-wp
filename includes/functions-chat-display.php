<?php
/**
 * Handle the display of the chatbot interface on the front-end
 */

 function lorybot_display() {
  $options = get_option('lorybot_options');

  if (isset($options['chat_enabled']) && $options['chat_enabled'] === 'on') {
      if (isset($options['chat_display']) && !empty($options['chat_display'])) {
          echo $options['chat_display'];
      } else {
          // Get the absolute path to the chat-html.php file
          $chat_html_path = plugin_dir_path(__FILE__) . 'settings/chat-html.php';

          // Include the file using the absolute path
          include($chat_html_path);
      }
  }
}


add_action('wp_footer', 'lorybot_display');