<?php
/**
 * This script contains callback functions for the LoryBot plugin's settings.
 * Each function corresponds to a specific setting, handling its display and behavior in the settings page.
 */

/**
 * Callback for displaying the section description.
 */
function lorybot_section_callback() {
    echo '<p>Main settings for Lorybot.</p>';
}

/**
 * Retrieves a specific LoryBot option with an optional default value.
 *
 * @param string $option_name The name of the option to retrieve.
 * @param string $default The default value to return if the option is not set.
 * @return mixed The value of the option or the default value.
 */
function get_lorybot_option($option_name, $default = '') {
    $options = get_option('lorybot_options');
    return $options[$option_name] ?? $default;
}

/**
 * Callback for displaying the chat enabled checkbox.
 */
function lorybot_enable_callback() {
    $chat_enabled = get_lorybot_option('chat_enabled');
    ?>
    <input type="checkbox" name="lorybot_options[chat_enabled]" id="lorybot_enabled_field" <?php checked($chat_enabled, 'on'); ?>>
    <?php
}

/**
 * Callback for displaying the custom ID text field.
 */
function lorybot_custom_id_callback() {
    $custom_id = get_lorybot_option('custom_id');
    echo '<input value="' . esc_attr($custom_id) . '" type="text" name="lorybot_options[custom_id]" id="lorybot_custom_id_field" readonly>';
}

/**
 * Callback for displaying the prompt textarea.
 */
function lorybot_prompt_callback() {
    $prompt = get_lorybot_option('prompt');
    echo '<textarea id="prompt_editor_id" name="lorybot_options[prompt]" rows="5" cols="60">' . esc_textarea($prompt) . '</textarea>';
}

/**
 * Callback for displaying the embedding editor.
 */
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

/**
 * Callback for displaying the chat display textarea.
 * It loads the default HTML from a file if the setting is not set.
 */
function lorybot_chat_display_callback() {
    $chat_html_path = plugin_dir_path(__FILE__) . 'chat-html.php';
    $chat_display = get_lorybot_option('chat_display', file_get_contents($chat_html_path));

    ?>
    <textarea name="lorybot_options[chat_display]" id="lorybot_chat_display_field" rows="10" cols="50"><?php echo esc_textarea($chat_display); ?></textarea>
    <?php
}

/**
 * Callback for displaying a color picker.
 *
 * @param string $option_name The name of the color option to display.
 */
function lorybot_color_picker_callback($option_name) {
    $color_value = get_lorybot_option($option_name);
    ?>
    <input class="lorybot-color-picker" type="text" name="lorybot_options[<?php echo $option_name; ?>]" value="<?php echo esc_attr($color_value); ?>">
    <?php
    lorybot_color_picker_script();
}

/**
 * Adds the color picker script once per page load.
 */
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

/**
 * Callbacks for color settings.
 */
function lorybot_main_color_callback() { lorybot_color_picker_callback('main_color'); }
function lorybot_background_color_callback() { lorybot_color_picker_callback('background_color'); }
function lorybot_title_color_callback() { lorybot_color_picker_callback('title_color'); }
?>
