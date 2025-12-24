<?php
/* Template Name: Service Request */
if (!is_user_logged_in()) { wp_redirect(home_url('/my-account')); exit; }

$success_msg = false;
$error_msg = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ds_submit_request'])) {
    
    $request_id = wp_insert_post([
        'post_title'   => "درخواست " . sanitize_text_field($_POST['service_type']),
        'post_type'    => 'ds_ticket',
        'post_status'  => 'pending',
        'post_author'  => get_current_user_id(),
    ]);

    if ($request_id) {
        update_post_meta($request_id, '_is_service_request', '1');
        update_post_meta($request_id, '_service_type', sanitize_text_field($_POST['service_type']));
        update_post_meta($request_id, '_visit_address', sanitize_textarea_field($_POST['address']));
        update_post_meta($request_id, '_visit_date', sanitize_text_field($_POST['visit_date']));
        
        $amount = 50000; 
        $callback = home_url('/payment-verify');
        
        if (function_exists('ds_request_payment')) {
            $payment_url = ds_request_payment($amount, "پرداخت بیعانه درخواست #" . $request_id, $callback);
            
            if ($payment_url) {
                // استخراج مطمئن Authority
                $parts = explode('/', rtrim($payment_url, '/'));
                $authority = end($parts);
                
                update_post_meta($request_id, '_pending_authority', $authority);
                update_post_meta($request_id, '_pending_amount', $amount);

                // ریدایرکت ترکیبی برای اطمینان
                if (!headers_sent()) {
                    wp_redirect($payment_url);
                    exit;
                } else {
                    echo '<script type="text/javascript">window.location.href="' . $payment_url . '";</script>';
                    exit;
                }
            } else {
                $error_msg = "خطا در اتصال به درگاه زرین‌پال. لطفاً مرچنت‌آیدی را در functions.php چک کنید.";
            }
        } else {
            $error_msg = "سیستم پرداخت در دسترس نیست (تابع تعریف نشده).";
        }
    }
}
get_header(); ?>

<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="rtl" x-data="{ step: 1, selectedService: '' }">
    <?php include(get_template_directory() . '/parts/dashboard-sidebar.php'); ?>

    <main class="flex-1 p-6 md:p-12">
        <div class="max-w-4xl mx-auto">
            
            <?php if($error_msg): ?>
                <div class="bg-red-100 text-red-600 p-6 rounded-3xl mb-8 border border-red-200 font-bold">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8">
                <div x-show="step === 1" x-transition>
                    <h2 class="text-3xl font-black mb-8 text-gray-800">کدام خدمت را نیاز دارید؟</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php 
                        $services = [
                            ['id' => 'cctv', 'title' => 'نصب دوربین مداربسته', 'icon' => '📹'],
                            ['id' => 'network', 'title' => 'خدمات شبکه و سرور', 'icon' => '🌐'],
                            ['id' => 'voip', 'title' => 'راه اندازی ویپ (VOIP)', 'icon' => '📞'],
                            ['id' => 'hardware', 'title' => 'تعمیرات سخت‌افزار', 'icon' => '💻'],
                            ['id' => 'software', 'title' => 'پشتیبانی نرم‌افزار', 'icon' => '⚙️'],
                            ['id' => 'ups', 'title' => 'برق اضطراری و UPS', 'icon' => '⚡']
                        ];
                        foreach($services as $s): ?>
                            <label class="cursor-pointer group">
                                <input type="radio" name="service_type" value="<?php echo $s['title']; ?>" 
                                       @click="selectedService = '<?php echo $s['title']; ?>'; step = 2" class="sr-only">
                                <div class="bg-white p-8 rounded-3xl border-2 border-transparent hover:border-indigo-600 hover:shadow-xl transition-all text-center">
                                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform"><?php echo $s['icon']; ?></div>
                                    <div class="font-bold text-gray-700"><?php echo $s['title']; ?></div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div x-show="step === 2" x-transition x-cloak class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <button type="button" @click="step = 1" class="text-indigo-600 font-bold mb-6 flex items-center gap-2 hover:underline">
                        <span>→</span> تغییر نوع خدمت (<span x-text="selectedService" class="text-gray-900"></span>)
                    </button>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block mb-3 font-bold text-gray-700">آدرس دقیق جهت اعزام تکنسین</label>
                            <textarea name="address" rows="4" required placeholder="خیابان، کوچه، پلاک..." 
                                      class="w-full bg-gray-50 border-0 rounded-2xl p-4 focus:ring-2 focus:ring-indigo-600"></textarea>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block mb-3 font-bold text-gray-700">تاریخ پیشنهادی بازدید</label>
                                <input type="date" name="visit_date" required class="w-full bg-gray-50 border-0 rounded-2xl p-4 focus:ring-2 focus:ring-indigo-600">
                            </div>
                            <button type="submit" name="ds_submit_request" class="w-full bg-indigo-600 text-white py-5 rounded-2xl font-bold text-lg shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
                                تایید و پرداخت کارشناسی (۵۰,۰۰۰ ت)
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
<?php get_footer(); ?>