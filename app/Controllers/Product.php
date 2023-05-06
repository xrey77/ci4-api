<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Product extends ResourceController
{
    use ResponseTrait;

    // get all products
    public function show($page = null){
        // $page = $this->request->getVar('page');
        // error_log("PAGE : " . $page,0);

        $db = \Config\Database::connect();

        $queryrows = $db->query('SELECT * FROM products order by id');
        $totalrecs = $queryrows->getNumRows();
        $perpage = 10;
        $totalpage = ceil(floatval($totalrecs) / $perpage);
        $offset = ($page - 1) * $perpage;
    
        $builder = $db->table('products');
        $query   = $builder->get($perpage, $offset);
        $data = $query->getResult();

        return $this->respond(['page' => $page,'totpages' => $totalpage, 'products' => $data]);
    }
}