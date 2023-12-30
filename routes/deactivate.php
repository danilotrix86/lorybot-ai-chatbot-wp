<?php

// Function when the plugin is activated
function lorybot_deactivate() {


    $lorybot_server_url = get_option('lorybot_server_url');
    $options = get_option('lorybot_options');
    $custom_id = isset($options['custom_id']) ? $options['custom_id'] : ''; 
   
    $json = [
        'domain' => getMainDomain(),
        'custom_id' => $custom_id,
    ];

    $args = array(
        'method'    => 'POST',
        'headers'   => [
            'Content-Type' => 'application/json',
            'LORYBOT-API-KEY' => $custom_id, // Add API key to the request header
        ],
        'body'      => json_encode($json),
        'sslverify' => false,
        'timeout'   => 60,
    );

    $response = wp_remote_post($lorybot_server_url . "deactivate", $args);

    // Check if the option already exists
    if (false === get_option('lorybot_options')) {
        // Option does not exist, so add the default value
        add_option('lorybot_options', array());
    }


    if (is_wp_error($response)) {
        echo "Something went wrong: " . $response->get_error_message();
    } else {
        echo 'POST request sent successfully!';
    }

    delete_option('lorybot_options');

}

// Deactivation hook
register_deactivation_hook(__FILE__, 'lorybot_deactivate');

?>