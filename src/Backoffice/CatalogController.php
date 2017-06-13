<?php
namespace Ulime\Backoffice;



use Symfony\Component\HttpFoundation\Response;

class CatalogController
{
    public function test()
    {
        echo 'test';

        return new Response();
    }
}