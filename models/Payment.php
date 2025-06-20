<?php

require_once 'BaseModel.php';

/**
 * Payment Model
 * Represents the payments table structure
 */
class Payment extends BaseModel
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'order_id',
        'payment_method_id',
        'amount',
        'currency',
        'transaction_id',
        'reference_number',
        'status',
        'gateway_response',
        'failure_reason',
        'processed_at'
    ];
    
    protected $guarded = [
        'payment_id',
        'created_at',
        'updated_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'payment_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'order_id' => 'int(11) NOT NULL',
        'payment_method_id' => 'int(11) NOT NULL',
        'amount' => 'decimal(10,2) NOT NULL',
        'currency' => 'varchar(3) DEFAULT "MYR"',
        'transaction_id' => 'varchar(100) DEFAULT NULL',
        'reference_number' => 'varchar(100) DEFAULT NULL',
        'status' => 'enum("pending","processing","completed","failed","cancelled","refunded") DEFAULT "pending"',
        'gateway_response' => 'text DEFAULT NULL',
        'failure_reason' => 'text DEFAULT NULL',
        'processed_at' => 'timestamp NULL DEFAULT NULL',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'order' => 'belongsTo:Order:order_id',
        'payment_method' => 'belongsTo:PaymentMethod:payment_method_id'
    ];
} 