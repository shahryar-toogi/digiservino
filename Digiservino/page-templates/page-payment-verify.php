<?php
/* Template Name: Payment Verify */
get_header();

$merchant_id = 'daaf9469-6f2e-4861-8381-e42934c2e809'; // مرچنت آیدی خود را اینجا قرار دهید
$authority = isset($_GET['Authority']) ? $_GET['Authority'] : '';
$status = isset($_GET['Status']) ? $_GET['Status'] : '';

// جستجوی پست (تیکت یا پرداخت) که منتظر این Authority است
// ما هر دو نوع پست (ds_ticket و ds_payment) را جستجو می‌کنیم
$query = new WP_Query([
    'post_type' => ['ds_ticket', 'ds_payment'],
    'meta_query' => [
        ['key' => '_pending_authority', 'value' => $authority]
    ],
    'posts_per_page' => 1
]);

?>

<div class="min-h-screen bg-gray-50 py-20 px-4" dir="rtl">
    <div class="max-w-xl mx-auto bg-white rounded-3xl p-10 shadow-sm border border-gray-100 text-center">
        
        <?php
        if ($status == 'OK' && $query->have_posts()) {
            $order_post = $query->posts[0];
            $order_id = $order_post->ID;
            $user_id = $order_post->post_author;
            
            // دریافت مبلغ ذخیره شده در مرحله قبل (تبدیل به ریال برای زرین‌پال)
            $amount_toman = get_post_meta($order_id, '_pending_amount', true) ?: 50000;
            $amount_rial = $amount_toman * 10;

            // استعلام نهایی از زرین‌پال (Verify)
            $data = array("merchant_id" => $merchant_id, "authority" => $authority, "amount" => $amount_rial);
            $jsonData = json_encode($data);
            $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
            curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($jsonData)));
            $result = curl_exec($ch);
            $result = json_decode($result, true);
            curl_close($ch);

            if (isset($result['data']['code']) && $result['data']['code'] == 100) {
                $ref_id = $result['data']['ref_id'];
                
                // ۱. آپدیت وضعیت پست به "انتشار"
                wp_update_post([
                    'ID' => $order_id,
                    'post_status' => 'publish'
                ]);

                // ۲. ذخیره کد پیگیری در دیتابیس
                update_post_meta($order_id, '_ref_id', $ref_id);
                delete_post_meta($order_id, '_pending_authority'); // پاکسازی متای موقت

                // ۳. تشخیص نوع تراکنش (اشتراک یا خدمات)
                $is_sub = get_post_meta($order_id, '_is_subscription', true);
                
                if ($is_sub == '1') {
                    // منطق فعال‌سازی اشتراک کاربر
                    $plan_name = get_post_meta($order_id, '_plan_type', true);
                    $expiry_date = date('Y-m-d', strtotime('+30 days'));
                    
                    update_user_meta($user_id, 'ds_plan', $plan_name);
                    update_user_meta($user_id, 'ds_expiry', $expiry_date);
                    
                    $success_title = "اشتراک شما فعال شد";
                    $success_desc = "طرح $plan_name با موفقیت برای حساب شما فعال شد و تا تاریخ $expiry_date معتبر است.";
                    $btn_link = home_url('/subscriptions');
                    $btn_text = "مدیریت اشتراک";
                } else {
                    // منطق تایید درخواست خدمت
                    $success_title = "درخواست خدمت ثبت شد";
                    $success_desc = "تراکنش موفقیت‌آمیز بود. درخواست شما برای کارشناسان ارسال شد و به زودی با شما تماس می‌گیرند.";
                    $btn_link = home_url('/service-history');
                    $btn_text = "سوابق درخواست‌ها";
                }
                ?>
                
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">✓</div>
                <h2 class="text-2xl font-black mb-4 text-gray-800"><?php echo $success_title; ?></h2>
                <p class="text-gray-500 mb-2">شماره پیگیری: <?php echo $ref_id; ?></p>
                <p class="text-gray-400 text-sm mb-8 leading-relaxed"><?php echo $success_desc; ?></p>
                <a href="<?php echo $btn_link; ?>" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-100"><?php echo $btn_text; ?></a>
                
                <?php
            } else {
                // خطای بانکی در Verify
                echo '<div class="text-red-500 font-bold text-xl">خطا در تایید تراکنش. اگر مبلغی کسر شده، به حساب شما باز خواهد گشت.</div>';
            }
        } else {
            // تراکنش لغو شده یا Authority یافت نشد
            ?>
            <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">✕</div>
            <h2 class="text-2xl font-black mb-4 text-gray-800">تراکنش لغو شد یا یافت نشد</h2>
            <p class="text-gray-500 mb-8 font-bold leading-relaxed">پرداخت با موفقیت انجام نشد. اگر مشکلی پیش آمده است، لطفاً با پشتیبانی تماس بگیرید.</p>
            <div class="flex gap-4 justify-center">
                <a href="<?php echo home_url('/client-dashboard'); ?>" class="bg-gray-100 text-gray-600 px-8 py-3 rounded-2xl font-bold">بازگشت به پنل</a>
                <a href="<?php echo home_url('/service-request'); ?>" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl font-bold">تلاش مجدد</a>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php get_footer(); ?>