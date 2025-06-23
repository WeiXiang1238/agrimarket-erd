<?php

require_once 'BaseModel.php';

/**
 * Notification Model
 * Represents the notifications table structure
 */
class Notification extends BaseModel
{
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'related_id',
        'action_url',
        'is_read',
        'read_at',
        'priority',
        'expires_at'
    ];
    
    protected $guarded = [
        'notification_id',
        'created_at',
        'updated_at',
        'is_archive'
    ];
    
    // Table columns definition
    protected $columns = [
        'notification_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'user_id' => 'int(11) NOT NULL',
        'title' => 'varchar(255) NOT NULL',
        'message' => 'text NOT NULL',
        'type' => 'enum("order","product","system","promotion","reminder") NOT NULL',
        'related_id' => 'int(11) DEFAULT NULL',
        'action_url' => 'varchar(255) DEFAULT NULL',
        'is_read' => 'tinyint(1) DEFAULT 0',
        'read_at' => 'timestamp NULL DEFAULT NULL',
        'priority' => 'enum("low","medium","high","urgent") DEFAULT "medium"',
        'expires_at' => 'timestamp NULL DEFAULT NULL',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()',
        'is_archive' => 'tinyint(1) DEFAULT 0'
    ];
    
    // Relationships
    protected $relationships = [
        'user' => 'belongsTo:User:user_id'
    ];
}

?> 