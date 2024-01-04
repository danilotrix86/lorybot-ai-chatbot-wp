<?php

// Function when the plugin is activated
function lorybot_activate() {


    error_log("lorybot_activate");

    // Set the flag to indicate activation is in progress
    $GLOBALS['is_lorybot_activating'] = true;
    // Clear existing options before setting new ones
    error_log("Clearing existing options");
    delete_option('lorybot_options');

    $lorybot_server_url = get_option('lorybot_server_url');
    $customID = generate_uuid();
    $options = get_option('lorybot_options', array());
    $options['custom_id'] = $customID;  // Store custom_id in lorybot_options array
    update_option('lorybot_options', $options);

    error_log("Custom ID: " . $customID);

    $json = [
        'domain' => getMainDomain(),
        'custom_id' => $customID,
    ];

    $args = array(
        'method'    => 'POST',
        'headers'   => array(
            'Content-Type' => 'application/json',
        ),
        'body'      => json_encode($json),
        'sslverify' => false,
        'timeout'   => 60,
    );

    $response = wp_remote_post($lorybot_server_url . "activate", $args);
    error_log("URL: " . $lorybot_server_url . "activate");

    // Check if the response is an instance of WP_Error
    if (is_wp_error($response)) {
        // Handle the error. You can log the error message.
        $error_message = $response->get_error_message();
        error_log("Error: " . $error_message);
    } else {
        // If it's not an error, convert the response to a string and log it.
        $response_string = print_r($response, true); // Convert the response to a readable string
        error_log("Response: " . $response_string);
    }   

    // Check if the option already exists
    if (false === get_option('lorybot_options')) {
        // Option does not exist, so add the default value
        add_option('lorybot_options', array());
    }


    // Reset the flag after activation is done
    $GLOBALS['is_lorybot_activating'] = false;

    add_option('lorybot_do_activation_redirect', true);

}




?>