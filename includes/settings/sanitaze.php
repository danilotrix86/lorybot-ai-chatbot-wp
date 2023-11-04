<?php

// Sanitize the input options
function my_chatbot_sanitize_options($input) {
    $output = array();

    if (isset($input['url'])) {
        $output['url'] = esc_url_raw($input['url']);
    }
    

    // openai key sanitaze


    if (isset($input['enabled'])) {
        $output['enabled'] = $input['enabled'];
    }

    if (isset($input['lorybot_api'])) {
        $output['lorybot_api'] = sanitize_text_field($input['lorybot_api']);
    }

    if (isset($input['prompt'])) {
        $output['prompt'] = $input['prompt'];
    }
    if (isset($input['embedding'])) {
        $output['embedding'] = $input['embedding'];
    }

    if (isset($input['chat_display'])) {
        $output['chat_display'] = $input['chat_display'];
    }

    if (isset($input['main_color']) && preg_match('/^#[a-f0-9]{6}$/i', $input['main_color'])) {
        $output['main_color'] = sanitize_text_field($input['main_color']);
    }

    if (isset($input['background_color']) && preg_match('/^#[a-f0-9]{6}$/i', $input['background_color'])) {
        $output['background_color'] = sanitize_text_field($input['background_color']);
    }


    return $output;
}