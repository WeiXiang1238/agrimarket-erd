<?php

require_once 'BaseModel.php';

/**
 * PromoCode Model
 * Represents the promo_codes table structure
 */
class PromoCode extends BaseModel
{
    protected $table = 'promo_codes';
    protected $primaryKey = 'promo_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active'
    ];
    
    protected $guarded = [
        'promo_id',
        'created_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'promo_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'code' => 'varchar(50) NOT NULL UNIQUE',
        'description' => 'text DEFAULT NULL',
        'discount_type' => 'enum("percentage","fixed_amount") DEFAULT "percentage"',
        'discount_value' => 'decimal(10,2) NOT NULL',
        'min_order_amount' => 'decimal(10,2) DEFAULT 0.00',
        'max_discount_amount' => 'decimal(10,2) DEFAULT NULL',
        'usage_limit' => 'int(11) DEFAULT NULL',
        'used_count' => 'int(11) DEFAULT 0',
        'start_date' => 'datetime NOT NULL',
        'end_date' => 'datetime NOT NULL',
        'is_active' => 'tinyint(1) DEFAULT 1',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'usage' => 'hasMany:PromoCodeUsage:promo_id'
    ];
}

?> 