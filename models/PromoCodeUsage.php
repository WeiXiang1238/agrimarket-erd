<?php

require_once 'BaseModel.php';

/**
 * PromoCodeUsage Model
 * Represents the promo_code_usage table structure
 */
class PromoCodeUsage extends BaseModel
{
    protected $table = 'promo_code_usage';
    protected $primaryKey = 'usage_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'customer_id',
        'promo_id',
        'order_id',
        'discount_amount'
    ];
    
    protected $guarded = [
        'usage_id',
        'used_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'usage_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'customer_id' => 'int(11) NOT NULL',
        'promo_id' => 'int(11) NOT NULL',
        'order_id' => 'int(11) DEFAULT NULL',
        'discount_amount' => 'decimal(10,2) NOT NULL',
        'used_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'customer' => 'belongsTo:Customer:customer_id',
        'promo_code' => 'belongsTo:PromoCode:promo_id',
        'order' => 'belongsTo:Order:order_id'
    ];
}

?> 