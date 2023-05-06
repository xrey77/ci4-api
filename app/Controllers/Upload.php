<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;

class Upload extends ResourceController
{
    use ResponseTrait;

    public function userimage(){
     try {
        $model = new UserModel();
        $id = $this->request->getVar('id');
        // error_log("test................" . $id,0);
        $filename= $_FILES["file"]["name"];
        $file_ext = "." . pathinfo($filename,PATHINFO_EXTENSION);
        
        $newfilename = "00" . $id . $file_ext;
        if($img = $this->request->getFile('file')) {
            if ($img->isValid() && ! $img->hasMoved()) {
                $image = \Config\Services::image();

                $image->withFile($img)
                ->fit(100, 100, 'center')
                ->save(ROOTPATH . '/public/users/' . $newfilename);                
                
                $urlimg = "http://localhost:8080/users/" . $newfilename;

                $data = [
                    'picture' => $urlimg
                ];
                $model->update($id, $data);
                return $this->respondCreated(['statuscode' => 201,'message' => "You picture has been changed."]);
                // $img->move(ROOTPATH . 'public/users', $image);
            }       
        }
    } catch(\Exception $ex) {
        return $this->respondCreated(['statuscode' => 400,'message' => $ex->getMessage()]);  
    }   
    }

}

