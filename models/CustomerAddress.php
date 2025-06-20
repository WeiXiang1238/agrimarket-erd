<?php

require_once 'BaseModel.php';

/**
 * CustomerAddress Model
 * Represents the customer_addresses table structure
 */
class CustomerAddress extends BaseModel
{
    protected $table = 'customer_addresses';
    protected $primaryKey = 'address_id';
    protected $timestamps = true;
    protected $softDeletes = false;
    
    protected $fillable = [
        'customer_id',
        'address_type',
        'first_name',
        'last_name',
        'company',
        'street_address',
        'street_address_2',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'is_default'
    ];
    
    protected $guarded = [
        'address_id',
        'created_at',
        'updated_at'
    ];
    
    // Table columns definition
    protected $columns = [
        'address_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'customer_id' => 'int(11) NOT NULL',
        'address_type' => 'enum("shipping","billing","both") DEFAULT "shipping"',
        'first_name' => 'varchar(50) NOT NULL',
        'last_name' => 'varchar(50) NOT NULL',
        'company' => 'varchar(100) DEFAULT NULL',
        'street_address' => 'varchar(255) NOT NULL',
        'street_address_2' => 'varchar(255) DEFAULT NULL',
        'city' => 'varchar(100) NOT NULL',
        'state' => 'varchar(100) NOT NULL',
        'postal_code' => 'varchar(20) NOT NULL',
        'country' => 'varchar(100) DEFAULT "Malaysia"',
        'phone' => 'varchar(20) DEFAULT NULL',
        'is_default' => 'tinyint(1) DEFAULT 0',
        'created_at' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        'updated_at' => 'timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()'
    ];
    
    // Relationships
    protected $relationships = [
        'customer' => 'belongsTo:Customer:customer_id'
    ];
} 