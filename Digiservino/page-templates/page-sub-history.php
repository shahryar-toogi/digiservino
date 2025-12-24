<?php
/* Template Name: Subscription History */
if (!is_user_logged_in()) { wp_redirect(home_url('/my-account')); exit; }

$user_id = get_current_user_id();
$payments = new WP_Query([
    'post_type' => 'ds_payment', // فرض بر وجود CPT پرداخت
    'author' => $user_id,
    'posts_per_page' => 10
]);

get_header(); ?>

<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="rtl">
    <?php include(get_template_directory() . '/parts/dashboard-sidebar.php'); ?>

    <main class="flex-1 p-8">
        <h1 class="text-2xl font-black text-gray-800 mb-8">تاریخچه پرداخت‌ها و اشتراک‌ها</h1>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 text-gray-400 text-xs">
                    <tr>
                        <th class="p-4">شناسه</th>
                        <th class="p-4">شرح تراکنش</th>
                        <th class="p-4">مبلغ</th>
                        <th class="p-4">تاریخ</th>
                        <th class="p-4">وضعیت</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php if($payments->have_posts()): while($payments->have_posts()): $payments->the_post(); ?>
                        <tr class="border-b border-gray-50">
                            <td class="p-4">#<?php the_ID(); ?></td>
                            <td class="p-4 font-bold"><?php the_title(); ?></td>
                            <td class="p-4"><?php echo number_format(get_post_meta(get_the_ID(), '_amount', true)); ?> تومان</td>
                            <td class="p-4"><?php echo get_the_date(); ?></td>
                            <td class="p-4"><span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs">موفق</span></td>
                        </tr>
                    <?php endwhile; wp_reset_postdata(); else: ?>
                        <tr><td colspan="5" class="p-10 text-center text-gray-400">هنوز تراکنشی ثبت نشده است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php get_footer(); ?>