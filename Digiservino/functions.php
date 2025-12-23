<?php
// 1. Load Scripts and Styles
add_action('wp_enqueue_scripts', function() {
    // Tailwind
    wp_enqueue_script('tailwindcss', 'https://cdn.tailwindcss.com', [], '3.3', false);
    // Alpine.js
    wp_enqueue_script('alpinejs', 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js', [], '3.12', true);
    // App Logic (only if file exists)
    wp_enqueue_script('ds-app-js', get_template_directory_uri() . '/assets/js/app.js', ['alpinejs'], '1.0', true);

    wp_localize_script('ds-app-js', 'dsSettings', [
        'root' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest'),
        'current_user_id' => get_current_user_id()
    ]);
});

// 2. Kill Admin Bar & Dashboard Access
add_filter('show_admin_bar', '__return_false');

add_action('template_redirect', function () {
    ob_start(function ($html) {
        return preg_replace(
            '#<div id="footer".*?</div>#is',
            '',
            $html
        );
    });
});


add_action('admin_init', function() {
    if (is_admin() && !current_user_can('administrator') && !defined('DOING_AJAX')) {
        wp_redirect(home_url('/my-account'));
        exit;
    }
});


// 4. Redirects
add_action('init', function() {
    global $pagenow;
    if( 'wp-login.php' == $pagenow && !isset($_GET['action']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
        wp_redirect(home_url('/my-account/'));
        exit();
    }
});

add_filter('logout_redirect', function() {
    return home_url('/');
}, 10, 3);