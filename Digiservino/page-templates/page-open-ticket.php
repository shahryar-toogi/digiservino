<?php
/* Template Name: Open Ticket */
if (!is_user_logged_in()) { wp_redirect(home_url('/my-account')); exit; }

$user_id = get_current_user_id();
// چک کردن اشتراک - در این صفحه اگر کاربر تاریخ انقضا نداشته باشد، false می‌گیرد
$has_subscription = ds_user_has_active_subscription($user_id);

$success_msg = false;

if ($has_subscription && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ds_submit_ticket'])) {
    $ticket_id = wp_insert_post([
        'post_title'   => sanitize_text_field($_POST['subject']),
        'post_content' => sanitize_textarea_field($_POST['description']),
        'post_type'    => 'ds_ticket',
        'post_status'  => 'publish',
        'post_author'  => $user_id,
    ]);

    if ($ticket_id) {
        update_post_meta($ticket_id, '_remote_type', sanitize_text_field($_POST['remote_type']));
        update_post_meta($ticket_id, '_remote_id', sanitize_text_field($_POST['remote_id']));
        update_post_meta($ticket_id, '_ticket_status', 'open');
        $success_msg = true;
    }
}
get_header(); ?>

<div class="min-h-screen bg-gray-50 flex flex-col md:flex-row" dir="rtl" x-data="{ step: 1, success_msg: false }">
    <?php include(get_template_directory() . '/parts/dashboard-sidebar.php'); ?>

    <main class="flex-1 p-6 md:p-12">
        <div class="max-w-3xl mx-auto">
            
            <?php if (!$has_subscription) : ?>
                <div class="bg-white rounded-3xl p-12 shadow-sm border border-gray-100 text-center animate-fade-in">
                    <div class="w-24 h-24 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-8 text-4xl shadow-inner">💎</div>
                    <h2 class="text-3xl font-black mb-4 text-gray-800">اشتراک شما فعال نیست</h2>
                    <p class="text-gray-500 mb-10 leading-relaxed text-lg">ثبت تیکت پشتیبانی ریموت (از راه دور) نیازمند داشتن یکی از طرح‌های اشتراک فعال است.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="<?php echo home_url('/subscriptions'); ?>" class="bg-indigo-600 text-white px-12 py-4 rounded-2xl font-bold shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all transform hover:-translate-y-1">خرید اشتراک</a>
                        <a href="<?php echo home_url('/service-request'); ?>" class="bg-gray-100 text-gray-600 px-12 py-4 rounded-2xl font-bold hover:bg-gray-200">درخواست سرویس حضوری (بدون اشتراک)</a>
                    </div>
                </div>
            <?php else : ?>

                <?php if($success_msg): ?>
                    <div class="bg-green-500 text-white p-6 rounded-3xl shadow-lg mb-8 text-center animate-bounce">
                        <h2 class="text-xl font-bold">تیکت با موفقیت ثبت شد!</h2>
                        <p>کارشناسان ما به زودی با شما در تماس خواهند بود.</p>
                    </div>
                <?php endif; ?>

                <div class="flex justify-between mb-8 px-4" x-show="!success_msg">
                    <template x-for="i in [1, 2, 3]">
                        <div class="flex flex-col items-center">
                            <div :class="step >= i ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-200 text-gray-400'" 
                                 class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-500">
                                <span x-text="i"></span>
                            </div>
                            <span class="text-[10px] mt-2 font-bold" :class="step >= i ? 'text-indigo-600' : 'text-gray-400'"
                                  x-text="i==1 ? 'جزئیات' : (i==2 ? 'دسترسی' : 'تایید')"></span>
                        </div>
                    </template>
                </div>

                <form method="POST" x-show="step > 0" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4">
                        <h2 class="text-2xl font-black mb-6">گام اول: شرح مشکل</h2>
                        <div class="space-y-6">
                            <input type="text" name="subject" required placeholder="موضوع درخواست" class="w-full bg-gray-50 border-0 rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500">
                            <textarea name="description" rows="5" required placeholder="توضیحات کامل..." class="w-full bg-gray-50 border-0 rounded-2xl p-4 focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                        <button type="button" @click="step = 2" class="mt-8 w-full bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100">مرحله بعد</button>
                    </div>

                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-cloak>
                        <h2 class="text-2xl font-black mb-6 text-gray-800">گام دوم: اطلاعات ریموت</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-8">
                            <?php $apps = ['AnyDesk', 'RustDesk', 'TeamViewer', 'Chrome Remote', 'Windows RDP', 'Other'];
                            foreach($apps as $app): ?>
                                <label class="cursor-pointer">
                                    <input type="radio" name="remote_type" value="<?php echo $app; ?>" class="peer sr-only" <?php echo $app == 'AnyDesk' ? 'checked' : ''; ?>>
                                    <div class="p-3 border-2 border-gray-50 rounded-2xl text-center peer-checked:border-indigo-600 peer-checked:bg-indigo-50 font-bold text-sm transition-all">
                                        <?php echo $app; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <input type="text" name="remote_id" placeholder="کد اتصال یا ID ریموت" class="w-full bg-gray-50 border-0 rounded-2xl p-4 text-left font-mono" dir="ltr">
                        <div class="flex gap-4 mt-8">
                            <button type="button" @click="step = 1" class="w-1/3 bg-gray-100 py-4 rounded-2xl font-bold">قبلی</button>
                            <button type="button" @click="step = 3" class="w-2/3 bg-indigo-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100">ادامه</button>
                        </div>
                    </div>

                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" class="text-center" x-cloak>
                        <div class="text-5xl mb-6">🚀</div>
                        <h2 class="text-2xl font-black mb-4">ارسال درخواست پشتیبانی</h2>
                        <p class="text-gray-500 mb-8">با کلیک بر روی دکمه زیر، تیکت شما ثبت شده و در لیست انتظار تکنسین‌ها قرار می‌گیرد.</p>
                        <div class="flex gap-4">
                            <button type="button" @click="step = 2" class="w-1/3 bg-gray-100 py-4 rounded-2xl font-bold">ویرایش</button>
                            <button type="submit" name="ds_submit_ticket" class="w-2/3 bg-green-600 text-white py-4 rounded-2xl font-bold shadow-lg shadow-green-100">ثبت و ارسال نهایی</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </main>
</div>

<?php get_footer(); ?>