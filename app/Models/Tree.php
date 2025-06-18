<?php

namespace App\Models;

use App\Models\Distributor;

class Tree
{
    public $root;

    public function __construct($jsonData = null)
    {
        if ($jsonData) {
            $root = $this->parseData($jsonData['data']['attributes'][0]);
            $this->root = $root;
        }
    }

    function parseData($jsonData)
    {
         return new Distributor($jsonData);

    }
    
    public function getRoot()
    {
        return $this->root;
    }
}
