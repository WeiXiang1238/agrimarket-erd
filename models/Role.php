<?php

require_once 'BaseModel.php';

/**
 * Role Model
 * Represents the roles table structure
 */
class Role extends BaseModel
{
    protected $table = 'roles';
    protected $primaryKey = 'role_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'role_name',
        'description',
        'is_active'
    ];
    
    protected $guarded = [
        'role_id',
        'created_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'role_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'role_name' => 'varchar(50) NOT NULL',
        'description' => 'text DEFAULT NULL',
        'is_active' => 'tinyint(1) DEFAULT 1',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'permissions' => 'belongsToMany:Permission:role_permissions:role_id:permission_id',
        'users' => 'belongsToMany:User:user_roles:role_id:user_id'
    ];
} 