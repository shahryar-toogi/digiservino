<?php
namespace DigiServino\Admin;

class Admin_Menu {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_pages']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function add_menu_pages() {
        add_menu_page(
            'DigiServino', 
            'DigiServino', 
            'manage_options', 
            'digi-servino-main', 
            [$this, 'render_dashboard'], 
            'dashicons-chart-area', 
            6
        );
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook != 'toplevel_page_digi-servino-main') return;
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js');
    }

    public function render_dashboard() {
        // Simple Report Data Logic (Inline for brevity)
        global $wpdb;
        $total_tickets = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ds_tickets");
        $open_tickets = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ds_tickets WHERE status='open'");
        $revenue = 12500; // Placeholder until billing module is connected
        $tickets = $wpdb->get_results("SELECT status, COUNT(*) as count FROM {$wpdb->prefix}ds_tickets GROUP BY status");
        $revenue_data = [5, 12, 8, 20, 15, 30, 45]; // MOCK DATA
        ?>
        <div class="wrap">
            <h1>DigiServino Overview</h1>
            
            <div style="display: flex; gap: 20px; margin-top: 20px;">
                <div style="background: white; padding: 20px; border-left: 5px solid #2271b1; box-shadow: 0 1px 3px rgba(0,0,0,.1);">
                    <h3>Total Tickets</h3>
                    <p style="font-size: 2em; margin: 0;"><?php echo $total_tickets; ?></p>
                </div>
                <div style="background: white; padding: 20px; border-left: 5px solid #d63638; box-shadow: 0 1px 3px rgba(0,0,0,.1);">
                    <h3>Open Issues</h3>
                    <p style="font-size: 2em; margin: 0;"><?php echo $open_tickets; ?></p>
                </div>
                <div style="background: white; padding: 20px; border-left: 5px solid #00a32a; box-shadow: 0 1px 3px rgba(0,0,0,.1);">
                    <h3>Revenue (MTD)</h3>
                    <p style="font-size: 2em; margin: 0;">$<?php echo number_format($revenue); ?></p>
                </div>
            </div>

            <div style="background: white; padding: 20px; margin-top: 20px; max-width: 600px;">
                <canvas id="ticketChart"></canvas>
            </div>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const ctx = document.getElementById('ticketChart');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Open', 'Closed', 'Pending'],
                        datasets: [{
                            data: [<?php echo $open_tickets; ?>, <?php echo $total_tickets - $open_tickets; ?>, 2],
                            backgroundColor: ['#d63638', '#00a32a', '#f0c33c']
                        }]
                    }
                });
            });
            </script>
        </div>

    <div class="wrap" style="background: #f0f2f5; padding: 20px;">
        <h1 style="font-weight: 900; margin-bottom: 30px;">پیشخوان مدیریتی دیجی‌سروینو</h1>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h2 style="margin-top:0">وضعیت تیکت‌ها</h2>
                <canvas id="ticketPieChart"></canvas>
            </div>

            <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h2 style="margin-top:0">روند درآمد (میلیون تومان)</h2>
                <canvas id="revenueLineChart"></canvas>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pie Chart
            new Chart(document.getElementById('ticketPieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['باز', 'بسته شده', 'در حال بررسی'],
                    datasets: [{
                        data: [15, 45, 10],
                        backgroundColor: ['#6366f1', '#10b981', '#f59e0b']
                    }]
                },
                options: { cutout: '70%' }
            });

            // Line Chart
            new Chart(document.getElementById('revenueLineChart'), {
                type: 'line',
                data: {
                    labels: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر'],
                    datasets: [{
                        label: 'درآمد کل',
                        data: <?php echo json_encode($revenue_data); ?>,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        });
        </script>
    </div>
        <?php
    }
}

// Instantiate
new Admin_Menu();