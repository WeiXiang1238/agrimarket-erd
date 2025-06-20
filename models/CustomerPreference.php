<?php

require_once 'BaseModel.php';

/**
 * CustomerPreference Model
 * Represents the customer_preferences table structure
 */
class CustomerPreference extends BaseModel
{
    protected $table = 'customer_preferences';
    protected $primaryKey = 'preference_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'customer_id',
        'product_id',
        'vendor_id',
        'category_id',
        'preference_type',
        'notes'
    ];
    
    protected $guarded = [
        'preference_id',
        'added_at',
        'updated_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'preference_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'customer_id' => 'int(11) NOT NULL',
        'product_id' => 'int(11) DEFAULT NULL',
        'vendor_id' => 'int(11) DEFAULT NULL',
        'category_id' => 'int(11) DEFAULT NULL',
        'preference_type' => 'enum("favorite_product","favorite_vendor","favorite_category","wishlist","compare") NOT NULL',
        'notes' => 'text DEFAULT NULL',
        'added_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'customer' => 'belongsTo:Customer:customer_id',
        'product' => 'belongsTo:Product:product_id',
        'vendor' => 'belongsTo:Vendor:vendor_id',
        'category' => 'belongsTo:ProductCategory:category_id'
    ];
} 