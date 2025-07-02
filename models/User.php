<?php

require_once 'BaseModel.php';

/**
 * User Model
 * Represents the users table structure
 */
class User extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $timestamps = true;
    protected $softDeletes = true;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'profile_picture',
        'is_active',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at'
    ];
    
    protected $guarded = [
        'user_id',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'user_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'name' => 'varchar(100) NOT NULL',
        'email' => 'varchar(100) NOT NULL',
        'phone' => 'varchar(20) DEFAULT NULL',
        'password' => 'varchar(255) NOT NULL',
        'role' => 'enum("admin","vendor","customer","staff") NOT NULL',
        'profile_picture' => 'varchar(255) DEFAULT NULL',
        'is_active' => 'tinyint(1) DEFAULT 1',
        'email_verified_at' => 'timestamp NULL DEFAULT NULL',
        'phone_verified_at' => 'timestamp NULL DEFAULT NULL',
        'last_login_at' => 'timestamp NULL DEFAULT NULL',
        'remember_token' => 'varchar(100) DEFAULT NULL',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0'
    ];
    
    // Relationships
    protected $relationships = [
        'roles' => 'hasMany:UserRole:user_id',
        'customer' => 'hasOne:Customer:user_id',
        'vendor' => 'hasOne:Vendor:user_id',
        'staff' => 'hasOne:Staff:user_id',
        'notifications' => 'hasMany:Notification:user_id',
        'audit_logs' => 'hasMany:AuditLog:user_id'
    ];
}

?> 