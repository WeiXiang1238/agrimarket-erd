<?php

require_once 'BaseModel.php';

/**
 * NotificationSetting Model
 * Represents the notification_settings table structure
 */
class NotificationSetting extends BaseModel
{
    protected $table = 'notification_settings';
    protected $primaryKey = 'setting_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'user_id',
        'order_updates',
        'promotional_emails',
        'low_stock_alerts',
        'sms_notifications',
        'push_notifications',
        'newsletter_subscription',
        'price_alerts',
        'new_product_alerts'
    ];
    
    protected $guarded = [
        'setting_id',
        'created_at',
        'updated_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'setting_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) NOT NULL',
        'order_updates' => 'tinyint(1) DEFAULT 1',
        'promotional_emails' => 'tinyint(1) DEFAULT 1',
        'low_stock_alerts' => 'tinyint(1) DEFAULT 1',
        'sms_notifications' => 'tinyint(1) DEFAULT 0',
        'push_notifications' => 'tinyint(1) DEFAULT 1',
        'newsletter_subscription' => 'tinyint(1) DEFAULT 1',
        'price_alerts' => 'tinyint(1) DEFAULT 0',
        'new_product_alerts' => 'tinyint(1) DEFAULT 0',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id'
    ];
} 