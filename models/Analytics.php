<?php

require_once 'BaseModel.php';

/**
 * Analytics Model
 * Represents the search_logs table structure for analytics
 */
class Analytics extends BaseModel
{
    protected $table = 'search_logs';
    protected $primaryKey = 'log_id';
    protected $timestamps = true;
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
        'search_duration'
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
        'search_duration' => 'int(11) DEFAULT NULL',
        'search_date' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id',
        'clicked_product' => 'belongsTo:Product:clicked_product_id'
    ];
}

?> 