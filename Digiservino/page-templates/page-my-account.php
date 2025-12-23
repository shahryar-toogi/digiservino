<?php
/* Template Name: Client Profile & Subscription */
if (!is_user_logged_in()) { 
    // If not logged in, show the login template instead
    get_template_part('page-login'); 
    exit; 
}
get_header(); 
$user = wp_get_current_user();
?>

<div class="min-h-screen bg-gray-50 pb-20" dir="rtl">
    <div class="bg-indigo-700 pt-32 pb-20 px-6">
        <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between text-white">
            <div class="flex items-center space-x-reverse space-x-6">
                <div class="w-24 h-24 bg-white/20 rounded-full border-4 border-white/30 flex items-center justify-center text-4xl">
                    <?php echo mb_substr($user->display_name, 0, 1); ?>
                </div>
                <div>
                    <h1 class="text-3xl font-black"><?php echo $user->display_name; ?> خوش آمدید</h1>
                    <p class="opacity-80">عضویت از: <?php echo date_i18n('j F Y', strtotime($user->user_registered)); ?></p>
                </div>
            </div>
            <div class="mt-6 md:mt-0">
                <a href="/open-ticket" class="bg-yellow-400 text-indigo-900 px-8 py-3 rounded-2xl font-black shadow-lg hover:bg-yellow-300 transition">ثبت درخواست جدید +</a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 -mt-10 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-400 mb-4">وضعیت اشتراک</h3>
            <div class="flex items-center justify-between mb-6">
                <span class="text-2xl font-black text-indigo-600">طرح طلایی</span>
                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-lg text-xs font-bold">فعال</span>
            </div>
            <div class="space-y-4 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>تیکت‌های باقی‌مانده:</span>
                    <span class="font-bold">نامحدود</span>
                </div>
                <div class="flex justify-between">
                    <span>تاریخ انقضا:</span>
                    <span class="font-bold">۱۴۰۴/۰۳/۱۵</span>
                </div>
            </div>
            <button class="w-full mt-8 py-3 border-2 border-indigo-600 text-indigo-600 rounded-xl font-bold hover:bg-indigo-50 transition">تمدید یا ارتقا</button>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-400 mb-4">آمار فعالیت</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-2xl text-center">
                    <span class="block text-2xl font-black text-blue-600">۱۲</span>
                    <span class="text-xs text-blue-400">کل تیکت‌ها</span>
                </div>
                <div class="bg-orange-50 p-4 rounded-2xl text-center">
                    <span class="block text-2xl font-black text-orange-600">۱</span>
                    <span class="text-xs text-orange-400">در جریان</span>
                </div>
            </div>
            <a href="/my-dashboard" class="block text-center mt-8 text-indigo-600 font-bold hover:underline">مشاهده تاریخچه کامل ←</a>
        </div>

        <div class="bg-indigo-900 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-lg font-bold opacity-60 mb-4">پشتیبانی مستقیم</h3>
                <p class="text-sm leading-loose mb-6">در صورت نیاز به مشاوره فوری با کارشناس اختصاصی خود تماس بگیرید.</p>
                <div class="text-xl font-black">۰۲۱-۹۱۰۰XXXX</div>
            </div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-tr-full"></div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 mt-12">
        <h3 class="text-xl font-black mb-6">فاکتورهای اخیر</h3>
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-6 text-gray-500 font-bold">شماره فاکتور</th>
                        <th class="p-6 text-gray-500 font-bold">تاریخ</th>
                        <th class="p-6 text-gray-500 font-bold">مبلغ</th>
                        <th class="p-6 text-gray-500 font-bold">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="p-6 font-bold">INV-8801</td>
                        <td class="p-6 text-gray-600">۱۴۰۲/۱۰/۱۲</td>
                        <td class="p-6 text-gray-600">۹۸۰,۰۰۰ تومان</td>
                        <td class="p-6">
                            <button class="text-indigo-600 font-bold hover:bg-indigo-50 px-4 py-2 rounded-lg transition">دانلود PDF</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php get_footer(); ?>