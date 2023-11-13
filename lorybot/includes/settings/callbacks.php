<?php

function lorybot_section_callback() {
    echo '<p>Main settings for Lorybot.</p>';
}

function get_lorybot_option($option_name, $default = '') {
    $options = get_option('lorybot_options');
    return isset($options[$option_name]) ? $options[$option_name] : $default;
}

function lorybot_enable_callback() {
    $chat_enabled = get_lorybot_option('chat_enabled');
    ?>
    <input type="checkbox" name="lorybot_options[chat_enabled]" id="lorybot_enabled_field" <?php checked($chat_enabled, 'on'); ?>>
    <?php
}

function lorybot_text_input_callback($option_name, $type = 'text') {
    $option_value = get_lorybot_option($option_name);
    ?>
    <input value="<?php echo esc_attr($option_value); ?>" type="<?php echo $type; ?>" name="lorybot_options[<?php echo $option_name; ?>]" id="lorybot_<?php echo $option_name; ?>_field">
    <?php
}

function lorybot_api_callback() {
    lorybot_text_input_callback('lorybot_api');
}

function lorybot_prompt_callback() {
    $prompt = get_lorybot_option('prompt');
    echo '<textarea id="prompt_editor_id" name="lorybot_options[prompt]" rows="5" cols="60">' . esc_textarea($prompt) . '</textarea>';
}

function lorybot_embedding_callback() {
    $embedding = get_lorybot_option('embedding');
    wp_editor($embedding, 'embedding_editor_id', [
        'textarea_name' => 'lorybot_options[embedding]',
        'media_buttons' => false,
        'textarea_rows' => 20,
        'tinymce' => true,
        'quicktags' => true
    ]);
}

function lorybot_chat_display_callback() {
    // Get the absolute path to the chat-html.php file
    $chat_html_path = plugin_dir_path(__FILE__) . 'chat-html.php';

    // Use file_get_contents with the absolute path
    $chat_display = get_lorybot_option('chat_display', file_get_contents($chat_html_path));

    ?>
    <textarea name="lorybot_options[chat_display]" id="lorybot_chat_display_field" rows="10" cols="50"><?php echo esc_textarea($chat_display); ?></textarea>
    <?php
}


function lorybot_color_picker_callback($option_name) {
    $color_value = get_lorybot_option($option_name);
    ?>
    <input class="lorybot-color-picker" type="text" name="lorybot_options[<?php echo $option_name; ?>]" value="<?php echo esc_attr($color_value); ?>">
    <?php
    lorybot_color_picker_script();
}

function lorybot_color_picker_script() {
    static $script_added = false;
    if (!$script_added) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.lorybot-color-picker').wpColorPicker();
            });
        </script>
        <?php
        $script_added = true;
    }
}

function lorybot_main_color_callback() {
    lorybot_color_picker_callback('main_color');
}

function lorybot_background_color_callback() {
    lorybot_color_picker_callback('background_color');
}

function lorybot_title_color_callback() {
    lorybot_color_picker_callback('title_color');
}
