<?php
/* Template Name: Login/Signup Page */
get_header(); ?>

<div class="min-h-screen bg-indigo-50 flex items-center justify-center p-6" dir="rtl">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-10" x-data="authHandler()">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-black text-indigo-600 mb-2" x-text="mode === 'login' ? 'ورود به پنل' : 'ثبت نام کاربر جدید'"></h1>
            <p class="text-gray-400" x-text="mode === 'login' ? 'خوش آمدید، مشخصات خود را وارد کنید' : 'برای استفاده از خدمات حساب کاربری بسازید'"></p>
        </div>

        <div x-show="successMessage" x-transition x-text="successMessage" class="mb-4 p-4 bg-green-50 text-green-700 rounded-2xl text-sm text-center border border-green-100"></div>

        <div x-show="errorMessage" x-transition x-text="errorMessage" class="mb-4 p-4 bg-red-50 text-red-600 rounded-2xl text-sm text-center border border-red-100"></div>

        <form @submit.prevent="handleSubmit">
            <div class="space-y-4">
                <div x-show="mode === 'signup'" x-transition>
                    <label class="block text-sm font-bold mb-2 text-gray-700">نام کامل</label>
                    <input type="text" x-model="formData.name" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-gray-700">ایمیل یا نام کاربری</label>
                    <input type="text" x-model="formData.username" dir="ltr" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-left" required>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2 text-gray-700">رمز عبور</label>
                    <input type="password" x-model="formData.password" dir="ltr" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-left" required>
                </div>
            </div>

            <div class="flex justify-between items-center mt-6 mb-8 text-sm">
                <a href="<?php echo wp_lostpassword_url(); ?>" class="text-indigo-600 font-bold hover:underline">فراموشی رمز عبور؟</a>
                <label class="flex items-center cursor-pointer">
                    <span class="ml-2 text-gray-600">مرا به خاطر بسپار</span>
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-indigo-600 rounded">
                </label>
            </div>

            <button type="submit" 
                    :disabled="loading"
                    class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-lg hover:bg-indigo-700 transition shadow-xl shadow-indigo-100 disabled:opacity-50">
                <span x-show="!loading" x-text="mode === 'login' ? 'ورود به حساب' : 'تکمیل ثبت نام'"></span>
                <span x-show="loading">در حال پردازش...</span>
            </button>
        </form>

        <div class="mt-8 text-center border-t pt-8">
            <p class="text-gray-500">
                <span x-text="mode === 'login' ? 'حساب کاربری ندارید؟' : 'قبلاً ثبت نام کرده‌اید؟'"></span>
                <button @click="mode = (mode === 'login' ? 'signup' : 'login'); errorMessage = ''; successMessage = '';" class="text-indigo-600 font-bold mr-2">
                    <span x-text="mode === 'login' ? 'ایجاد حساب جدید' : 'وارد شوید'"></span>
                </button>
            </p>
        </div>
    </div>
</div>

<script>
function authHandler() {
    return {
        mode: 'login',
        loading: false,
        errorMessage: '',
        successMessage: '',
        formData: { username: '', password: '', name: '' },
        async handleSubmit() {
            this.loading = true;
            this.errorMessage = '';
            this.successMessage = '';
            
            // Check which endpoint to call
            const endpoint = this.mode === 'login' ? '/auth/login' : '/auth/register';
            
            try {
                const response = await fetch(dsSettings.root + 'digi-servino/v1' + endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.formData)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    this.successMessage = (this.mode === 'login' ? 'با موفقیت وارد شدید.' : 'ثبت‌نام با موفقیت انجام شد.') + ' در حال انتقال...';
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    this.errorMessage = result.message || 'خطایی رخ داده است.';
                }
            } catch (err) {
                this.errorMessage = 'خطای ارتباط با سرور. لطفا اتصال اینترنت خود را چک کنید.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<?php get_footer(); ?>