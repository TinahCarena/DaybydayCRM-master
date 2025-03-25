<?php

namespace App\DTOs;

use Carbon\Carbon;

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
     * @var string|null
     */
    public $address;

    /**
     * @var array|null
     */
    public $number;

    /**
     * @var string|null
     */
    public $image_path;

    /**
     * @var string|null
     */
    public $remember_token;

    /**
     * @var Carbon
     */
    public $created_at;

    /**
     * @var Carbon
     */
    public $updated_at;

}