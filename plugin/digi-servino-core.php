<?php
/**
 * Plugin Name:       DigiServino Core
 * Plugin URI:        https://example.com/digi-servino
 * Description:       Core functionality for the DigiServino IT SaaS Platform (Tickets, Roles, API).
 * Version:           1.0.0
 * Author:            DigiServino Team
 * Text Domain:       digi-servino-core
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define Constants
define( 'DS_VERSION', '1.0.0' );
define( 'DS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoloader for our classes
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'DigiServino\\';

    // Base directory for the namespace prefix
    $base_dir = DS_PLUGIN_DIR . 'includes/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    // We handle the "Core/Activator" -> "Core/class-ds-activator.php" mapping manually or simple mapping below:
    
    // Simple PSR-4 to WP filename conversion (UpperCamelCase to class-kebab-case) is standard, 
    // but for simplicity here we will map folder structures directly.
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // NOTE: In a real production environment, use Composer. 
    // For this manual build, ensure filenames match the class calls.
    // E.g. DigiServino\Core\Activator looks for includes/Core/Activator.php 
    // (Or rename your files to match the class names exactly for this simple autoloader).
    
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * The code that runs during plugin activation.
 */
function activate_digi_servino_core() {
	require_once DS_PLUGIN_DIR . 'includes/Core/Activator.php';
	\DigiServino\Core\Activator::activate();
}
register_activation_hook( __FILE__, 'activate_digi_servino_core' );

/**
 * Initialize REST API
 */
add_action('rest_api_init', function() {
    require_once DS_PLUGIN_DIR . 'includes/Api/Ticket_Controller.php';
    $tickets_controller = new \DigiServino\Api\Ticket_Controller();
    $tickets_controller->register_routes();
});

// Inside DigiServino Core Plugin
add_action('rest_api_init', function() {
    register_rest_route('digi-servino/v1', '/auth/login', [
        'methods' => 'POST',
        'callback' => 'ds_ajax_login_handler',
        'permission_callback' => '__return_true'
    ]);
    
    register_rest_route('digi-servino/v1', '/auth/register', [
        'methods' => 'POST',
        'callback' => 'ds_ajax_register_handler',
        'permission_callback' => '__return_true'
    ]);
});

function ds_ajax_login_handler($request) {
    $params = $request->get_json_params();
    $creds = [
        'user_login'    => sanitize_text_field($params['username']),
        'user_password' => $params['password'],
        'remember'      => true
    ];

    $user = wp_signon($creds, is_ssl());

    if (is_wp_error($user)) {
        return new WP_Error('login_failed', 'اطلاعات ورود اشتباه است', ['status' => 403]);
    }

    // Determine Redirect URL based on Role
    $redirect = home_url('/my-account'); // Default for clients
    
    // logic inside the plugin login handler
    if (in_array('administrator', (array)$user->roles)) {
        $redirect = home_url('/admin-dashboard/'); 
    } elseif (in_array('ds_technician', (array)$user->roles)) {
        $redirect = home_url('/tech-dashboard/');
    } else {
        // Both Client and New Users go here
        $redirect = home_url('/client-dashboard/'); 
    }

    return rest_ensure_response([
        'success' => true, 
        'redirect' => $redirect,
        'message' => 'خوش آمدید! در حال انتقال...'
    ]);
}

/**
 * AJAX Register Handler
 */
function ds_ajax_register_handler($request) {
    $params = $request->get_json_params();
    
    $username = sanitize_user($params['username']);
    $email    = sanitize_email($params['username']); // Using username field as email for simplicity
    $password = $params['password'];
    $name     = sanitize_text_field($params['name']);

    // Validation
    if (empty($username) || empty($password)) {
        return new WP_Error('missing_fields', 'لطفاً تمامی فیلدها را پر کنید', ['status' => 400]);
    }

    if (email_exists($email) || username_exists($username)) {
        return new WP_Error('user_exists', 'این ایمیل یا نام کاربری قبلاً ثبت شده است', ['status' => 400]);
    }

    // Create User
    $user_id = wp_insert_user([
        'user_login' => $username,
        'user_pass'  => $password,
        'user_email' => $email,
        'display_name' => $name,
        'role'       => 'ds_client' // Assigning the IT Client role automatically
    ]);

    if (is_wp_error($user_id)) {
        return new WP_Error('registration_failed', 'خطا در ثبت نام. مجدداً تلاش کنید', ['status' => 500]);
    }

    // Automatically log them in after registration
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    return rest_ensure_response([
        'success' => true, 
        'redirect' => home_url('/my-account'),
        'message' => 'ثبت‌نام با موفقیت انجام شد'
    ]);
}


// Load Admin Menu logic
require_once DS_PLUGIN_DIR . 'includes/Admin/Admin_Menu.php';