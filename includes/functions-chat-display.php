<?php
/**
 * Handle the display of the chatbot interface on the front-end
 */

 function my_chatbot_display() {
     $options = get_option('my_chatbot_options');
 
     if (isset($options['enabled']) && $options['enabled'] === 'on') {
         echo isset($options['chat_display']) && !empty($options['chat_display']) ? 
              $options['chat_display'] : 
              '<button class="chatbot-toggler">
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
               </div>';
     }
 }
 
 add_action('wp_footer', 'my_chatbot_display');
 ?>
 