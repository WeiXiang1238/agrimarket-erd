<?php

require_once 'BaseModel.php';

/**
 * StaffTask Model
 * Represents the staff_tasks table structure
 */
class StaffTask extends BaseModel
{
    protected $table = 'staff_tasks';
    protected $primaryKey = 'task_id';
    protected $timestamps = false;
    protected $softDeletes = false;
    
    protected $fillable = [
        'staff_id',
        'task_title',
        'task_description',
        'priority',
        'status',
        'due_date'
    ];
    
    protected $guarded = [
        'task_id',
        'assigned_date',
        'completed_date'
    ];
    
    // Table columns definition
    protected $columns = [
        'task_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'staff_id' => 'int(11) NOT NULL',
        'task_title' => 'varchar(255) NOT NULL',
        'task_description' => 'text DEFAULT NULL',
        'priority' => 'enum("low","medium","high") DEFAULT "medium"',
        'status' => 'enum("pending","in_progress","completed","cancelled") DEFAULT "pending"',
        'due_date' => 'date DEFAULT NULL',
        'assigned_date' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'completed_date' => 'timestamp NULL DEFAULT NULL'
    ];
    
    // Relationships
    protected $relationships = [
        'staff' => 'belongsTo:Staff:staff_id'
    ];
}

?> 