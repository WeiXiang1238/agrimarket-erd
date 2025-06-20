<?php

require_once 'BaseModel.php';

/**
 * ProductCategory Model
 * Represents the product_categories table structure
 */
class ProductCategory extends BaseModel
{
    protected $table = 'product_categories';
    protected $primaryKey = 'category_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_category_id',
        'image_path',
        'sort_order',
        'is_active'
    ];
    
    protected $guarded = [
        'category_id',
        'created_at',
        'updated_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'category_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'name' => 'varchar(100) NOT NULL',
        'slug' => 'varchar(100) NOT NULL',
        'description' => 'text DEFAULT NULL',
        'parent_category_id' => 'int(11) DEFAULT NULL',
        'image_path' => 'varchar(255) DEFAULT NULL',
        'sort_order' => 'int(11) DEFAULT 0',
        'is_active' => 'tinyint(1) DEFAULT 1',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'parent_category' => 'belongsTo:ProductCategory:parent_category_id',
        'subcategories' => 'hasMany:ProductCategory:parent_category_id',
        'products' => 'hasMany:Product:category_id',
        'preferences' => 'hasMany:CustomerPreference:category_id'
    ];
} 