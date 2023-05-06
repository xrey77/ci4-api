<?php 
namespace App\Models;
use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['descriptions','qty','unit','cost_price',
    'sell_price','prod_pic','category','sale_price','alert_level',
    'critical_level','datecreated','dateupdated'];
}



