<?php

require_once 'BaseModel.php';

/**
 * VendorSetting Model
 * Represents the vendor_settings table structure
 */
class VendorSetting extends BaseModel
{
    protected $table = 'vendor_settings';
    protected $primaryKey = 'setting_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'vendor_id',
        'business_license',
        'tax_number',
        'business_type',
        'return_policy',
        'shipping_policy',
        'minimum_order_amount',
        'free_shipping_threshold',
        'processing_time_days',
        'auto_accept_orders',
        'vacation_mode',
        'vacation_message'
    ];
    
    protected $guarded = [
        'setting_id',
        'created_at',
        'updated_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'setting_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'vendor_id' => 'int(11) NOT NULL',
        'business_license' => 'varchar(100) DEFAULT NULL',
        'tax_number' => 'varchar(50) DEFAULT NULL',
        'business_type' => 'varchar(100) DEFAULT NULL',
        'return_policy' => 'text DEFAULT NULL',
        'shipping_policy' => 'text DEFAULT NULL',
        'minimum_order_amount' => 'decimal(10,2) DEFAULT 0.00',
        'free_shipping_threshold' => 'decimal(10,2) DEFAULT NULL',
        'processing_time_days' => 'int(11) DEFAULT 3',
        'auto_accept_orders' => 'tinyint(1) DEFAULT 0',
        'vacation_mode' => 'tinyint(1) DEFAULT 0',
        'vacation_message' => 'text DEFAULT NULL',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'vendor' => 'belongsTo:Vendor:vendor_id'
    ];
} 