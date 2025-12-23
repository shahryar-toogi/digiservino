<?php
/* Template Name: Service Request Form */
if (!is_user_logged_in()) { auth_redirect(); }
get_header(); 
?>

<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" x-data="serviceRequest()">
    <div class="max-w-3xl mx-auto">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900">Open Service Request</h1>
            <p class="mt-2 text-sm text-gray-600">Tell us what's wrong, and we'll fix it.</p>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="bg-gray-200 h-2 w-full">
                <div class="bg-blue-600 h-2 transition-all duration-300" :style="'width: ' + (step * 33) + '%'"></div>
            </div>

            <form @submit.prevent="submitForm" class="p-8">
                
                <div x-show="step === 1">
                    <h2 class="text-xl font-bold mb-6 text-gray-800">1. Select Service Type</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="type in types">
                            <div @click="formData.category = type.id; step = 2"
                                 class="cursor-pointer border-2 rounded-lg p-6 hover:border-blue-500 hover:bg-blue-50 transition flex items-center space-x-4">
                                <div class="text-3xl" x-text="type.icon"></div>
                                <div>
                                    <h3 class="font-bold text-gray-900" x-text="type.name"></h3>
                                    <p class="text-xs text-gray-500" x-text="type.desc"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="step === 2">
                    <h2 class="text-xl font-bold mb-6 text-gray-800">2. Issue Details</h2>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Subject</label>
                        <input type="text" x-model="formData.subject" class="w-full p-3 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., Laptop won't turn on">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea x-model="formData.description" class="w-full p-3 border rounded h-32 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Please describe the issue..."></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Priority</label>
                        <select x-model="formData.priority" class="w-full p-3 border rounded bg-white">
                            <option value="low">Low - Routine Maintainence</option>
                            <option value="normal">Normal - Standard Issue</option>
                            <option value="high">High - Work Stoppage</option>
                        </select>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" @click="step = 1" class="text-gray-500 hover:text-gray-800">Back</button>
                        <button type="button" @click="step = 3" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">Next Step</button>
                    </div>
                </div>

                <div x-show="step === 3">
                    <h2 class="text-xl font-bold mb-6 text-gray-800">3. Remote Access</h2>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Do you authorize our technicians to remotely connect to your device if needed?
                                </p>
                            </div>
                        </div>
                    </div>

                    <label class="flex items-center space-x-3 mb-8 p-4 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" x-model="formData.remote" class="form-checkbox h-6 w-6 text-blue-600">
                        <span class="font-bold text-gray-700">Yes, I authorize remote access for this ticket.</span>
                    </label>

                    <div class="flex justify-between items-center">
                        <button type="button" @click="step = 2" class="text-gray-500 hover:text-gray-800">Back</button>
                        <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 shadow-lg transform hover:-translate-y-1 transition">
                            <span x-show="!submitting">Submit Ticket</span>
                            <span x-show="submitting">Processing...</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('serviceRequest', () => ({
        step: 1,
        submitting: false,
        formData: { category: '', subject: '', description: '', priority: 'normal', remote: false },
        types: [
            { id: 'pc', name: 'PC Repair', desc: 'Hardware/Boot issues', icon: '💻' },
            { id: 'net', name: 'Network', desc: 'WiFi/Server issues', icon: '📡' },
            { id: 'soft', name: 'Software', desc: 'Installations/Viruses', icon: '💿' },
            { id: 'cctv', name: 'CCTV', desc: 'Surveillance Systems', icon: '📹' }
        ],
        async submitForm() {
            this.submitting = true;
            try {
                const res = await fetch(dsSettings.root + 'digi-servino/v1/tickets', {
                    method: 'POST',
                    headers: { 'X-WP-Nonce': dsSettings.nonce, 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.formData)
                });
                const data = await res.json();
                if(data.id) {
                    window.location.href = '/my-dashboard'; // Redirect to dashboard
                }
            } catch (e) { alert('Error submitting ticket'); }
            this.submitting = false;
        }
    }));
});
</script>
<?php get_footer(); ?>