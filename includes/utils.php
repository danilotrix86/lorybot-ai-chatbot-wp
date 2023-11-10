<?php

function getMainDomain() {
    $hostParts = explode('.', $_SERVER['HTTP_HOST']);
    $numParts = count($hostParts);

    // If there are enough parts in the hostname (e.g., www.example.com)
    if ($numParts > 1) {
        return $hostParts[$numParts - 2] . '.' . $hostParts[$numParts - 1];
    }
    // If the hostname is something like 'localhost' or an IP address
    else {
        return $_SERVER['HTTP_HOST'];
    }
}


function lorybot_redirect() {
    if (get_option('lorybot_do_activation_redirect', false)) {
        delete_option('lorybot_do_activation_redirect');
        wp_redirect(admin_url('options-general.php?page=lorybot-settings'));
        exit;
    }
}
add_action('admin_init', 'lorybot_redirect');
?>