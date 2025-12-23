document.addEventListener('alpine:init', () => {
    Alpine.data('clientDashboard', () => ({
        tickets: [],
        loading: true,

        init() {
            console.log('DigiServino App Initialized');
            this.fetchTickets();
        },

        async fetchTickets() {
            try {
                const url = dsSettings.root + 'digi-servino/v1/tickets';
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-WP-Nonce': dsSettings.nonce,
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('API Failed');
                
                this.tickets = await response.json();
            } catch (error) {
                console.error('Error fetching tickets:', error);
            } finally {
                this.loading = false;
            }
        }
    }));
});