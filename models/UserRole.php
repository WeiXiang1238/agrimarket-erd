<?php

require_once 'BaseModel.php';

/**
 * UserRole Model
 * Represents the user_roles table structure (pivot table)
 */
class UserRole extends BaseModel
{
    protected $table = 'user_roles';
    protected $primaryKey = 'user_role_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'user_id',
        'role_id',
        'is_active'
    ];
    
    protected $guarded = [
        'user_role_id',
        'assigned_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'user_role_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) NOT NULL',
        'role_id' => 'int(11) NOT NULL',
        'assigned_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'is_active' => 'tinyint(1) DEFAULT 1'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id',
        'role' => 'belongsTo:Role:role_id'
    ];
} 