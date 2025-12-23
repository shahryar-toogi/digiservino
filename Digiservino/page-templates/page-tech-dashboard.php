<?php
/* Template Name: Technician Dashboard */
if (!current_user_can('ds_technician') && !current_user_can('administrator')) { wp_redirect(home_url()); exit; }
get_header(); 
?>

<div class="min-h-screen bg-gray-900 text-white" x-data="techDashboard()">
    <div class="flex justify-between items-center p-6 border-b border-gray-700 bg-gray-800">
        <div class="flex items-center space-x-4">
            <span class="text-2xl font-bold tracking-tight text-blue-400">DigiServino</span>
            <span class="px-3 py-1 bg-gray-700 rounded text-xs text-gray-300 uppercase">Tech Portal</span>
        </div>
        <div class="flex space-x-4">
             <div class="text-right">
                <div class="text-sm font-bold"><?php echo wp_get_current_user()->display_name; ?></div>
                <div class="text-xs text-green-400">● Online</div>
             </div>
        </div>
    </div>

    <div class="p-8">
        <div class="flex space-x-4 mb-8">
            <button @click="filter='all'" :class="filter==='all' ? 'bg-blue-600' : 'bg-gray-700'" class="px-4 py-2 rounded">All Tickets</button>
            <button @click="filter='open'" :class="filter==='open' ? 'bg-blue-600' : 'bg-gray-700'" class="px-4 py-2 rounded">Open</button>
            <button @click="filter='urgent'" :class="filter==='urgent' ? 'bg-blue-600' : 'bg-gray-700'" class="px-4 py-2 rounded text-red-300">Urgent</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <template x-for="ticket in filteredTickets" :key="ticket.id">
                <div class="bg-gray-800 rounded-lg p-5 border-l-4 hover:bg-gray-750 transition transform hover:-translate-y-1 cursor-pointer shadow-lg"
                     :class="{
                        'border-green-500': ticket.status === 'closed',
                        'border-red-500': ticket.priority === 'high' && ticket.status !== 'closed',
                        'border-blue-500': ticket.priority !== 'high' && ticket.status !== 'closed'
                     }">
                    
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-mono text-gray-500 text-sm" x-text="'#'+ticket.id"></span>
                        <span class="text-xs px-2 py-1 rounded bg-gray-700" x-text="ticket.status"></span>
                    </div>

                    <h3 class="font-bold text-lg mb-2 truncate" x-text="ticket.subject"></h3>
                    <p class="text-gray-400 text-sm mb-4 line-clamp-2" x-text="ticket.description"></p>

                    <div class="flex items-center justify-between mt-4 border-t border-gray-700 pt-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-900 flex items-center justify-center text-xs font-bold mr-2">
                                <span x-text="ticket.client_name ? ticket.client_name.substring(0,2) : 'CL'"></span>
                            </div>
                        </div>
                        
                        <a :href="'/view-ticket/?id=' + ticket.id" class="text-blue-400 hover:text-white text-sm font-bold">Manage &rarr;</a>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('techDashboard', () => ({
        tickets: [],
        filter: 'all',
        
        get filteredTickets() {
            if (this.filter === 'open') return this.tickets.filter(t => t.status === 'open');
            if (this.filter === 'urgent') return this.tickets.filter(t => t.priority === 'high');
            return this.tickets;
        },

        init() {
            this.fetchTickets();
            setInterval(() => this.fetchTickets(), 15000); // Live polling
        },

        async fetchTickets() {
            const res = await fetch(dsSettings.root + 'digi-servino/v1/tickets', {
                headers: { 'X-WP-Nonce': dsSettings.nonce }
            });
            this.tickets = await res.json();
        }
    }));
});
</script>
<?php get_footer(); ?>