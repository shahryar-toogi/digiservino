<?php
namespace DigiServino\Core;

class Activator {
    public static function activate() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

        // 1. Tickets Table
        $table_name = $wpdb->prefix . 'ds_tickets';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            assigned_tech_id bigint(20) DEFAULT 0,
            subject varchar(255) NOT NULL,
            description text NOT NULL,
            status varchar(50) DEFAULT 'open',
            priority varchar(20) DEFAULT 'normal',
            remote_access_requested tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // 2. Roles
        self::create_roles();

        // Subscriptions Table
    $sql_subs = "CREATE TABLE {$wpdb->prefix}ds_subscriptions (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        plan_name varchar(100) NOT NULL, -- 'Basic', 'Premium', 'Pay-as-you-go'
        status varchar(50) DEFAULT 'active',
        expiry_date datetime DEFAULT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($sql_subs);

    // Work Logs Table
$sql_logs = "CREATE TABLE {$wpdb->prefix}ds_work_logs (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    ticket_id mediumint(9) NOT NULL,
    tech_id bigint(20) NOT NULL,
    duration_minutes int(11) NOT NULL,
    work_details text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (id)
) $charset_collate;";
dbDelta($sql_logs);
    }

    private static function create_roles() {
        // Client Role
        add_role('ds_client', 'IT Client', [
            'read' => true,
            'ds_view_tickets' => true
        ]);

        // Technician Role
        add_role('ds_technician', 'IT Technician', [
            'read' => true, 
            'ds_view_tickets' => true,
            'ds_manage_tickets' => true,
            'upload_files' => true
        ]);
    }
}