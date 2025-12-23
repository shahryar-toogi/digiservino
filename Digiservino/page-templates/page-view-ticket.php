<?php
/* Template Name: View Ticket Detail */
if (!is_user_logged_in()) { auth_redirect(); }
get_header();

$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<div class="min-h-screen bg-gray-100 py-10" x-data="ticketDetail(<?php echo $ticket_id; ?>)">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="bg-white rounded-t-xl shadow-sm p-6 border-b">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900" x-text="ticket.subject"></h1>
                    <p class="text-sm text-gray-500 mt-1" x-text="'Ticket #' + ticket.id + ' • Opened on ' + formatDate(ticket.created_at)"></p>
                </div>
                <div class="flex flex-col items-end">
                    <span :class="statusColor(ticket.status)" class="px-3 py-1 rounded-full text-xs font-bold uppercase" x-text="ticket.status"></span>
                    
                    <template x-if="ticket.remote_access_requested == 1">
                        <span class="mt-2 flex items-center text-red-600 text-xs font-bold animate-pulse">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Remote Support Enabled
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-6 space-y-4 h-[500px] overflow-y-auto border-x shadow-inner">
            <div class="flex justify-start">
                <div class="bg-blue-100 p-4 rounded-lg max-w-[80%] shadow-sm">
                    <p class="text-xs font-bold text-blue-800 mb-1">Issue Description</p>
                    <p class="text-gray-800 text-sm" x-text="ticket.description"></p>
                </div>
            </div>

            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.user_id == <?php echo get_current_user_id(); ?> ? 'justify-end' : 'justify-start'" class="flex">
                    <div :class="msg.user_id == <?php echo get_current_user_id(); ?> ? 'bg-indigo-600 text-white' : 'bg-white text-gray-800'" 
                         class="p-4 rounded-lg max-w-[80%] shadow-sm">
                        <p class="text-[10px] opacity-75 mb-1" x-text="msg.display_name + ' • ' + formatDate(msg.created_at)"></p>
                        <p class="text-sm" x-text="msg.message"></p>
                    </div>
                </div>
            </template>
        </div>

        <div class="bg-white rounded-b-xl shadow-sm p-4 border-t">
            <div class="flex space-x-3">
                <textarea x-model="newReply" class="flex-1 border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="Type your message..."></textarea>
                <button @click="sendReply()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold self-end hover:bg-indigo-700 transition">
                    Send
                </button>
            </div>
            
            <?php if (current_user_can('ds_technician')): ?>
            <div class="mt-4 pt-4 border-t flex space-x-4">
                <button @click="updateStatus('closed')" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded font-bold">Mark Resolved</button>
                <button @click="openRemoteAccess()" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded font-bold">Launch Remote Session</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mt-6 pt-6 border-t bg-gray-50 p-4 rounded-xl" x-data="workLog(<?php echo $ticket_id; ?>)">
    <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Log Work Time
    </h4>
    <div class="flex flex-col md:flex-row gap-3">
        <input type="number" x-model="log.minutes" placeholder="Minutes" class="w-24 border p-2 rounded text-sm">
        <input type="text" x-model="log.note" placeholder="What was done?" class="flex-1 border p-2 rounded text-sm">
        <button @click="saveLog()" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-black transition">
            Save Log
        </button>
    </div>
    
    <div class="mt-4 overflow-x-auto">
        <table class="w-full text-left text-[11px] text-gray-500">
            <thead>
                <tr class="border-b">
                    <th class="pb-1">Tech</th>
                    <th class="pb-1">Time</th>
                    <th class="pb-1">Note</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="entry in history" :key="entry.id">
                    <tr class="border-b last:border-0">
                        <td class="py-1 font-bold" x-text="entry.tech_name"></td>
                        <td class="py-1" x-text="entry.duration_minutes + 'm'"></td>
                        <td class="py-1 truncate max-w-[150px]" x-text="entry.work_details"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<script>
// Add this to your existing Alpine script or separate
Alpine.data('workLog', (ticketId) => ({
    log: { minutes: '', note: '' },
    history: [],
    async init() { this.fetchLogs(); },
    async fetchLogs() {
        const res = await fetch(`${dsSettings.root}digi-servino/v1/tickets/${ticketId}/logs`, {
            headers: { 'X-WP-Nonce': dsSettings.nonce }
        });
        this.history = await res.json();
    },
    async saveLog() {
        if(!this.log.minutes) return;
        const res = await fetch(`${dsSettings.root}digi-servino/v1/tickets/${ticketId}/logs`, {
            method: 'POST',
            headers: { 'X-WP-Nonce': dsSettings.nonce, 'Content-Type': 'application/json' },
            body: JSON.stringify(this.log)
        });
        if(res.ok) {
            this.log = { minutes: '', note: '' };
            this.fetchLogs();
        }
    }
}));
</script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ticketDetail', (ticketId) => ({
        ticket: {},
        messages: [],
        newReply: '',
        ticketId: ticketId,

        async init() {
            this.fetchData();
            setInterval(() => this.fetchData(), 10000); // Polling for new messages
        },

        async fetchData() {
            const res = await fetch(`${dsSettings.root}digi-servino/v1/tickets/${this.ticketId}`, {
                headers: { 'X-WP-Nonce': dsSettings.nonce }
            });
            const data = await res.json();
            this.ticket = data.ticket;
            this.messages = data.messages;
        },

        async sendReply() {
            if (!this.newReply.trim()) return;
            const res = await fetch(`${dsSettings.root}digi-servino/v1/tickets/${this.ticketId}/reply`, {
                method: 'POST',
                headers: { 
                    'X-WP-Nonce': dsSettings.nonce,
                    'Content-Type': 'application/json' 
                },
                body: JSON.stringify({ message: this.newReply })
            });
            if (res.ok) {
                this.newReply = '';
                this.fetchData();
            }
        },

        statusColor(status) {
            return {
                'open': 'bg-blue-100 text-blue-700',
                'closed': 'bg-green-100 text-green-700',
                'pending': 'bg-yellow-100 text-yellow-700'
            }[status] || 'bg-gray-100';
        },

        formatDate(date) {
            return new Date(date).toLocaleString();
        }
    }));
});
</script>
<?php get_footer(); ?>