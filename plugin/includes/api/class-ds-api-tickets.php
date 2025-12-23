<?php
namespace DigiServino\Api;
use DigiServino\Database\Ticket_Manager;

class Ticket_Controller extends \WP_REST_Controller {
    
    public function register_routes() {
        $namespace = 'digi-servino/v1';
        
        register_rest_route($namespace, '/tickets', [
            'methods' => 'GET',
            'callback' => [$this, 'get_items'],
            'permission_callback' => [$this, 'permissions_check']
        ]);

        register_rest_route($namespace, '/tickets', [
            'methods' => 'POST',
            'callback' => [$this, 'create_item'],
            'permission_callback' => [$this, 'permissions_check']
        ]);
    }

    public function permissions_check($request) {
        return is_user_logged_in();
    }

    public function get_items($request) {
        $user = wp_get_current_user();
        $manager = new Ticket_Manager();

        if (in_array('ds_technician', (array) $user->roles) || in_array('administrator', (array) $user->roles)) {
            // Tech sees all/assigned
            $data = $manager->get_technician_overview();
        } else {
            // Client sees own
            global $wpdb;
            $table = $wpdb->prefix . 'ds_tickets';
            $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user->ID));
        }

        return rest_ensure_response($data);
    }
}