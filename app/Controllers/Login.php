<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

class Login extends ResourceController
{
    use ResponseTrait;

    public function loginauth() {
        $session = session();
        $userModel = new UserModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $data = $userModel->where('username', $username)->first();
        
        if($data){

            $key = getenv('JWT_SECRET');
            $iat = time();
            $exp = $iat + 28800000;  // 8hours
            $fullname = $data['firstname'] . ' ' . $data['lastname'];

            $payload = array(
                "iss" => "Bruce Lee",
                "aud" => "Philippine Taekwondo Association",
                "sub" => "1234567890",
                "iat" => $iat, //Time the JWT issued at
                "exp" => $exp, // Expiration time of token
                "email" => $data['email'],
            );
              
            $token = JWT::encode($payload, $key, 'HS256');
      
            $pass = $data['password'];
            $authenticatePassword = password_verify($password, $pass);
            if($authenticatePassword){
                $ses_data = [
                    'id' => $data['id'],
                    'lastname' => $data['lastname'],
                    'firstname' => $data['firstname'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'qrcodeurl' => $data['qrcodeurl'],
                    'isactivated' => $data['isactivated'],
                    'isblocked' => $data['isblocked'],
                    'token' => $token,
                    'isLoggedIn' => TRUE
                ];
                // $session->set($ses_data);
                $response = [
                    'statuscode'   => 201,
                    'message' => 'Login successfully',
                    'user' => $ses_data
                ];
                return $this->respondCreated($response);

            
            }else{
                // $session->setFlashdata('msg', 'Password is incorrect.');
                $response = [
                    'statuscode'   => 404,
                    'message' => 'Password is incorrect'
                ];
                return $this->respondCreated($response);

                // return redirect()->to('/signin');
            }
        }else{
            // $session->setFlashdata('msg', 'Email does not exist.');
            $response = [
                'statuscode'   => 404,
                'message' => 'Username does not exist.'                
            ];
            return $this->respondCreated($response);

            // return redirect()->to('/signin');
        }


    }

}