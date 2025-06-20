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
    protected $timestamps = true;
    protected $softDeletes = true;
    
    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'business_registration_number',
        'contact_person',
        'business_address',
        'business_phone',
        'business_email',
        'website_url',
        'description',
        'logo_path',
        'verification_status',
        'subscription_tier',
        'subscription_start_date',
        'subscription_end_date',
        'total_sales',
        'commission_rate',
        'rating',
        'total_reviews'
    ];
    
    protected $guarded = [
        'vendor_id',
        'created_at',
        'updated_at',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'vendor_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) NOT NULL',
        'business_name' => 'varchar(255) NOT NULL',
        'business_type' => 'varchar(100) DEFAULT NULL',
        'business_registration_number' => 'varchar(100) DEFAULT NULL',
        'contact_person' => 'varchar(100) NOT NULL',
        'business_address' => 'text NOT NULL',
        'business_phone' => 'varchar(20) NOT NULL',
        'business_email' => 'varchar(100) DEFAULT NULL',
        'website_url' => 'varchar(255) DEFAULT NULL',
        'description' => 'text DEFAULT NULL',
        'logo_path' => 'varchar(255) DEFAULT NULL',
        'verification_status' => 'enum("pending","verified","rejected") DEFAULT "pending"',
        'subscription_tier' => 'enum("basic","premium","enterprise") DEFAULT "basic"',
        'subscription_start_date' => 'date DEFAULT NULL',
        'subscription_end_date' => 'date DEFAULT NULL',
        'total_sales' => 'decimal(15,2) DEFAULT 0.00',
        'commission_rate' => 'decimal(5,2) DEFAULT 5.00',
        'rating' => 'decimal(3,2) DEFAULT 0.00',
        'total_reviews' => 'int(11) DEFAULT 0',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id',
        'products' => 'hasMany:Product:vendor_id',
        'orders' => 'hasMany:Order:vendor_id',
        'reviews' => 'hasMany:VendorReview:vendor_id',
        'preferences' => 'hasMany:CustomerPreference:vendor_id',
        'settings' => 'hasOne:VendorSetting:vendor_id'
    ];
}

?> 