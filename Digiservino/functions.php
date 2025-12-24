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
add_filter('admin_footer_text', '__return_empty_string', 999);
add_filter('update_footer', '__return_empty_string', 999);

// اصلاح بخش ۲ در functions.php
add_action('admin_init', function() {
    // اگر درخواست AJAX بود یا کاربر مدیر کل بود، کاری نداشته باش
    if (defined('DOING_AJAX') && DOING_AJAX) return;
    if (current_user_can('administrator')) return;

    // بررسی نقش تکنسین بر اساس کدهای پلاگین (ds_technician)
    $user = wp_get_current_user();
    if (in_array('ds_technician', (array) $user->roles)) {
        // اگر تکنسین خواست وارد صفحات مدیریت وردپرس (/wp-admin) شود،
        // او را به صفحه داشبورد خودش بفرست، نه به my-account
        if (is_admin()) {
            wp_redirect(home_url('/tech-dashboard/'));
            exit;
        }
        return; // اجازه عبور در فرانت‌سایت
    }

    // برای بقیه (مشتریان)، دسترسی به ادمین ممنوع و ریدایرکت به حساب کاربری
    if (is_admin()) {
        wp_redirect(home_url('/my-account/'));
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



// client dashboard
// 1. Force Logout Redirection to Homepage
add_action('wp_logout', function(){
    wp_redirect(home_url());
    exit();
});

// 2. Add custom "Client" Menu support
add_action('after_setup_theme', function() {
    register_nav_menus([
        'client_dashboard_menu' => 'پنل کاربری - سایدبار',
    ]);
});

// 3. Subscription Status Shortcode (for use in dashboard)
add_shortcode('user_subscription', function() {
    if (!is_user_logged_in()) return 'No active plan';
    // Logic to pull from your DB/Plugin later
    return '<span class="text-indigo-600 font-bold">طرح فعال شما: طلایی</span>';
});

// 4. Ticketing History Helper (Mockup for now)
function get_client_tickets($user_id) {
    // This will eventually query your "Tickets" Custom Post Type
    return get_posts(['post_type' => 'ds_tickets', 'author' => $user_id]);
}

// 1. Register Tickets Custom Post Type
add_action('init', function() {
    register_post_type('ds_ticket', [
        'labels' => ['name' => 'تیکت‌ها', 'singular_name' => 'تیکت'],
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => ['title', 'editor', 'author'],
        'has_archive' => false,
    ]);
});

// 2. Helper: Get User Subscription Label
function ds_get_user_subscription($user_id) {
    $plan = get_user_meta($user_id, 'ds_subscription_plan', true);
    return $plan ? $plan : 'بدون اشتراک فعال';
}

// 3. Helper: Get User Ticket Count
function ds_get_user_ticket_count($user_id, $status = 'any') {
    $args = [
        'post_type' => 'ds_ticket',
        'author' => $user_id,
        'post_status' => $status,
        'fields' => 'ids',
        'posts_per_page' => -1
    ];
    return count(get_posts($args));
}

add_action('add_meta_boxes', function() {
    add_meta_box('ds_remote_info', 'اطلاعات دسترسی ریموت', function($post) {
        $type = get_post_meta($post->ID, '_remote_type', true);
        $id = get_post_meta($post->ID, '_remote_id', true);
        echo "<p><strong>نوع نرم‌افزار:</strong> $type</p>";
        echo "<p><strong>کد/لینک اتصال:</strong> $id</p>";
    }, 'ds_ticket', 'side');
});

add_filter('manage_ds_ticket_posts_columns', function($columns) {
    $columns['ticket_type'] = 'نوع درخواست';
    return $columns;
});

add_action('manage_ds_ticket_posts_custom_column', function($column, $post_id) {
    if ($column === 'ticket_type') {
        $is_service = get_post_meta($post_id, '_is_service_request', true);
        echo $is_service ? '<span style="color:blue">🛠️ خدمات در محل</span>' : '<span style="color:orange">🎫 پشتیبانی فنی</span>';
    }
}, 10, 2);


// zarrinpal payment
// تابع ارسال به درگاه زرین‌پال
function ds_request_payment($amount, $description, $callback_url) {
    $merchant_id = 'daaf9469-6f2e-4861-8381-e42934c2e809'; // مرچنت آیدی شما
    
    $data = array(
        'merchant_id' => $merchant_id,
        'amount' => (int)$amount * 10, // تبدیل تومان به ریال
        'callback_url' => $callback_url,
        'description' => $description,
    );

    $jsonData = json_encode($data);
    $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)));
    
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    
    $result = json_decode($result, true);
    
    // اگر خطایی وجود داشت، در لاگ وردپرس ذخیره شود
    if (isset($result['errors'])) {
        error_log('Zarinpal Error: ' . print_r($result['errors'], true));
    }

    if (isset($result['data']['authority'])) {
        return 'https://www.zarinpal.com/pg/StartPay/' . $result['data']['authority'];
    }
    return false;
}

function ds_user_has_active_subscription($user_id) {
    // ۱. ادمین همیشه و در همه جا دسترسی کامل دارد (بالاترین اولویت)
    if (user_can($user_id, 'administrator')) {
        return true;
    }

    // ۲. در صفحات زیر، فرم‌ها نباید برای هیچکس (حتی بدون اشتراک) قفل شوند
    if (is_page('service-request') || is_page('payment-verify')) {
        return true;
    }

    // ۳. بررسی اشتراک واقعی برای کاربران عادی در صفحه تیکت ریموت
    $expiry = get_user_meta($user_id, 'ds_expiry', true);
    
    // اگر تاریخی ثبت نشده باشد
    if (!$expiry || empty($expiry)) {
        return false;
    }
    
    $today = date('Y-m-d');
    
    // مقایسه تاریخ انقضا با امروز
    if (strtotime($expiry) >= strtotime($today)) {
        return true;
    }
    
    return false;
}

add_action('init', function() {
    register_post_type('ds_payment', [
        'labels' => ['name' => 'تراکنش‌ها', 'singular_name' => 'تراکنش'],
        'public' => false,
        'show_ui' => true,
        'supports' => ['title', 'author', 'custom-fields'],
    ]);
});
