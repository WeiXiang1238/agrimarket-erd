<?php

require_once 'BaseModel.php';

/**
 * Staff Model
 * Represents the staff table structure
 */
class Staff extends BaseModel
{
    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    protected $timestamps = true;
    protected $softDeletes = true;
    
    protected $fillable = [
        'user_id',
        'employee_id',
        'department',
        'position',
        'hire_date',
        'salary',
        'manager_id',
        'phone',
        'emergency_contact',
        'address',
        'performance_rating',
        'status'
    ];
    
    protected $guarded = [
        'staff_id',
        'created_at',
        'updated_at',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'staff_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) NOT NULL',
        'employee_id' => 'varchar(20) NOT NULL',
        'department' => 'varchar(100) NOT NULL',
        'position' => 'varchar(100) NOT NULL',
        'hire_date' => 'date NOT NULL',
        'salary' => 'decimal(10,2) DEFAULT NULL',
        'manager_id' => 'int(11) DEFAULT NULL',
        'phone' => 'varchar(20) DEFAULT NULL',
        'emergency_contact' => 'varchar(255) DEFAULT NULL',
        'address' => 'text DEFAULT NULL',
        'performance_rating' => 'decimal(3,2) DEFAULT NULL',
        'status' => 'enum("active","inactive","terminated") DEFAULT "active"',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id',
        'manager' => 'belongsTo:Staff:manager_id',
        'subordinates' => 'hasMany:Staff:manager_id',
        'tasks' => 'hasMany:Task:assigned_to'
    ];
}

?> 