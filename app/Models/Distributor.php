<?php

namespace App\Models;

class Distributor
{
   public $distributor_id;
   public $num_children;
   public $username;
   public $full_name;
   public $status;
   public $product_name;
   public $category_name;
   public $parent_id;
   public $binary_placement;
   public $children;
   public $parent_username;

   public function __construct($jsonData)
   {
     (isset($jsonData['distributor_id']) ? $this->distributor_id = $jsonData['distributor_id'] : null);
     (isset($jsonData['num_children']) ? $this->num_children = $jsonData['num_children'] : null);
     (isset($jsonData['username']) ? $this->username = $jsonData['username'] : null);
     (isset($jsonData['full_name']) ? $this->full_name = $jsonData['full_name'] : null);
     (isset($jsonData['status']) ? $this->status = $jsonData['status'] : null);
     (isset($jsonData['product_name']) ? $this->product_name = $jsonData['product_name'] : null);
     (isset($jsonData['category_name']) ? $this->category_name = $jsonData['category_name'] : null);
     (isset($jsonData['parent_id']) ? $this->parent_id = $jsonData['parent_id'] : null);
     (isset($jsonData['binary_placement']) ? $this->binary_placement = $jsonData['binary_placement'] : null);
     (isset($jsonData['parent_username']) ? $this->parent_username = $jsonData['parent_username'] : null);

     (isset($jsonData['children']) ? 
                (isset($jsonData['children'][1])
                ?$this->children=[new Distributor($jsonData['children'][0]),new Distributor($jsonData['children'][1])]
                :$this->children=[new Distributor($jsonData['children'][0])])
            : null);
   }

public function getDistributorId()   {      return $this->distributor_id;   }    

public function getNumChildren()     {      return $this->num_children;   }

public function getUsername()        {      return $this->username;   }

public function getFullName()        {      return $this->full_name;   }

public function getStatus()          {      return $this->status;   }

public function getProductName()     {      return $this->product_name;   }

public function getCategoryName()    {      return $this->category_name;   }

public function getParentId()        {      return $this->parent_id;   }

public function getBinaryPlacement() {      return $this->binary_placement;   }

public function getChildren()        {      return $this->children;   }

public function getParentUsername()  {      return $this->parent_username;   }

}
