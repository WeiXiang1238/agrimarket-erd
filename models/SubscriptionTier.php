<?php

require_once 'BaseModel.php';

/**
 * SubscriptionTier Model
 * Represents the subscription_tiers table structure
 */
class SubscriptionTier extends BaseModel
{
    protected $table = 'subscription_tiers';
    protected $primaryKey = 'tier_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'name',
        'description',
        'max_products',
        'monthly_fee',
        'features'
    ];
    
    protected $guarded = [
        'tier_id'
    ];
    
    // Table columns definition
    protected $columns = [
        'tier_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'name' => 'varchar(50) NOT NULL',
        'description' => 'text DEFAULT NULL',
        'max_products' => 'int(11) DEFAULT NULL',
        'monthly_fee' => 'decimal(10,2) DEFAULT 0.00',
        'features' => 'text DEFAULT NULL'
    ];
    
    // Relationships
    protected $relationships = [
        'vendors' => 'hasMany:Vendor:subscription_tier_id',
        'vendor_subscriptions' => 'hasMany:VendorSubscription:tier_id'
    ];
}

?> 