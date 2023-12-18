<?php
ob_start();
function getMainDomain() {
    if (isset($_SERVER['HTTP_HOST'])) {
        $hostParts = explode('.', $_SERVER['HTTP_HOST']);
        $domain = (count($hostParts) > 1) ? $hostParts[count($hostParts) - 2] . '.' . $hostParts[count($hostParts) - 1] : $_SERVER['HTTP_HOST'];

        // Correct use of error_log function
        error_log("getMainDomain: " . $domain);

        return $domain;
    } else {
        // Handle the case where HTTP_HOST is not set
        error_log("HTTP_HOST is not set");
        return null;
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

function generate_uuid() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}


add_action('init', 'set_user_id_cookie');
function set_user_id_cookie() {
    if (!isset($_COOKIE['user_id'])) {
        $user_id = generate_uuid(); // Ensure this function exists in your plugin to generate UUID.
        setcookie('user_id', $user_id, time() + (86400), "/"); // Set for 1 day
    }
}

function lorybot_enqueue_color_picker($hook_suffix) {
    if ($hook_suffix === 'settings_page_lorybot-settings') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'lorybot_enqueue_color_picker');


function lorybot_end_output_buffering() {
    // Check if output buffering is active before ending it.
    if (ob_get_level() > 0 && ob_get_length() > 0) {
        ob_end_flush();
    }
}

add_action('shutdown', 'lorybot_end_output_buffering');
?>
