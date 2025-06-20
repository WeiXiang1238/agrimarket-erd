<?php

require_once 'BaseModel.php';

/**
 * Product Model
 * Represents the products table structure
 */
class Product extends BaseModel
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $timestamps = true;
    protected $softDeletes = true;
    
    protected $fillable = [
        'vendor_id',
        'category_id',
        'name',
        'description',
        'unit_price',
        'selling_price',
        'stock_quantity',
        'sku',
        'image_path',
        'weight',
        'dimensions',
        'expiry_date',
        'origin_location',
        'organic_certified',
        'quantity_per_unit',
        'minimum_order_quantity',
        'status',
        'views',
        'sales_count',
        'featured'
    ];
    
    protected $guarded = [
        'product_id',
        'created_at',
        'updated_at',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'product_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'vendor_id' => 'int(11) NOT NULL',
        'category_id' => 'int(11) DEFAULT NULL',
        'name' => 'varchar(255) NOT NULL',
        'description' => 'text DEFAULT NULL',
        'unit_price' => 'decimal(10,2) NOT NULL',
        'selling_price' => 'decimal(10,2) NOT NULL',
        'stock_quantity' => 'int(11) DEFAULT 0',
        'sku' => 'varchar(100) DEFAULT NULL',
        'image_path' => 'varchar(255) DEFAULT NULL',
        'weight' => 'decimal(8,2) DEFAULT NULL',
        'dimensions' => 'varchar(100) DEFAULT NULL',
        'expiry_date' => 'date DEFAULT NULL',
        'origin_location' => 'varchar(255) DEFAULT NULL',
        'organic_certified' => 'tinyint(1) DEFAULT 0',
        'quantity_per_unit' => 'varchar(50) DEFAULT NULL',
        'minimum_order_quantity' => 'int(11) DEFAULT 1',
        'status' => 'enum("active","inactive","out_of_stock") DEFAULT "active"',
        'views' => 'int(11) DEFAULT 0',
        'sales_count' => 'int(11) DEFAULT 0',
        'featured' => 'tinyint(1) DEFAULT 0',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0'
    ];
    
    // Relationships
    protected $relationships = [
        'vendor' => 'belongsTo:Vendor:vendor_id',
        'category' => 'belongsTo:ProductCategory:category_id',
        'reviews' => 'hasMany:Review:product_id',
        'order_items' => 'hasMany:OrderItem:product_id',
        'shopping_cart' => 'hasMany:ShoppingCart:product_id',
        'images' => 'hasMany:ProductImage:product_id',
        'attributes' => 'hasMany:ProductAttribute:product_id',
        'preferences' => 'hasMany:CustomerPreference:product_id'
    ];
}

?> 