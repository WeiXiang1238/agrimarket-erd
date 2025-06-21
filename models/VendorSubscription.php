<?php

require_once 'BaseModel.php';

/**
 * VendorSubscription Model
 * Represents the vendor_subscriptions table structure
 */
class VendorSubscription extends BaseModel
{
    protected $table = 'vendor_subscriptions';
    protected $primaryKey = 'id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'vendor_id',
        'tier_id',
        'start_date',
        'end_date',
        'payment_amount',
        'is_active'
    ];
    
    protected $guarded = [
        'id'
    ];
    
    // Table columns definition
    protected $columns = [
        'id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'vendor_id' => 'int(11) DEFAULT NULL',
        'tier_id' => 'int(11) DEFAULT NULL',
        'start_date' => 'date DEFAULT NULL',
        'end_date' => 'date DEFAULT NULL',
        'payment_amount' => 'decimal(10,2) DEFAULT NULL',
        'is_active' => 'tinyint(1) DEFAULT 1'
    ];
    
    // Relationships
    protected $relationships = [
        'vendor' => 'belongsTo:Vendor:vendor_id',
        'subscription_tier' => 'belongsTo:SubscriptionTier:tier_id'
    ];
}

?> 