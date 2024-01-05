<?php
// Start output buffering
ob_start();

/**
 * Retrieves the main domain from the HTTP_HOST server variable.
 *
 * @return string|null The main domain or null if HTTP_HOST is not set.
 */
function getMainDomain() {
    if (isset($_SERVER['HTTP_HOST'])) {
        $hostParts = explode('.', $_SERVER['HTTP_HOST']);
        // Return the domain by joining the last two parts of the host
        return count($hostParts) > 1 ? implode('.', array_slice($hostParts, -2)) : $_SERVER['HTTP_HOST'];
    } else {
        error_log("HTTP_HOST is not set");
        return null;
    }
}

/**
 * Redirects to the LoryBot settings page upon plugin activation.
 */
function lorybot_redirect() {
    if (get_option('lorybot_do_activation_redirect', false)) {
        delete_option('lorybot_do_activation_redirect');
        wp_redirect(admin_url('options-general.php?page=lorybot-settings'));
        exit;
    }
}
add_action('admin_init', 'lorybot_redirect');

/**
 * Generates a universally unique identifier (UUID).
 *
 * @return string The generated UUID.
 */
function generate_uuid() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, // Set the version to 4 (randomly generated UUID)
        mt_rand(0, 0x3fff) | 0x8000, // Set the variant to DCE 1.1, ISO/IEC 11578:1996
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

/**
 * Enqueues the WordPress color picker on the LoryBot settings page.
 *
 * @param string $hook_suffix The current admin page's hook suffix.
 */
function lorybot_enqueue_color_picker($hook_suffix) {
    if ($hook_suffix === 'settings_page_lorybot-settings') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'lorybot_enqueue_color_picker');

/**
 * Ends output buffering and flushes the output buffer.
 */
function lorybot_end_output_buffering() {
    if (ob_get_length()) {
        ob_end_flush();
    }
}
add_action('shutdown', 'lorybot_end_output_buffering');
