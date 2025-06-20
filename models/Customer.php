<?php

require_once 'BaseModel.php';

/**
 * Customer Model
 * Represents the customers table structure
 */
class Customer extends BaseModel
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    protected $timestamps = true;
    protected $softDeletes = true;
    
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'shipping_address',
        'billing_address',
        'loyalty_points',
        'total_orders',
        'total_spent',
        'preferred_payment_method',
        'marketing_consent'
    ];
    
    protected $guarded = [
        'customer_id',
        'created_at',
        'updated_at',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'customer_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) NOT NULL',
        'first_name' => 'varchar(50) NOT NULL',
        'last_name' => 'varchar(50) NOT NULL',
        'date_of_birth' => 'date DEFAULT NULL',
        'gender' => 'enum("male","female","other") DEFAULT NULL',
        'shipping_address' => 'text DEFAULT NULL',
        'billing_address' => 'text DEFAULT NULL',
        'loyalty_points' => 'int(11) DEFAULT 0',
        'total_orders' => 'int(11) DEFAULT 0',
        'total_spent' => 'decimal(10,2) DEFAULT 0.00',
        'preferred_payment_method' => 'varchar(50) DEFAULT NULL',
        'marketing_consent' => 'tinyint(1) DEFAULT 0',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id',
        'orders' => 'hasMany:Order:customer_id',
        'reviews' => 'hasMany:Review:customer_id',
        'shopping_cart' => 'hasMany:ShoppingCart:customer_id',
        'addresses' => 'hasMany:CustomerAddress:customer_id',
        'preferences' => 'hasMany:CustomerPreference:customer_id',
        'vendor_reviews' => 'hasMany:VendorReview:customer_id'
    ];
}

?> 