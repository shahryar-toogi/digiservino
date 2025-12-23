<?php
namespace DigiServino\Api;

class Ticket_Controller extends \WP_REST_Controller {
    
    public function register_routes() {
        $namespace = 'digi-servino/v1';

        // 1. Get Ticket List & Create Ticket
        register_rest_route($namespace, '/tickets', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_tickets'],
                'permission_callback' => [$this, 'permissions_check']
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'create_ticket'],
                'permission_callback' => [$this, 'permissions_check']
            ]
        ]);

        // 2. Get Single Ticket Details (inc. Messages)
        register_rest_route($namespace, '/tickets/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_ticket_detail'],
            'permission_callback' => [$this, 'permissions_check']
        ]);

        // 3. Update Ticket (Status/Reply)
        register_rest_route($namespace, '/tickets/(?P<id>\d+)/reply', [
            'methods' => 'POST',
            'callback' => [$this, 'add_reply'],
            'permission_callback' => [$this, 'permissions_check']
        ]);

        // Register inside register_routes()
register_rest_route($namespace, '/tickets/(?P<id>\d+)/logs', [
    [
        'methods' => 'GET',
        'callback' => [$this, 'get_work_logs'],
        'permission_callback' => [$this, 'permissions_check']
    ],
    [
        'methods' => 'POST',
        'callback' => [$this, 'add_work_log'],
        'permission_callback' => [$this, 'permissions_check']
    ]
]);
    }



    // Callback: Get Logs
public function get_work_logs($request) {
    global $wpdb;
    $id = $request['id'];
    $logs = $wpdb->get_results($wpdb->prepare("
        SELECT l.*, u.display_name as tech_name 
        FROM {$wpdb->prefix}ds_work_logs l
        JOIN {$wpdb->users} u ON l.tech_id = u.ID
        WHERE l.ticket_id = %d ORDER BY l.created_at DESC
    ", $id));
    return rest_ensure_response($logs);
}

// Callback: Add Log
public function add_work_log($request) {
    if (!current_user_can('ds_technician')) return new \WP_Error('forbidden', 'Only techs can log work', ['status' => 403]);
    
    global $wpdb;
    $params = $request->get_json_params();
    $wpdb->insert($wpdb->prefix . 'ds_work_logs', [
        'ticket_id' => $request['id'],
        'tech_id' => get_current_user_id(),
        'duration_minutes' => intval($params['minutes']),
        'work_details' => sanitize_textarea_field($params['note'])
    ]);
    return rest_ensure_response(['status' => 'success']);
}

    public function permissions_check() {
        return is_user_logged_in();
    }

    // --- CALLBACKS ---

    public function get_tickets($request) {
    global $wpdb;
    $user_id = get_current_user_id();
    
    // If technician/admin, show all. If client, show only theirs.
    if (current_user_can('ds_technician') || current_user_can('administrator')) {
        $tickets = $wpdb->get_results("SELECT t.*, u.display_name as client_name 
                                       FROM {$wpdb->prefix}ds_tickets t 
                                       LEFT JOIN {$wpdb->users} u ON t.user_id = u.ID 
                                       ORDER BY t.created_at DESC");
    } else {
        $tickets = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ds_tickets WHERE user_id = %d ORDER BY created_at DESC", 
            $user_id
        ));
    }
    
    return rest_ensure_response($tickets);
}

    public function create_ticket($request) {
        global $wpdb;
        $user_id = get_current_user_id();
        $params = $request->get_json_params();

        // Basic Validation
        if (empty($params['subject']) || empty($params['description'])) {
            return new \WP_Error('missing_data', 'Subject and Description are required', ['status' => 400]);
        }

        $table = $wpdb->prefix . 'ds_tickets';
        
        $wpdb->insert($table, [
            'user_id' => $user_id,
            'subject' => sanitize_text_field($params['subject']),
            'description' => sanitize_textarea_field($params['description']),
            'priority' => sanitize_text_field($params['priority'] ?? 'normal'),
            'status' => 'open',
            'remote_access_requested' => !empty($params['remote']) ? 1 : 0
        ]);

        return rest_ensure_response(['id' => $wpdb->insert_id, 'message' => 'Ticket Created']);
    }

    public function get_ticket_detail($request) {
        global $wpdb;
        $ticket_id = $request['id'];
        
        // Fetch Ticket
        $ticket = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ds_tickets WHERE id = %d", $ticket_id));

        // Fetch Messages
        $msgs_table = $wpdb->prefix . 'ds_ticket_messages'; // Assuming table exists (see Activator)
        // If table wasn't created in step 1, add it to Activator now or assume it exists.
        
        // We need a quick fallback if message table query fails (for robust code)
        $messages = []; 
        // Note: You must ensure 'ds_ticket_messages' table exists in your DB from the Activator.
        // Assuming it does:
        /*
        $messages = $wpdb->get_results($wpdb->prepare("
            SELECT m.*, u.display_name 
            FROM {$wpdb->prefix}ds_ticket_messages m
            LEFT JOIN {$wpdb->users} u ON m.user_id = u.ID
            WHERE ticket_id = %d ORDER BY created_at ASC
        ", $ticket_id));
        */

        return rest_ensure_response([
            'ticket' => $ticket,
            'messages' => $messages // currently empty until we implement the table fully
        ]);
    }

    public function add_reply($request) {
        // Implementation for replying coming in Phase 3
        return rest_ensure_response(['status' => 'Reply logic placeholder']);
    }

    public function launch_remote_session($request) {
    $ticket_id = $request['id'];
    
    // 1. Audit Log the attempt
    global $wpdb;
    $wpdb->insert($wpdb->prefix . 'ds_remote_logs', [
        'ticket_id' => $ticket_id,
        'tech_id' => get_current_user_id(),
        'action_type' => 'connection_start'
    ]);

    // 2. Logic to generate a session link 
    // Example for a custom RustDesk relay or simple protocol:
    $client_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}ds_tickets WHERE id = %d", $ticket_id));
    
    return rest_ensure_response([
        'url' => "rustdesk://connection/direct/" . $client_id, // Custom protocol handler
        'message' => 'Remote session initiated'
    ]);
}
}