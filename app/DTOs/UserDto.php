<?php

namespace App\DTOs;

class UserDto
{
    /**
     * @var int
     */
    public $id;
    
    /**
     * @var string
     */
    public $external_id;
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var string
     */
    public $email;
    
    /**
     * @var string
     */
    public $password;
    
    /**
     * @var string
     */
    public $address;
    
    /**
     * @var string|null
     */
    public $primary_number;
    
    /**
     * @var string|null
     */
    public $secondary_number;
    
    /**
     * @var string
     */
    public $image_path;
    
    /**
     * @var string|null
     */
    public $remember_token;
    
    /**
     * @var \Carbon\Carbon
     */
    public $created_at;
    
    /**
     * @var \Carbon\Carbon
     */
    public $updated_at;
}

