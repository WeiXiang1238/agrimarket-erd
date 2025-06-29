<?php

require_once 'BaseModel.php';

/**
 * SearchLog Model
 * Represents the search_logs table structure
 */
class SearchLog extends BaseModel
{
    protected $table = 'search_logs';
    protected $primaryKey = 'log_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'user_id',
        'keyword',
        'filters',
        'results_count',
        'ip_address',
        'user_agent',
        'session_id',
        'clicked_product_id',
        'clicked_vendor_id',
        'search_duration',
        'click_position',
        'clicked_at'
    ];
    
    protected $guarded = [
        'log_id',
        'search_date'
    ];
    
    // Table columns definition
    protected $columns = [
        'log_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) DEFAULT NULL',
        'keyword' => 'varchar(255) NOT NULL',
        'filters' => 'text DEFAULT NULL',
        'results_count' => 'int(11) DEFAULT 0',
        'ip_address' => 'varchar(45) DEFAULT NULL',
        'user_agent' => 'text DEFAULT NULL',
        'session_id' => 'varchar(100) DEFAULT NULL',
        'clicked_product_id' => 'int(11) DEFAULT NULL',
        'clicked_vendor_id' => 'int(11) DEFAULT NULL',
        'search_duration' => 'int(11) DEFAULT NULL',
        'click_position' => 'int(11) DEFAULT NULL',
        'clicked_at' => 'timestamp NULL DEFAULT NULL',
        'search_date' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id',
        'clicked_product' => 'belongsTo:Product:clicked_product_id',
        'clicked_vendor' => 'belongsTo:Vendor:clicked_vendor_id'
    ];
} 