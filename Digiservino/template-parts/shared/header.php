<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="bg-white border-b sticky top-0 z-50 px-6 py-4" dir="rtl">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-10">
            <a href="<?php echo home_url(); ?>" class="text-2xl font-black text-indigo-600 tracking-tighter">دیجی‌سروینو</a>
            
            <nav class="hidden md:flex gap-8 text-gray-500 font-bold text-sm">
                <a href="<?php echo home_url(); ?>" class="hover:text-indigo-600 transition">صفحه اصلی</a>
                <a href="<?php echo home_url(); ?>#services" class="hover:text-indigo-600 transition">خدمات تک‌موردی</a>
                <a href="<?php echo home_url(); ?>#pricing" class="hover:text-indigo-600 transition">اشتراک‌های ویژه</a>
            </nav>
        </div>
        
        <div class="flex items-center gap-3">
            <?php if(is_user_logged_in()): ?>
                <?php 
                $user = wp_get_current_user();
                if(current_user_can('administrator')) { $url = home_url('/admin-dashboard'); $label = 'مدیریت کل'; }
                elseif(current_user_can('ds_technician')) { $url = home_url('/tech-dashboard'); $label = 'پنل فنی'; }
                else { $url = home_url('/client-dashboard'); $label = 'میز کار من'; }
                ?>
                
                <div class="flex items-center gap-4 bg-gray-50 p-1 pr-4 rounded-2xl border border-gray-100">
                    <div class="text-right leading-tight ml-2">
                        <div class="text-[10px] text-indigo-500 uppercase font-black"><?php echo $label; ?></div>
                        <div class="text-xs font-bold text-gray-700"><?php echo $user->display_name; ?></div>
                    </div>
                    <a href="<?php echo $url; ?>" class="bg-indigo-600 text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-md shadow-indigo-100">پنل مدیریت</a>
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="mr-2 text-gray-400 hover:text-red-500 transition" title="خروج">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </a>
                </div>
            <?php else: ?>
                <a href="<?php echo home_url('/my-account'); ?>" class="text-gray-600 font-bold text-sm px-4 hover:text-indigo-600">ورود</a>
                <a href="<?php echo home_url('/my-account'); ?>" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">ثبت نام</a>
            <?php endif; ?>
        </div>
    </div>
</header>