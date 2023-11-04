<?php
/**
 * Handle the display of the chatbot interface on the front-end
 */


function generate_uuid() {
  return sprintf(
      '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), // time_low
      mt_rand(0, 0xffff), // time_mid
      mt_rand(0, 0x0fff) | 0x4000, // time_hi_and_version
      mt_rand(0, 0x3fff) | 0x8000, // clk_seq_hi_res
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff) // node
  );
}
function set_user_id_cookie() {
  if (!isset($_COOKIE['user_id'])) {
    $user_id = generate_uuid();
    // Ensure the `setcookie` is called before any output
    setcookie('user_id', $user_id, time() + 86400, "/", '', isset($_SERVER["HTTPS"]), true);
  }
}

function my_chatbot_display() {

    $options = get_option('my_chatbot_options');

    // Check if the chatbot is enabled
    if (isset($options['enabled']) && $options['enabled'] === 'on') {
        // Check if the chat_display option is set and not empty
        if (isset($options['chat_display']) && !empty($options['chat_display'])) {
            echo $options['chat_display'];
        } else {
            echo '
              <button class="chatbot-toggler">
                <span class="material-symbols-rounded">mode_comment</span>
                <span class="material-symbols-outlined">close</span>
              </button>
              <div class="chatbot">
                <header>
                  <h2>Chatbot</h2>
                  <span class="close-btn material-symbols-outlined">close</span>
                </header>
                <ul class="chatbox">
                  <li class="chat incoming">
                    <span class="material-symbols-outlined">smart_toy</span>
                    <p>Hi there 👋<br>How can I help you today?</p>
                  </li>
                </ul>
                <div class="chat-input">
                  <textarea placeholder="Enter a message..." spellcheck="false" required></textarea>
                  <span id="send-btn" class="material-symbols-rounded">send</span>
                </div>
              </div>
            ';
        }
    }
}


// Hook the set_user_id_cookie function early in the WordPress init stage
add_action('init', 'set_user_id_cookie');

// Continue to hook the display function to the wp_footer
add_action('wp_footer', 'my_chatbot_display');