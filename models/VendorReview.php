<?php

require_once 'BaseModel.php';

/**
 * VendorReview Model
 * Represents the vendor_reviews table structure
 */
class VendorReview extends BaseModel
{
    protected $table = 'vendor_reviews';
    protected $primaryKey = 'vendor_review_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'vendor_id',
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
        'approved_at'
    ];
    
    protected $guarded = [
        'vendor_review_id',
        'created_at',
        'updated_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'vendor_review_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'vendor_id' => 'int(11) NOT NULL',
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
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'vendor' => 'belongsTo:Vendor:vendor_id',
        'customer' => 'belongsTo:Customer:customer_id',
        'order' => 'belongsTo:Order:order_id',
        'approver' => 'belongsTo:User:approved_by'
    ];
} 