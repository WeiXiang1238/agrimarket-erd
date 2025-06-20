<?php

require_once 'BaseModel.php';

/**
 * PaymentMethod Model
 * Represents the payment_methods table structure
 */
class PaymentMethod extends BaseModel
{
    protected $table = 'payment_methods';
    protected $primaryKey = 'payment_method_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'processing_fee_percent',
        'min_amount',
        'max_amount',
        'sort_order'
    ];
    
    protected $guarded = [
        'payment_method_id',
        'created_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'payment_method_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'name' => 'varchar(50) NOT NULL',
        'code' => 'varchar(20) NOT NULL',
        'description' => 'text DEFAULT NULL',
        'is_active' => 'tinyint(1) DEFAULT 1',
        'processing_fee_percent' => 'decimal(5,2) DEFAULT 0.00',
        'min_amount' => 'decimal(10,2) DEFAULT 0.00',
        'max_amount' => 'decimal(10,2) DEFAULT NULL',
        'sort_order' => 'int(11) DEFAULT 0',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'payments' => 'hasMany:Payment:payment_method_id'
    ];
} 