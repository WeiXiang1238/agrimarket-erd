<?php

require_once 'BaseModel.php';

/**
 * Review Model
 * Represents the reviews table structure
 */
class Review extends BaseModel
{
    protected $table = 'reviews';
    protected $primaryKey = 'review_id';
    protected $timestamps = true;
    protected $softDeletes = true;
    
    protected $fillable = [
        'product_id',
        'customer_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'pros',
        'cons',
        'is_verified_purchase',
        'is_approved',
        'approved_by',
        'approved_at',
        'helpful_count'
    ];
    
    protected $guarded = [
        'review_id',
        'created_at',
        'updated_at',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'review_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'product_id' => 'int(11) NOT NULL',
        'customer_id' => 'int(11) NOT NULL',
        'order_id' => 'int(11) DEFAULT NULL',
        'rating' => 'int(11) NOT NULL CHECK (rating BETWEEN 1 AND 5)',
        'title' => 'varchar(255) DEFAULT NULL',
        'comment' => 'text DEFAULT NULL',
        'pros' => 'text DEFAULT NULL',
        'cons' => 'text DEFAULT NULL',
        'is_verified_purchase' => 'tinyint(1) DEFAULT 0',
        'is_approved' => 'tinyint(1) DEFAULT 0',
        'approved_by' => 'int(11) DEFAULT NULL',
        'approved_at' => 'timestamp NULL DEFAULT NULL',
        'helpful_count' => 'int(11) DEFAULT 0',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0'
    ];
    
    // Relationships
    protected $relationships = [
        'product' => 'belongsTo:Product:product_id',
        'customer' => 'belongsTo:Customer:customer_id',
        'order' => 'belongsTo:Order:order_id',
        'approver' => 'belongsTo:User:approved_by'
    ];
}

?> 