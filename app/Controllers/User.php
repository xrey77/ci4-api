<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class User extends ResourceController
{
    use ResponseTrait;

    // get all users
    public function index(){
      $model = new UserModel();
      $data['users'] = $model->orderBy('id', 'DESC')->findAll();
      return $this->respond($data);
    }

    // create
    public function create() {
        $model = new UserModel();

        $data = [
            'lastname' => $this->request->getVar('lastname'),
            'firstname' => $this->request->getVar('firstname'),
            'email'  => $this->request->getVar('email'),
            'mobile' => $this->request->getVar('mobile'),
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
        ];
        $model->insert($data);
        return $this->respondCreated(['statuscode'   => 201,'messages' => 'User created successfully']);
    }

    // get user by id
    public function show($id = null) {
            $model = new UserModel();
            $data = $model->where('id', $id)->first();
            if($data){
                return $this->respond($data);
            } else {
                return $this->respondCreated(['statuscode' => 404,'messages' => 'User ID not found.']);
            }
    }

    // update
    public function update($id = null){
        $model = new UserModel();
        $id = $this->request->getVar('id');
        $data = [
            'lastname' => $this->request->getVar('lastname'),
            'firstname' => $this->request->getVar('firstname'),
            'mobile' => $this->request->getVar('mobile'),
        ];
        $model->update($id, $data);
        return $this->respond(['statuscode' => 200,'messages' => 'User updated successfully']);
    }

    // delete
    public function delete($id = null){
        $model = new UserModel();
        $data = $model->where('id', $id)->delete($id);
        if($data){
            $model->delete($id);
            return $this->respondDeleted(['statuscode' => 200,'messages' => 'User successfully deleted']);
        }else{
            return $this->respondDeleted(['statuscode' => 404,'messages' => 'User ID not found.']);
        }
    }
}

