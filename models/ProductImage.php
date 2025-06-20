<?php

require_once 'BaseModel.php';

/**
 * ProductImage Model
 * Represents the product_images table structure
 */
class ProductImage extends BaseModel
{
    protected $table = 'product_images';
    protected $primaryKey = 'image_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'product_id',
        'image_path',
        'image_name',
        'image_size',
        'image_type',
        'is_primary',
        'alt_text',
        'sort_order'
    ];
    
    protected $guarded = [
        'image_id',
        'uploaded_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'image_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'product_id' => 'int(11) NOT NULL',
        'image_path' => 'varchar(255) NOT NULL',
        'image_name' => 'varchar(255) DEFAULT NULL',
        'image_size' => 'int(11) DEFAULT NULL',
        'image_type' => 'varchar(50) DEFAULT NULL',
        'is_primary' => 'tinyint(1) DEFAULT 0',
        'alt_text' => 'varchar(255) DEFAULT NULL',
        'sort_order' => 'int(11) DEFAULT 0',
        'uploaded_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'product' => 'belongsTo:Product:product_id'
    ];
} 