<?php

// Function when the plugin is activated
function lorybot_activate() {

    // Set the flag to indicate activation is in progress
    $GLOBALS['is_lorybot_activating'] = true;

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

    // Reset the flag after activation is done
    $GLOBALS['is_lorybot_activating'] = false;

    add_option('lorybot_do_activation_redirect', true);

}

// Activation hook
register_activation_hook(__FILE__, 'lorybot_activate');


?>