<?php

require_once 'BaseModel.php';

/**
 * OrderItem Model
 * Represents the order_items table structure
 */
class OrderItem extends BaseModel
{
    protected $table = 'order_items';
    protected $primaryKey = 'item_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_at_purchase',
        'subtotal'
    ];
    
    protected $guarded = [
        'item_id'
    ];
    
    // Table columns definition
    protected $columns = [
        'item_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'order_id' => 'int(11) NOT NULL',
        'product_id' => 'int(11) NOT NULL',
        'quantity' => 'int(11) NOT NULL',
        'price_at_purchase' => 'decimal(10,2) NOT NULL',
        'subtotal' => 'decimal(10,2) NOT NULL'
    ];
    
    // Relationships
    protected $relationships = [
        'order' => 'belongsTo:Order:order_id',
        'product' => 'belongsTo:Product:product_id'
    ];
} 