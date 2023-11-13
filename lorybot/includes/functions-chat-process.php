<?php

function process_chatbot_message() {
    // Sanitize and assign user and chat details
    $user_id = sanitize_text_field($_COOKIE['user_id'] ?? '');
    $user_message = sanitize_text_field($_POST['message'] ?? '');
    $client_id = getMainDomain();
    $custom_id = get_option('lorybot_custom_id');

    // Prepare the data payload for the POST request
    $data = http_build_query([
        'project_domain' => $client_id,
        'message' => $user_message,
        'user_id' => $user_id,
        'custom_id' => $custom_id
    ]);

    // Set up the HTTP context for the POST request
    $context = stream_context_create([
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => $data,
        ],
    ]);
    $lorybot_server_url = get_option('lorybot_server_url');
    // Perform the POST request and capture the response
    $response = file_get_contents($lorybot_server_url . "/chat", false, $context);

    echo json_encode(['response' => $response]);
    wp_die(); // Required to terminate immediately and return a proper response
}

add_action('wp_ajax_nopriv_process_chatbot_message', 'process_chatbot_message');
add_action('wp_ajax_process_chatbot_message', 'process_chatbot_message');

?>
