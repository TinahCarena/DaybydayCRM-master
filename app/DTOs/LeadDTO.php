<?php

namespace App\DTOs;

class LeadDTO
{
    /**
     * @var string
     */
    public $client_name;

    /**
     * @var string
     */
    public $lead_title;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $produit;

    /**
     * @var double
     */
    public $prix;

    /**
     * @var int
     */
    public $quantite;
}