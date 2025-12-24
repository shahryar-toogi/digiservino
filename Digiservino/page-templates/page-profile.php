<?php
/* Template Name: User Profile */
if (!is_user_logged_in()) { wp_redirect(home_url('/my-account')); exit; }

$current_user = wp_get_current_user();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    if (wp_verify_nonce($_POST['ds_profile_nonce'], 'ds_update_user')) {
        $user_data = [
            'ID' => $current_user->ID,
            'display_name' => sanitize_text_field($_POST['display_name']),
            'user_email' => sanitize_email($_POST['user_email'])
        ];
        $updated = wp_update_user($user_data);
        if (is_wp_error($updated)) {
            $error = 'خطایی رخ داد: ' . $updated->get_error_message();
        } else {
            $success = 'اطلاعات با موفقیت بروزرسانی شد.';
        }
    }
}

get_header(); ?>

<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="rtl">
    <?php include(locate_template('parts/dashboard-sidebar.php')); ?>

    <main class="flex-1 p-8">
        <h1 class="text-2xl font-black text-gray-800 mb-8">تنظیمات پروفایل</h1>

        <div class="max-w-2xl bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <?php if($error): ?><div class="bg-red-50 text-red-500 p-4 rounded-xl mb-6"><?php echo $error; ?></div><?php endif; ?>
            <?php if($success): ?><div class="bg-green-50 text-green-500 p-4 rounded-xl mb-6"><?php echo $success; ?></div><?php endif; ?>

            <form method="post" class="space-y-6">
                <?php wp_nonce_field('ds_update_user', 'ds_profile_nonce'); ?>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نام نمایشی</label>
                    <input type="text" name="display_name" value="<?php echo $current_user->display_name; ?>" class="w-full bg-gray-50 border-0 rounded-xl p-4 focus:ring-2 focus:ring-indigo-600">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">ایمیل</label>
                    <input type="email" name="user_email" value="<?php echo $current_user->user_email; ?>" class="w-full bg-gray-50 border-0 rounded-xl p-4 focus:ring-2 focus:ring-indigo-600">
                </div>

                <button type="submit" name="update_profile" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold hover:bg-indigo-700 transition">ذخیره تغییرات</button>
            </form>
        </div>
    </main>
</div>

<?php get_footer(); ?>