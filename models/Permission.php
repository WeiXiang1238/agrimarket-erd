<?php

require_once 'BaseModel.php';

/**
 * Permission Model
 * Represents the permissions table structure
 */
class Permission extends BaseModel
{
    protected $table = 'permissions';
    protected $primaryKey = 'permission_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'permission_name',
        'module',
        'description',
        'is_active'
    ];
    
    protected $guarded = [
        'permission_id',
        'created_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'permission_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'permission_name' => 'varchar(100) NOT NULL',
        'module' => 'varchar(50) NOT NULL',
        'description' => 'text DEFAULT NULL',
        'is_active' => 'tinyint(1) DEFAULT 1',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'roles' => 'belongsToMany:Role:role_permissions:permission_id:role_id'
    ];
} 