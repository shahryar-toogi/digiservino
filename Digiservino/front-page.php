<?php get_header(); ?>

<div class="bg-white text-right" dir="rtl">
    <section class="relative bg-gray-50 overflow-hidden h-[500px] flex items-center">
        <div class="container mx-auto px-6 grid md:grid-cols-2 gap-8 items-center">
            <div class="z-10">
                <h2 class="text-5xl font-extrabold text-gray-900 leading-tight mb-6">راهکارهای هوشمند برای <br><span class="text-indigo-600">دنیای دیجیتال</span> شما</h2>
                <p class="text-gray-600 text-lg mb-8">مرکز تخصصی تعمیرات، شبکه و امنیت با پشتیبانی لحظه‌ای و ریموت.</p>
                <div class="flex space-x-reverse space-x-4">
                    <a href="<?php echo is_user_logged_in() ? home_url('/open-ticket') : home_url('/my-account'); ?>" class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold shadow-lg shadow-indigo-200">ثبت درخواست آنی</a>
                </div>
            </div>
            <div class="hidden md:block relative">
                <img src="https://img.freepik.com/free-vector/it-specialists-working-repairing-computer-parts_335657-3154.jpg" class="w-full opacity-80" alt="IT Support">
            </div>
        </div>
    </section>

    <section id="services" class="py-20 container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-4">خدمات تک موردی (پرداخت به ازای هر مورد)</h2>
            <p class="text-gray-500">سرویس‌های فوری بدون نیاز به اشتراک ماهیانه</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php
            $services = [
                ['title' => 'تعمیرات لپ‌تاپ', 'price' => '۴۹۰,۰۰۰ تومان', 'icon' => '💻'],
                ['title' => 'عیب‌یابی شبکه', 'price' => '۶۵۰,۰۰۰ تومان', 'icon' => '📡'],
                ['title' => 'نصب دوربین مداربسته', 'price' => '۸۹۰,۰۰۰ تومان', 'icon' => '📹'],
                ['title' => 'پشتیبانی ریموت', 'price' => '۲۵۰,۰۰۰ تومان', 'icon' => '🖱️'],
            ];
            foreach ($services as $s): ?>
            <div class="border border-gray-100 p-8 rounded-3xl text-center hover:shadow-2xl transition group bg-white">
                <div class="text-4xl mb-4"><?php echo $s['icon']; ?></div>
                <h3 class="font-bold text-xl mb-2"><?php echo $s['title']; ?></h3>
                <p class="text-indigo-600 font-bold mb-4"><?php echo $s['price']; ?></p>
                <button class="w-full py-2 bg-gray-50 text-gray-700 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition font-bold">سفارش آنی</button>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="pricing" class="py-24 bg-indigo-900 text-white overflow-hidden relative">
        <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-800 rounded-full blur-3xl opacity-50"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black mb-4">اشتراک‌های ویژه پشتیبانی</h2>
                <p class="text-indigo-200">با خرید اشتراک، هزینه‌های IT خود را تا ۶۰٪ کاهش دهید</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-indigo-800/50 backdrop-blur-md border border-indigo-700 p-8 rounded-3xl hover:bg-indigo-800 transition">
                    <h3 class="text-xl font-bold mb-2">طرح نقره‌ای (خانگی)</h3>
                    <div class="text-3xl font-black mb-6 text-white">۲۹۰,۰۰۰ <span class="text-sm font-normal opacity-60">تومان / ماهانه</span></div>
                    <ul class="space-y-4 mb-10 text-indigo-100 text-sm">
                        <li>✅ ۲ عدد تیکت اولویت‌دار</li>
                        <li>✅ پشتیبانی ریموت (AnyDesk)</li>
                        <li>✅ عیب‌یابی نرم‌افزاری رایگان</li>
                        <li>❌ بازدید حضوری رایگان</li>
                    </ul>
                    <button class="w-full py-3 bg-white text-indigo-900 rounded-xl font-bold hover:bg-indigo-50 transition">انتخاب طرح</button>
                </div>
                <div class="bg-white text-indigo-900 p-8 rounded-3xl transform scale-105 shadow-2xl relative">
                    <div class="absolute -top-4 right-8 bg-yellow-400 text-indigo-900 text-xs font-black px-3 py-1 rounded-full shadow-md">پیشنهاد ویژه</div>
                    <h3 class="text-xl font-bold mb-2">طرح طلایی (تجاری)</h3>
                    <div class="text-3xl font-black mb-6">۹۸۰,۰۰۰ <span class="text-sm font-normal text-gray-500">تومان / ماهانه</span></div>
                    <ul class="space-y-4 mb-10 text-gray-600 text-sm">
                        <li>✅ تیکت نامحدود</li>
                        <li>✅ پشتیبانی ۲۴/۷ ریموت</li>
                        <li>✅ ۱ مورد بازدید حضوری رایگان</li>
                        <li>✅ مانیتورینگ شبکه و امنیت</li>
                    </ul>
                    <button class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">خرید اشتراک طلایی</button>
                </div>
                <div class="bg-indigo-800/50 backdrop-blur-md border border-indigo-700 p-8 rounded-3xl hover:bg-indigo-800 transition">
                    <h3 class="text-xl font-bold mb-2">طرح سازمانی</h3>
                    <div class="text-3xl font-black mb-6">تماس بگیرید</div>
                    <ul class="space-y-4 mb-10 text-indigo-100 text-sm">
                        <li>✅ تکنسین اختصاصی</li>
                        <li>✅ نگهداری کامل سرور و CCTV</li>
                        <li>✅ زمان پاسخگویی زیر ۱ ساعت</li>
                        <li>✅ گزارشات دوره‌ای تحلیلی</li>
                    </ul>
                    <button class="w-full py-3 border-2 border-white text-white rounded-xl font-bold hover:bg-white hover:text-indigo-900 transition">مشاوره سازمانی</button>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-6 grid md:grid-cols-3 gap-12 text-right">
            <div>
                <h4 class="text-xl font-bold mb-6 text-indigo-400">دیجی‌سروینو</h4>
                <p class="text-gray-400 leading-loose text-sm">مرکز تخصصی خدمات IT و پشتیبانی شبکه. راهکارهای ما برای کسب و کار شما طراحی شده است.</p>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6 text-indigo-400">ارتباط سریع</h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li>📍 دفتر مرکزی: تهران، ولیعصر</li>
                    <li>📞 پشتیبانی: ۰۲۱-۱۲۳۴۵۶۷۸</li>
                    <li>📧 ایمیل: support@digiservino.ir</li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6 text-indigo-400">ساعات پاسخگویی</h4>
                <p class="text-gray-400 text-sm">شنبه تا چهارشنبه: ۹:۰۰ الی ۱۸:۰۰</p>
                <p class="text-gray-400 text-sm">پنجشنبه‌ها: ۹:۰۰ الی ۱۳:۰۰</p>
                <div class="mt-6 flex gap-4">
                    <div class="w-8 h-8 bg-gray-800 rounded-full"></div>
                    <div class="w-8 h-8 bg-gray-800 rounded-full"></div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-xs">
            <p>تمامی حقوق این وب‌سایت متعلق به دیجی‌سروینو است. طراحی و توسعه اختصاصی ۱۴۰۴ ©</p>
        </div>
    </footer>
</div>

<?php get_footer(); ?>