<?php
/* Template Name: Admin SaaS Dashboard */
if (!current_user_can('administrator')) { wp_die('دسترسی غیرمجاز'); }
get_header(); ?>

<div class="min-h-screen bg-gray-50 p-8" dir="rtl">
    <div class="max-w-7xl mx-auto">
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-black text-gray-900">گزارش کل سیستم</h1>
                <p class="text-gray-500 mt-2">مشاهده وضعیت تیکت‌ها، درآمد و عملکرد تکنسین‌ها</p>
            </div>
            <div class="flex gap-4">
                <button class="bg-white border p-3 rounded-xl font-bold text-sm shadow-sm hover:bg-gray-50">خروجی اکسل</button>
                <button class="bg-indigo-600 text-white p-3 px-6 rounded-xl font-bold text-sm shadow-lg shadow-indigo-100">تنظیمات سیستم</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <span class="text-gray-400 text-xs font-bold block mb-2">کل درآمد ماه</span>
                <span class="text-2xl font-black text-green-600">۸۴,۵۰۰,۰۰۰ <small class="text-xs">تومان</small></span>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <span class="text-gray-400 text-xs font-bold block mb-2">تیکت‌های در انتظار</span>
                <span class="text-2xl font-black text-indigo-600">۱۴ درخواست</span>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <span class="text-gray-400 text-xs font-bold block mb-2">تکنسین‌های فعال</span>
                <span class="text-2xl font-black text-blue-600">۸ نفر</span>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <span class="text-gray-400 text-xs font-bold block mb-2">اشتراک‌های منقضی</span>
                <span class="text-2xl font-black text-red-500">۳ مورد</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold mb-6">نمودار فروش و خدمات</h3>
                <canvas id="adminRevenueChart" height="200"></canvas>
            </div>
            
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold mb-6">آخرین تیکت‌های دریافتی</h3>
                <div class="space-y-4">
                    <template x-for="i in 5">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                            <div>
                                <div class="font-bold text-sm text-gray-800">مشکل در شبکه شرکت آریا</div>
                                <div class="text-[10px] text-gray-400 mt-1">توسط: حمید علوی • ۲ ساعت پیش</div>
                            </div>
                            <span class="bg-indigo-100 text-indigo-600 text-[10px] font-black px-3 py-1 rounded-full uppercase">فوری</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('adminRevenueChart'), {
        type: 'line',
        data: {
            labels: ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه'],
            datasets: [{
                label: 'درآمد روزانه',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: '#6366f1',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.05)'
            }]
        }
    });
});
</script>

<?php get_footer(); ?>