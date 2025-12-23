// includes/Models/class-ds-subscription.php
public function can_create_ticket($user_id) {
    $sub = $this->get_subscription($user_id);
    
    if ($sub->status === 'active') {
        return true;
    }
    
    if ($sub->credits > 0) {
        return true;
    }
    
    return false; // Frontend will redirect to payment
}