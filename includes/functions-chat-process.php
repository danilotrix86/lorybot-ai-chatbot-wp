<?php
/**
 * Handle the processing of chatbot messages
 */

function process_chatbot_message() {
    

    $user_id = sanitize_text_field($_COOKIE['user_id']);



    if (isset($_POST['message'])) {
        $user_message = $_POST['message'];
    }

    if (isset($_POST['prompt'])) {
        $prompt = ($_POST['prompt']);
    }
    

    $client_id = getMainDomain();
    

    if (isset($_POST['chat_display'])) {
        $chat_display = sanitize_text_field($_POST['chat_display']);
    }

    global $lorybot_server_url;

    $url = $lorybot_server_url."/chat";

    $data = array(
        'project_domain' => $client_id,
        'message' => $user_message,
        'chat_display' => $chat_display,
        'user_id' => $user_id,
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    $log_file = plugin_dir_path(__FILE__) . '../log.log';

    if ($response === FALSE) {
        file_put_contents($log_file, "Failed to get response from $url\n", FILE_APPEND);
        // Handle error here
    } 

    echo json_encode(array('response' => $response));

    wp_die(); // This is required to terminate immediately and return a proper response
}

// Hook the function to handle AJAX requests for processing chat messages
add_action('wp_ajax_nopriv_process_chatbot_message', 'process_chatbot_message');
add_action('wp_ajax_process_chatbot_message', 'process_chatbot_message');
