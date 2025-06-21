<?php

require_once 'BaseModel.php';

/**
 * Vendor Model
 * Represents the vendors table structure
 */
class Vendor extends BaseModel
{
    protected $table = 'vendors';
    protected $primaryKey = 'vendor_id';
    protected $timestamps = false;
    protected $softDeletes = true;
    
    protected $fillable = [
        'user_id',
        'business_name',
        'contact_number',
        'address',
        'website_url',
        'description',
        'subscription_tier_id',
        'tier_id'
    ];
    
    protected $guarded = [
        'vendor_id',
        'registration_date',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'vendor_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) NOT NULL',
        'business_name' => 'varchar(100) NOT NULL',
        'contact_number' => 'varchar(20) NOT NULL',
        'address' => 'text NOT NULL',
        'website_url' => 'varchar(255) NULL',
        'description' => 'text NULL',
        'subscription_tier_id' => 'int(11) NOT NULL',
        'registration_date' => 'date NOT NULL DEFAULT current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0',
        'tier_id' => 'int(11) DEFAULT 1'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id',
        'subscription_tier' => 'belongsTo:SubscriptionTier:subscription_tier_id',
        'tier' => 'belongsTo:SubscriptionTier:tier_id',
        'products' => 'hasMany:Product:vendor_id',
        'orders' => 'hasMany:Order:vendor_id',
        'reviews' => 'hasMany:VendorReview:vendor_id',
        'subscriptions' => 'hasMany:VendorSubscription:vendor_id',
        'settings' => 'hasOne:VendorSetting:vendor_id'
    ];
}

?> 