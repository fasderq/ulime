<?php
namespace Ulime\Backoffice\Section\Repository;


use MongoDB\Client;

class SectionRepository
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getSections()
    {
        return $this->client->selectCollection('mydb', 'mycol')->find()->toArray();
    }

    
}