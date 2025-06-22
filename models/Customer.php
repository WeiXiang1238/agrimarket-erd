<?php

require_once 'BaseModel.php';

/**
 * Customer Model
 * Represents the customers table structure
 * 
 * Note: This is a minimal table that links users to customer-specific data
 * Personal details (first_name, last_name, date_of_birth, gender) are stored in the users table
 * All addresses are handled via the customer_addresses table (CustomerAddress model)
 * Business data like loyalty points, orders, etc. would be calculated from related tables
 */
class Customer extends BaseModel
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    protected $timestamps = false;
    protected $softDeletes = true;
    
    protected $fillable = [
        'user_id',
        'phone'
    ];
    
    protected $guarded = [
        'customer_id',
        'is_archive'
    ];
    
    // Table columns definition (matches actual database structure)
    protected $columns = [
        'customer_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) DEFAULT NULL',
        'phone' => 'varchar(20) DEFAULT NULL',
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