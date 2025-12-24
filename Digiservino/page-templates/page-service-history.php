<?php
/* Template Name: Service Requests History */
if (!is_user_logged_in()) { wp_redirect(home_url('/my-account')); exit; }

$user_id = get_current_user_id();
// دریافت پست‌هایی که متای _is_service_request دارند
$requests = new WP_Query([
    'post_type' => 'ds_ticket',
    'author' => $user_id,
    'meta_query' => [
        [
            'key' => '_is_service_request',
            'value' => '1'
        ]
    ]
]);

get_header(); ?>

<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="rtl">
    <?php include(get_template_directory() . '/parts/dashboard-sidebar.php'); ?>

    <main class="flex-1 p-8">
        <h1 class="text-2xl font-black text-gray-800 mb-8">سوابق درخواست‌های خدمات</h1>

        <div class="grid grid-cols-1 gap-4">
            <?php if($requests->have_posts()): while($requests->have_posts()): $requests->the_post(); 
                $service_type = get_post_meta(get_the_ID(), '_service_type', true);
                $visit_date = get_post_meta(get_the_ID(), '_visit_date', true);
            ?>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-xl">🛠️</div>
                        <div>
                            <h3 class="font-bold text-gray-800"><?php echo $service_type; ?></h3>
                            <p class="text-xs text-gray-400">تاریخ ثبت: <?php echo get_the_date(); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end">
                        <div class="text-right">
                            <p class="text-xs text-gray-400 mb-1">تاریخ بازدید درخواستی:</p>
                            <p class="text-sm font-bold text-gray-700"><?php echo $visit_date ?: 'نامشخص'; ?></p>
                        </div>
                        <span class="bg-blue-100 text-blue-600 px-4 py-2 rounded-xl text-xs font-bold">در انتظار تایید</span>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); else: ?>
                <div class="bg-white p-20 rounded-3xl text-center border border-dashed border-gray-200">
                    <p class="text-gray-400">شما هنوز هیچ درخواست خدماتی ثبت نکرده‌اید.</p>
                    <a href="<?php echo home_url('/service-request'); ?>" class="text-indigo-600 font-bold mt-2 inline-block">ثبت اولین درخواست</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php get_footer(); ?>