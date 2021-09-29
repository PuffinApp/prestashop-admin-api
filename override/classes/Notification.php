<?php

class Notification extends NotificationCore {
    
    /**
     * getAll return all the notifications (new order, new customer registration, and new customer message)
     * Get all the notifications.
     *
     * @return array containing the notifications
     */
    public function getAll() {
        $notifications = [];

        foreach ($this->types as $type) {
            $notifications[$type] = Notification::getLastElementsIdsByType($type, 0);
        }

        return $notifications;
    }

}