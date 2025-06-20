<?php

require_once 'BaseModel.php';

/**
 * Order Model
 * Represents the orders table structure
 */
class Order extends BaseModel
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    protected $timestamps = true;
    protected $softDeletes = true;
    
    protected $fillable = [
        'customer_id',
        'vendor_id',
        'order_number',
        'order_date',
        'total_amount',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
        'final_amount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'billing_address',
        'delivery_date',
        'tracking_number',
        'notes',
        'cancelled_reason'
    ];
    
    protected $guarded = [
        'order_id',
        'created_at',
        'updated_at',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'order_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'customer_id' => 'int(11) NOT NULL',
        'vendor_id' => 'int(11) NOT NULL',
        'order_number' => 'varchar(50) NOT NULL',
        'order_date' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'total_amount' => 'decimal(10,2) NOT NULL',
        'shipping_cost' => 'decimal(10,2) DEFAULT 0.00',
        'tax_amount' => 'decimal(10,2) DEFAULT 0.00',
        'discount_amount' => 'decimal(10,2) DEFAULT 0.00',
        'final_amount' => 'decimal(10,2) NOT NULL',
        'status' => 'enum("pending","confirmed","processing","shipped","delivered","cancelled","returned") DEFAULT "pending"',
        'payment_status' => 'enum("pending","paid","failed","refunded") DEFAULT "pending"',
        'payment_method' => 'varchar(50) DEFAULT NULL',
        'shipping_address' => 'text NOT NULL',
        'billing_address' => 'text DEFAULT NULL',
        'delivery_date' => 'date DEFAULT NULL',
        'tracking_number' => 'varchar(100) DEFAULT NULL',
        'notes' => 'text DEFAULT NULL',
        'cancelled_reason' => 'text DEFAULT NULL',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0'
    ];
    
    // Relationships
    protected $relationships = [
        'customer' => 'belongsTo:Customer:customer_id',
        'vendor' => 'belongsTo:Vendor:vendor_id',
        'order_items' => 'hasMany:OrderItem:order_id',
        'payments' => 'hasMany:Payment:order_id',
        'reviews' => 'hasMany:Review:order_id',
        'vendor_reviews' => 'hasMany:VendorReview:order_id'
    ];
}

?> 