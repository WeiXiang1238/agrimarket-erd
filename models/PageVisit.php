<?php

require_once 'BaseModel.php';

/**
 * PageVisit Model
 * Represents the page_visits table structure
 */
class PageVisit extends BaseModel
{
    protected $table = 'page_visits';
    protected $primaryKey = 'visit_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'user_id',
        'session_id',
        'page_url',
        'page_title',
        'referrer_url',
        'user_agent',
        'ip_address',
        'country',
        'device_type',
        'browser',
        'visit_duration'
    ];
    
    protected $guarded = [
        'visit_id',
        'visit_date'
    ];
    
    // Table columns definition
    protected $columns = [
        'visit_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) DEFAULT NULL',
        'session_id' => 'varchar(100) DEFAULT NULL',
        'page_url' => 'varchar(500) NOT NULL',
        'page_title' => 'varchar(255) DEFAULT NULL',
        'referrer_url' => 'varchar(500) DEFAULT NULL',
        'user_agent' => 'text DEFAULT NULL',
        'ip_address' => 'varchar(45) DEFAULT NULL',
        'country' => 'varchar(100) DEFAULT NULL',
        'device_type' => 'varchar(50) DEFAULT NULL',
        'browser' => 'varchar(100) DEFAULT NULL',
        'visit_duration' => 'int(11) DEFAULT NULL',
        'visit_date' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id'
    ];
} 