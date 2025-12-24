<?php
/* Template Name: Client Dashboard */
if (!is_user_logged_in()) { wp_redirect(home_url('/my-account')); exit; }
get_header();
$user_id = get_current_user_id();
$current_user = wp_get_current_user();

// Fetch Real Data
$subscription = ds_get_user_subscription($user_id);
$total_tickets = ds_get_user_ticket_count($user_id);
$recent_tickets = get_posts([
    'post_type' => 'ds_ticket',
    'author' => $user_id,
    'posts_per_page' => 5
]);
?>

<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="rtl">
    <?php include(get_template_directory() . '/parts/dashboard-sidebar.php'); ?>

    <main class="flex-1 p-8">
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-black text-gray-800">خوش آمدید، <?php echo $current_user->display_name; ?></h1>
            <a href="<?php echo home_url('/open-ticket'); ?>" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg">ثبت تیکت جدید</a>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-sm mb-1">وضعیت اشتراک</p>
                <p class="text-xl font-bold text-indigo-600"><?php echo $subscription; ?></p>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-sm mb-1">کل تیکت‌ها</p>
                <p class="text-xl font-bold text-gray-800"><?php echo $total_tickets; ?> مورد</p>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <p class="text-gray-400 text-sm mb-1">موجودی کیف پول</p>
                <p class="text-xl font-bold text-green-600">۰ تومان</p>
            </div>
        </div>

        <section class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50"><h3 class="font-bold">آخرین تیکت‌های شما</h3></div>
            <table class="w-full text-right">
                <thead class="bg-gray-50 text-gray-400 text-xs">
                    <tr><th class="p-4">عنوان</th><th class="p-4">تاریخ</th><th class="p-4">وضعیت</th></tr>
                </thead>
                <tbody>
                    <?php if($recent_tickets): foreach($recent_tickets as $ticket): ?>
                    <tr class="border-b border-gray-50">
                        <td class="p-4 font-medium"><?php echo get_the_title($ticket->ID); ?></td>
                        <td class="p-4"><?php echo get_the_date('', $ticket->ID); ?></td>
                        <td class="p-4">
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs">باز</span>
                        </td>
                        <td><a href="<?php echo home_url('/view-ticket/?id=' . $ticket->ID); ?>" class="text-indigo-600 hover:underline">
                        
                        مشاهده جزئیات
                        </a></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="3" class="p-10 text-center text-gray-400">هیچ تیکتی یافت نشد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
