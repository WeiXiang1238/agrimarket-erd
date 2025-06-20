<?php

require_once 'BaseModel.php';

/**
 * ShoppingCart Model
 * Represents the shopping_cart table structure
 */
class ShoppingCart extends BaseModel
{
    protected $table = 'shopping_cart';
    protected $primaryKey = 'cart_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'customer_id',
        'product_id',
        'quantity'
    ];
    
    protected $guarded = [
        'cart_id',
        'added_at',
        'updated_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'cart_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'customer_id' => 'int(11) NOT NULL',
        'product_id' => 'int(11) NOT NULL',
        'quantity' => 'int(11) NOT NULL DEFAULT 1',
        'added_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'customer' => 'belongsTo:Customer:customer_id',
        'product' => 'belongsTo:Product:product_id'
    ];
} 