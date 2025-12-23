<?php
namespace DigiServino\Database;

class Ticket_Manager {
    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'ds_tickets';
    }

    /**
     * Create a new ticket
     */
    public function create_ticket($user_id, $data) {
        global $wpdb;
        
        $wpdb->insert(
            $this->table,
            [
                'user_id' => $user_id,
                'subject' => sanitize_text_field($data['subject']),
                'priority' => sanitize_text_field($data['priority']),
                'remote_access_requested' => isset($data['remote']) ? 1 : 0
            ],
            ['%d', '%s', '%s', '%d']
        );
        
        return $wpdb->insert_id;
    }

    /**
     * Get Tickets for Technician Dashboard
     * Logic: Return array of client objects containing their tickets
     */
    public function get_technician_overview() {
        global $wpdb;
        
        // Complex query to group tickets by status for the "Red/Green" logic
        $sql = "SELECT t.*, u.user_email, u.display_name 
                FROM {$this->table} t
                JOIN {$wpdb->users} u ON t.user_id = u.ID
                ORDER BY t.priority DESC, t.created_at ASC";
                
        return $wpdb->get_results($sql, ARRAY_A);
    }
}