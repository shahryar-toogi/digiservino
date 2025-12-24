<aside class="w-full md:w-64 bg-white border-l border-gray-200 p-6 flex-shrink-0">
    <a href="<?php echo home_url('/profile'); ?>">
        <div class="mb-10 text-center">
            <div class="w-20 h-20 bg-indigo-100 rounded-full mx-auto mb-4 flex items-center justify-center text-indigo-600 font-bold text-2xl">
                <?php echo mb_substr($current_user->display_name, 0, 1); ?>
            </div>
            <h2 class="font-bold text-gray-800"><?php echo $current_user->display_name; ?></h2>
            <p class="text-xs text-gray-400">شناسه کاربر: #<?php echo $current_user->ID; ?></p>
        </div></a>

        <nav class="space-y-2">
            <a href="<?php echo home_url('/client-dashboard'); ?>" class="flex items-center gap-3 p-3 bg-indigo-50 text-indigo-700 rounded-xl font-bold">
                <span>🏠</span> میز کار من
            </a>

            <div class="pt-4 pb-2 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">خدمات و پشتیبانی</div>
            <a href="<?php echo home_url('/open-ticket'); ?>" class="flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                <span>🎫</span> تیکت‌های پشتیبانی
            </a>
            <a href="<?php echo home_url('/service-request'); ?>" 
            class="flex items-center gap-3 p-3 text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition <?php echo is_page('service-request') ? 'bg-indigo-50 text-indigo-700 font-bold' : ''; ?>">
                <span>🛠️</span> درخواست خدمات در محل
            </a>

            <div class="pt-4 pb-2 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">مالی و حساب</div>
            <a href="<?php echo home_url('/subscriptions'); ?>" class="flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                <span>💎</span> مدیریت اشتراک
            </a>
            <a href="<?php echo home_url('/sub-history'); ?>" class="flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                <span>📜</span> سوابق پرداخت
            </a>
            <a href="<?php echo home_url('/req-history'); ?>" class="flex items-center gap-3 p-3 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                <span>📜</span> سوابق درخواست‌ها
            </a>
            <a href="<?php echo home_url('/profile'); ?>" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50">👤 پروفایل من</a>
            <div class="border-t my-4"></div>
            <a href="<?php echo wp_logout_url(home_url()); ?>" class="flex items-center gap-3 p-3 text-red-500 hover:bg-red-50 rounded-xl transition">
                <span>🚪</span> خروج از حساب
            </a>
        </nav>
    </aside>