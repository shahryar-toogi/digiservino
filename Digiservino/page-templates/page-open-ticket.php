<?php
/* Template Name: Open New Ticket */
if (!is_user_logged_in()) { auth_redirect(); }
get_header(); ?>

<div class="min-h-screen bg-gray-50 py-12" dir="rtl" x-data="ticketForm()">
    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-xl p-10">
        <h1 class="text-3xl font-black text-gray-800 mb-8">ثبت درخواست پشتیبانی</h1>
        
        <form @submit.prevent="submitTicket">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">موضوع مشکل</label>
                    <input type="text" x-model="subject" class="w-full p-4 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="مثلاً: قطع شدن اینترنت" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">اولویت</label>
                    <select x-model="priority" class="w-full p-4 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="normal">عادی</option>
                        <option value="high">فوری (بحرانی)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">توضیحات کامل</label>
                    <textarea x-model="description" rows="5" class="w-full p-4 bg-gray-50 border rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="جزئیات مشکل را بنویسید..." required></textarea>
                </div>

                <button type="submit" :disabled="submitting" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black text-lg hover:bg-indigo-700 transition disabled:opacity-50">
                    <span x-show="!submitting">ارسال درخواست</span>
                    <span x-show="submitting">در حال ارسال...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ticketForm() {
    return {
        subject: '', description: '', priority: 'normal', submitting: false,
        async submitTicket() {
            this.submitting = true;
            const res = await fetch(dsSettings.root + 'digi-servino/v1/tickets', {
                method: 'POST',
                headers: { 'X-WP-Nonce': dsSettings.nonce, 'Content-Type': 'application/json' },
                body: JSON.stringify({ subject: this.subject, description: this.description, priority: this.priority })
            });
            if (res.ok) { window.location.href = '/my-account'; }
            this.submitting = false;
        }
    }
}
</script>
<?php get_footer(); ?>