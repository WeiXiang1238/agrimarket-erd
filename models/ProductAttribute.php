<?php

require_once 'BaseModel.php';

/**
 * ProductAttribute Model
 * Represents the product_attributes table structure
 */
class ProductAttribute extends BaseModel
{
    protected $table = 'product_attributes';
    protected $primaryKey = 'attribute_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'product_id',
        'attribute_name',
        'attribute_value',
        'attribute_type',
        'sort_order'
    ];
    
    protected $guarded = [
        'attribute_id',
        'created_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'attribute_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'product_id' => 'int(11) NOT NULL',
        'attribute_name' => 'varchar(100) NOT NULL',
        'attribute_value' => 'text NOT NULL',
        'attribute_type' => 'enum("text","number","date","boolean","select") DEFAULT "text"',
        'sort_order' => 'int(11) DEFAULT 0',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'product' => 'belongsTo:Product:product_id'
    ];
} 