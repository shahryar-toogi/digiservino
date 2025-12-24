<?php
/* Template Name: View Ticket */
if (!is_user_logged_in()) { wp_redirect(home_url('/my-account')); exit; }

$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ticket = get_post($ticket_id);

// بررسی دسترسی: فقط نویسنده تیکت یا ادمین
if (!$ticket || ($ticket->post_author != get_current_user_id() && !current_user_can('manage_options'))) {
    wp_die('شما دسترسی به این تیکت را ندارید.');
}

get_header(); ?>

<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="rtl">
    <?php include(get_template_directory() . '/parts/dashboard-sidebar.php'); ?>

    <main class="flex-1 p-6 md:p-12">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-black text-gray-800"><?php echo get_the_title($ticket_id); ?></h1>
                <span class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full text-xs font-bold">وضعیت: در حال بررسی</span>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-400 text-xs mb-1">نرم‌افزار ریموت:</p>
                    <p class="font-bold text-gray-700"><?php echo get_post_meta($ticket_id, '_remote_type', true) ?: 'ثبت نشده'; ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">شناسه اتصال:</p>
                    <p class="font-bold text-gray-700 font-mono" dir="ltr"><?php echo get_post_meta($ticket_id, '_remote_id', true) ?: '---'; ?></p>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-sm">👤</div>
                    <span class="font-bold text-gray-800">شرح درخواست شما:</span>
                </div>
                <div class="text-gray-600 leading-relaxed">
                    <?php echo apply_filters('the_content', $ticket->post_content); ?>
                </div>
            </div>

            <div class="space-y-6">
                <h3 class="font-bold text-lg text-gray-800">پاسخ‌های پشتیبانی</h3>
                <?php
                $comments = get_comments(['post_id' => $ticket_id, 'order' => 'ASC']);
                foreach($comments as $comment):
                    $is_admin = user_can($comment->user_id, 'manage_options');
                ?>
                    <div class="<?php echo $is_admin ? 'bg-indigo-50 border-indigo-100' : 'bg-white border-gray-100'; ?> p-6 rounded-3xl border shadow-sm">
                        <div class="flex justify-between mb-2">
                            <span class="font-bold text-sm <?php echo $is_admin ? 'text-indigo-700' : 'text-gray-700'; ?>">
                                <?php echo $is_admin ? '🛡️ کارشناس فنی' : '👤 شما'; ?>
                            </span>
                            <span class="text-xs text-gray-400"><?php echo get_comment_date('', $comment); ?></span>
                        </div>
                        <p class="text-gray-600"><?php echo $comment->comment_content; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>
<?php get_footer(); ?>