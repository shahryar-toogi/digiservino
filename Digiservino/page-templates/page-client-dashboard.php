<?php
/* Template Name: Client Dashboard */
get_header(); 
?>

<div class="min-h-screen flex" dir="auto">
    
    <aside class="w-64 bg-gray-900 text-white hidden md:block">
        <div class="p-6 font-bold text-xl tracking-wider">DigiServino</div>
        <nav class="mt-6">
            <a href="#" class="block py-2.5 px-4 bg-gray-800 border-l-4 border-blue-500">Dashboard</a>
            <a href="#" class="block py-2.5 px-4 hover:bg-gray-800 transition duration-200">New Ticket</a>
            <a href="#" class="block py-2.5 px-4 hover:bg-gray-800 transition duration-200">Invoices</a>
            <a href="<?php echo wp_logout_url(home_url()); ?>" class="block py-2.5 px-4 text-red-400 mt-10">Logout</a>
        </nav>
    </aside>

    <main class="flex-1 p-8" x-data="clientDashboard()">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">My Tickets</h1>
        
        <div x-show="loading" class="text-gray-500">Loading your data...</div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden" x-show="!loading">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Subject</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="ticket in tickets" :key="ticket.id">
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap" x-text="'#' + ticket.id"></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap font-bold" x-text="ticket.subject"></p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <span class="relative inline-block px-3 py-1 font-semibold leading-tight rounded-full"
                                      :class="{
                                          'bg-green-200 text-green-900': ticket.status === 'closed',
                                          'bg-yellow-200 text-yellow-900': ticket.status === 'open'
                                      }">
                                    <span aria-hidden="true" class="absolute inset-0 opacity-50 rounded-full"></span>
                                    <span class="relative" x-text="ticket.status"></span>
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap" x-text="new Date(ticket.created_at).toLocaleDateString()"></p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            
            <div x-show="tickets.length === 0" class="p-6 text-center text-gray-500">
                No tickets found. Create one!
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?>