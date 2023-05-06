<?php 
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['lastname','firstname','email','mobile',
    'username','password','picture','secretkey','qrcodeurl'];
}



