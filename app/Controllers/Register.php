<?php namespace App\Controllers;

use vendor\autoload;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use GoogleAuthenticator\GoogleAuthenticator;

class Register extends ResourceController
{
    use ResponseTrait;

public function registration() {

        $ga = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

        $secret = $ga->generateSecret();

        $model = new UserModel();
        $usrname = $this->request->getVar('username');
        $mail = $this->request->getVar('email');

        $findemail = $model->where('email', $mail)->first();
        if($findemail) 
        {
            return $this->respondCreated(['status' => 201,'messages' => 'Email Address is taken.']);
        }

        $findusername = $model->where('username', $usrname)->first();
        if($findusername) 
        {
            return $this->respondCreated(['status' => 201,'messages' => 'Username is taken.']);
        }

        $data = [
            'lastname' => $this->request->getVar('lastname'),
            'firstname' => $this->request->getVar('firstname'),
            'email'  => $this->request->getVar('email'),
            'mobile' => $this->request->getVar('mobile'),
            'username' => $this->request->getVar('username'),
            'secretkey' => $secret,
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
        ];
        $model->insert($data);
        return $this->respondCreated(['status'   => 201,'messages' => 'User created successfully']);
    }

}