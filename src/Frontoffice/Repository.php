<?php
namespace Ulime\Frontoffice;

use MongoDB\Client;

class Repository
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function find()
    {
        return $this->client
            ->selectCollection('mydb', 'mycol')
            ->findOne();
    }
}