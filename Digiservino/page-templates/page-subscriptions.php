<?php
/* Template Name: User Subscriptions */
if (!is_user_logged_in()) { wp_redirect(home_url('/my-account')); exit; }

$user_id = get_current_user_id();
$error_msg = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_plan'])) {
    $plan_name = sanitize_text_field($_POST['plan_name']);
    $price = intval($_POST['plan_price']); 
    
    $order_id = wp_insert_post([
        'post_title' => "خرید اشتراک " . $plan_name . " - کاربر " . $user_id,
        'post_type' => 'ds_payment',
        'post_status' => 'pending',
        'author'      => $user_id
    ]);

    if ($order_id) {
        update_post_meta($order_id, '_plan_type', $plan_name);
        update_post_meta($order_id, '_is_subscription', '1');
        update_post_meta($order_id, '_pending_amount', $price);
        
        $callback = home_url('/payment-verify');
        
        if (function_exists('ds_request_payment')) {
            $payment_url = ds_request_payment($price, "خرید اشتراک " . $plan_name, $callback);
            
            if ($payment_url) {
                $parts = explode('/', rtrim($payment_url, '/'));
                $authority = end($parts);
                update_post_meta($order_id, '_pending_authority', $authority);
                
                // ریدایرکت قطعی
                echo '<script type="text/javascript">window.location.href="' . $payment_url . '";</script>';
                exit;
            } else {
                $error_msg = "خطا در برقراری ارتباط با درگاه پرداخت.";
            }
        }
    }
}

get_header(); ?>

<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="rtl">
    <?php include(get_template_directory() . '/parts/dashboard-sidebar.php'); ?>

    <main class="flex-1 p-6 md:p-12">
        <h1 class="text-3xl font-black text-gray-800 mb-8 text-center md:text-right">خرید و مدیریت اشتراک</h1>

        <?php if($error_msg): ?>
            <div class="bg-red-100 text-red-600 p-4 rounded-2xl mb-6 font-bold text-center">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            $plans = [
                ['id' => 'bronze', 'name' => 'برنزی', 'price' => 490000, 'label' => '۴۹۰,۰۰۰', 'features' => 'پشتیبانی تیکتی'],
                ['id' => 'silver', 'name' => 'نقره‌ای', 'price' => 980000, 'label' => '۹۸۰,۰۰۰', 'features' => 'پشتیبانی ریموت + تیکت'],
                ['id' => 'gold', 'name' => 'طلایی', 'price' => 1900000, 'label' => '۱,۹۰۰,۰۰۰', 'features' => 'پشتیبانی VIP + اولویت']
            ];
            foreach($plans as $plan): ?>
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm text-center flex flex-col justify-between hover:shadow-lg transition group">
                    <div>
                        <h4 class="font-black text-xl mb-4 text-gray-800 group-hover:text-indigo-600 transition"><?php echo $plan['name']; ?></h4>
                        <div class="mb-2">
                            <span class="text-3xl font-black text-indigo-600"><?php echo $plan['label']; ?></span>
                            <span class="text-xs text-gray-400 font-bold block mt-1">تومان / ماهانه</span>
                        </div>
                        <div class="h-px bg-gray-100 my-6"></div>
                        <ul class="text-sm text-gray-500 mb-8 space-y-3">
                            <li class="flex items-center justify-center gap-2">
                                <span class="text-green-500">✓</span> <?php echo $plan['features']; ?>
                            </li>
                            <li class="flex items-center justify-center gap-2 italic text-xs text-gray-400">
                                فعال‌سازی آنی پس از پرداخت
                            </li>
                        </ul>
                    </div>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="plan_name" value="<?php echo $plan['name']; ?>">
                        <input type="hidden" name="plan_price" value="<?php echo $plan['price']; ?>">
                        <button type="submit" name="buy_plan" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-bold shadow-md hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-300">
                            خرید و فعال‌سازی
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<?php get_footer(); ?>