<?php
/* Template Name: Tech Dashboard */

$user = wp_get_current_user();
// اجازه ورود فقط به مدیر و تکنسین
if (!is_user_logged_in() || !array_intersect(['administrator', 'ds_technician'], (array) $user->roles)) {
    wp_redirect(home_url('/my-account'));
    exit;
}

// کوئری برای گرفتن کاربران (فقط کلاینت‌ها)
$users = get_users(['role__in' => ['ds_client', 'subscriber']]);
$all_tickets = get_posts([
    'post_type' => 'ds_ticket',
    'posts_per_page' => -1,
    'post_status' => ['publish', 'processing', 'pending'] // وضعیت‌های مدنظر
]);

// آماده‌سازی دسته‌بندی‌ها (بخش B)
$categories = [
    'cctv' => ['title' => 'دوربین مداربسته', 'icon' => '📹', 'count' => 0],
    'network' => ['title' => 'شبکه و سرور', 'icon' => '🌐', 'count' => 0],
    'voip' => ['title' => 'ویپ (VOIP)', 'icon' => '📞', 'count' => 0],
    'hardware' => ['title' => 'سخت‌افزار', 'icon' => '💻', 'count' => 0],
    'software' => ['title' => 'نرم‌افزار', 'icon' => '⚙️', 'count' => 0],
    'ups' => ['title' => 'برق اضطراری', 'icon' => '⚡', 'count' => 0],
];

// بخش اصلاح شده شمارش در tech-dashboard.php
foreach($all_tickets as $t) {
    $service_type = get_post_meta($t->ID, '_service_type', true);
    foreach($categories as $key => $cat) {
        // بررسی تطابق نام فارسی خدمت با دیتابیس
        if(trim($service_type) === trim($cat['title'])) {
            $categories[$key]['count']++;
        }
    }
}

get_header(); ?>

<div class="min-h-screen bg-gray-900 text-white" dir="rtl" x-data="{ tab: 'users', filterStatus: 'all' }">
    
    <header class="bg-gray-800 border-b border-gray-700 p-4 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center font-bold">T</div>
                <div>
                    <h2 class="font-bold text-sm"><?php echo wp_get_current_user()->display_name; ?></h2>
                    <p class="text-[10px] text-gray-400">پنل مدیریت تکنسین</p>
                </div>
            </div>
            
            <nav class="hidden md:flex bg-gray-900 rounded-xl p-1 gap-1">
                <button @click="tab = 'users'" :class="tab === 'users' ? 'bg-indigo-600' : ''" class="px-4 py-2 rounded-lg text-xs font-bold transition">مانیتورینگ کاربران</button>
                <button @click="tab = 'cats'" :class="tab === 'cats' ? 'bg-indigo-600' : ''" class="px-4 py-2 rounded-lg text-xs font-bold transition">دسته‌بندی‌ها</button>
                <button @click="tab = 'list'" :class="tab === 'list' ? 'bg-indigo-600' : ''" class="px-4 py-2 rounded-lg text-xs font-bold transition">کل تیکت‌ها</button>
            </nav>

            <div class="flex items-center gap-4">
                <a href="<?php echo home_url('/my-account'); ?>" class="text-xs hover:text-indigo-400">پروفایل</a>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="text-xs text-red-400 bg-red-400/10 px-3 py-1 rounded-md">خروج</a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6">
        
        <div x-show="tab === 'users'" x-transition class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach($users as $u): 
                $pending_tickets = get_posts([
                    'post_type' => 'ds_ticket',
                    'author' => $u->ID,
                    'post_status' => 'publish', // فرض بر اینکه publish یعنی تیکت باز
                    'posts_per_page' => 1
                ]);
                $has_ticket = !empty($pending_tickets);
            ?>
                <div class="relative group">
                    <a href="<?php echo $has_ticket ? get_edit_post_link($pending_tickets[0]->ID) : '#'; ?>" 
                       class="block p-6 rounded-2xl border-2 transition-all <?php echo $has_ticket ? 'bg-red-500/10 border-red-500 animate-pulse' : 'bg-green-500/10 border-green-500 opacity-60'; ?> text-center">
                        <div class="text-3xl mb-2"><?php echo $has_ticket ? '🔔' : '👤'; ?></div>
                        <div class="font-bold text-xs truncate"><?php echo $u->display_name; ?></div>
                        <?php if($has_ticket): ?>
                            <div class="absolute inset-0 bg-red-600 opacity-0 group-hover:opacity-10 transition-opacity rounded-2xl"></div>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div x-show="tab === 'cats'" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach($categories as $key => $cat): ?>
                <div class="bg-gray-800 p-8 rounded-3xl border border-gray-700 hover:border-indigo-500 transition-all group">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform"><?php echo $cat['icon']; ?></div>
                    <h3 class="text-xl font-black"><?php echo $cat['title']; ?></h3>
                    <p class="text-indigo-400 mt-2 font-bold"><?php echo $cat['count']; ?> تیکت در این بخش</p>
                </div>
            <?php endforeach; ?>
        </div>

        <div x-show="tab === 'list'" x-transition class="bg-gray-800 rounded-3xl border border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-700 flex justify-between items-center bg-gray-800/50">
                <h3 class="font-black text-lg">فیلتر پیشرفته تیکت‌ها</h3>
                <select x-model="filterStatus" class="bg-gray-900 border-gray-600 rounded-xl px-4 py-2 text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="all">همه تیکت‌ها</option>
                    <option value="publish">باز (Open)</option>
                    <option value="processing">در حال انجام</option>
                    <option value="solved">حل شده</option>
                    <option value="closed">بسته شده</option>
                </select>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-900/50 text-gray-400 uppercase text-[10px]">
                        <tr>
                            <th class="p-4">مشتری</th>
                            <th class="p-4">موضوع</th>
                            <th class="p-4">وضعیت</th>
                            <th class="p-4">ریموت ID</th>
                            <th class="p-4">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($all_tickets as $ticket): 
                            $status = $ticket->post_status;
                            $remote_id = get_post_meta($ticket->ID, '_remote_id', true);
                            $author = get_userdata($ticket->post_author);
                        ?>
                            <tr x-show="filterStatus === 'all' || filterStatus === '<?php echo $status; ?>'" 
                                class="border-b border-gray-700 hover:bg-gray-700/30 transition-colors">
                                <td class="p-4">
                                    <div class="font-bold"><?php echo $author->display_name; ?></div>
                                    <div class="text-[10px] text-gray-500"><?php echo $author->user_email; ?></div>
                                </td>
                                <td class="p-4"><?php echo $ticket->post_title; ?></td>
                                <td class="p-4">
                                    <span class="px-2 py-1 rounded-md text-[9px] font-bold bg-gray-900 border border-gray-600 uppercase">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td class="p-4 font-mono text-indigo-400"><?php echo $remote_id ?: '---'; ?></td>
                                <td class="p-4">
                                    <a href="<?php echo get_edit_post_link($ticket->ID); ?>" class="text-indigo-500 hover:underline font-bold">مشاهده جزئیات</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <?php 
    $any_red = false;
    foreach($users as $u) {
        if(!empty(get_posts(['post_type' => 'ds_ticket', 'author' => $u->ID, 'post_status' => 'publish', 'posts_per_page' => 1]))) {
            $any_red = true; break;
        }
    }
    if($any_red): ?>
    <div class="fixed bottom-6 left-6 z-50 animate-bounce shadow-2xl">
        <div class="bg-red-600 text-white p-5 rounded-2xl flex items-center gap-4">
            <span class="text-2xl">🚨</span>
            <div>
                <p class="font-black text-sm">تیکت جدید شناسایی شد!</p>
                <p class="text-[10px]">باکس‌های قرمز رنگ را چک کنید.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>