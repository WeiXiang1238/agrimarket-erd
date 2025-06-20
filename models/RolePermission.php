<?php

require_once 'BaseModel.php';

/**
 * RolePermission Model
 * Represents the role_permissions table structure (pivot table)
 */
class RolePermission extends BaseModel
{
    protected $table = 'role_permissions';
    protected $primaryKey = 'role_permission_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'role_id',
        'permission_id'
    ];
    
    protected $guarded = [
        'role_permission_id',
        'granted_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'role_permission_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'role_id' => 'int(11) NOT NULL',
        'permission_id' => 'int(11) NOT NULL',
        'granted_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'role' => 'belongsTo:Role:role_id',
        'permission' => 'belongsTo:Permission:permission_id'
    ];
} 