<?php
namespace DigiServino\Core;

class SaaS_Manager {
    public static function has_active_plan($user_id) {
        global $wpdb;
        $plan = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ds_subscriptions WHERE user_id = %d AND status = 'active'", 
            $user_id
        ));
        
        // For development: allow admins/techs or if plan exists
        if (current_user_can('manage_options')) return true;
        
        return $plan ? true : false;
    }
}