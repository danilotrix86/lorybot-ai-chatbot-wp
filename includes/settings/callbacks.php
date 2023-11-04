<?php


// Settings Section Callback
function my_chatbot_section_callback() {
    echo '<p>Main settings for the Chatbot.</p>';
}


// ENABLED/ON/OFF Callback 
function my_chatbot_enable_callback() {
    $options = get_option('my_chatbot_options');
    $enabled = isset($options['enabled']) ? $options['enabled'] : '';
    ?>
    <input type="checkbox" name="my_chatbot_options[enabled]" id="my_chatbot_enabled_field" <?php checked($enabled, 'on'); ?>>
    <?php
}


// LoryBot KEY Callback
function my_chatbot_lorybot_api_callback() {
    $options = get_option('my_chatbot_options');
    $lorybot_api = isset($options['lorybot_api']) ? $options['lorybot_api'] : '';
    ?>
    <input value="<?php echo esc_attr($lorybot_api); ?>" type="text" name="my_chatbot_options[lorybot_api]" id="my_chatbot_lorybot_api_field">
    <?php
}


// PROMPT Callback
function my_chatbot_prompt_callback() {
    $options = get_option('my_chatbot_options');
    $prompt = isset($options['prompt']) ? $options['prompt'] : '';

    // Create the regular textarea
    echo '<textarea id="prompt_editor_id" name="my_chatbot_options[prompt]" rows="5" cols="60">' . esc_textarea($prompt) . '</textarea>';
}



// Embedding Callback
function my_chatbot_embedding_callback() {
    $options = get_option('my_chatbot_options');
    $embedding = isset($options['embedding']) ? $options['embedding'] : '';
    
    // Create the WYSIWYG editor
    wp_editor($embedding, 'embedding_editor_id', array(
        'textarea_name' => 'my_chatbot_options[embedding]',
        'media_buttons' => false,
        'textarea_rows' => 20,
        'tinymce' => true,
        'quicktags' => true
    ));
}

//chat display callback
function my_chatbot_chat_display_callback() {
    $options = get_option('my_chatbot_options');
    $chat_display = isset($options['chat_display']) ? $options['chat_display'] : '';
    $sample_html = '
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
    '; // Set your sample HTML here
    
    if (empty($chat_display)) {
        $chat_display = $sample_html;
    }
    
    ?>
    <textarea name="my_chatbot_options[chat_display]" id="my_chatbot_chat_display_field" rows="10" cols="50"><?php echo esc_textarea($chat_display); ?></textarea>
    <?php
}

function my_chatbot_main_color_callback() {
    $options = get_option('my_chatbot_options');
    $main_color = isset($options['main_color']) ? $options['main_color'] : '';
    ?>
    <input class="my-chatbot-color-picker" type="text" name="my_chatbot_options[main_color]" value="<?php echo esc_attr($main_color); ?>">
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.my-chatbot-color-picker').wpColorPicker();
        });
    </script>
    <?php
}

function my_chatbot_background_color_callback() {
    $options = get_option('my_chatbot_options');
    $background_color = isset($options['background_color']) ? $options['background_color'] : '';
    ?>
    <input class="my-chatbot-color-picker" type="text" name="my_chatbot_options[background_color]" value="<?php echo esc_attr($background_color); ?>">
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.my-chatbot-color-picker').wpColorPicker();
        });
    </script>
    <?php
}