<?php

function getMainDomain() {
    $hostParts = explode('.', $_SERVER['HTTP_HOST']);
    return (count($hostParts) > 1) ? 
           $hostParts[count($hostParts) - 2] . '.' . $hostParts[count($hostParts) - 1] : 
           $_SERVER['HTTP_HOST'];
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

function set_user_id_cookie() {
    if (!isset($_COOKIE['user_id'])) {
        setcookie('user_id', generate_uuid(), time() + 86400, "/", '', isset($_SERVER["HTTPS"]), true);
    }
}
add_action('init', 'set_user_id_cookie');

?>
