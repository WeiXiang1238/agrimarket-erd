<?php

require_once 'BaseModel.php';

/**
 * AuditLog Model
 * Represents the audit_logs table structure
 */
class AuditLog extends BaseModel
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'log_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];
    
    protected $guarded = [
        'log_id',
        'created_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'log_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) DEFAULT NULL',
        'action' => 'varchar(100) NOT NULL',
        'table_name' => 'varchar(100) NOT NULL',
        'record_id' => 'int(11) NOT NULL',
        'old_values' => 'json DEFAULT NULL',
        'new_values' => 'json DEFAULT NULL',
        'ip_address' => 'varchar(45) DEFAULT NULL',
        'user_agent' => 'text DEFAULT NULL',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id'
    ];
} 