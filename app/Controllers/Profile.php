<?php namespace App\Controllers;

use Exception;
use GoogleAuthenticator\Exception\GoogleAuthenticatorException;

use vendor\autoload;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use GoogleAuthenticator\GoogleAuthenticator;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Profile extends ResourceController
{
    use ResponseTrait;

    public function updateprofile() {
        $model = new UserModel();
        $usrname = $this->request->getVar('username');
    }

    public function enableTOTP() {
        $ga = new GoogleAuthenticator();

        $model = new UserModel();
        $isActivated = $this->request->getVar('isactivated');
        $id = $this->request->getVar('id');
        $findUser = $model->where('id', $id)->first();
        if($findUser) {
            $fullname = $findUser['firstname'] . ' ' . $findUser['lastname'];
            $mail = $findUser['email'];
            $secret = $findUser['secretkey'];
            $qrCodeUrl = $ga->getQRCodeGoogleUrl($fullname, $secret, $mail);

            $newqrcodefile = "public/qrcodes/00" . $id . ".png";
            $urlqrcodefile = "http://localhost:8080/qrcodes/" . "00" . $id . ".png";
            $writer = new PngWriter();
            $company ="WORLD BANK";
            $qrCode1 = "otpauth://totp/" . $company . ":" . $fullname . "?secret=". $secret . "&issuer=" . $company;
            $qrCode = QrCode::create($qrCode1)
            ->setSize(200)
            ->setMargin(10);
            
            $writer->write($qrCode)->saveToFile(ROOTPATH . $newqrcodefile);

            if($isActivated == 'Y') {
                $model->update($id, ['qrcodeurl' => $urlqrcodefile]);
                return $this->respondCreated(['statuscode' => 201,'message' => "2-Factor Authenticator is enabled.."]);
            } else {
                $model->update($id, ['qrcodeurl' => null]);
                return $this->respondCreated(['statuscode' => 201,'message' => "2-Factor Authenticator is disbled."]);
            }    
        }
    }

    public function validateTOTP() {
        $model = new UserModel();

        $ga = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
        $id = $this->request->getVar('id');        
        $findUser = $model->where('id', $id)->first();
        $secret = $findUser['secretkey'];

        $otp = $this->request->getVar('otpcode');
        $ga->getCode($secret);
        try {
            if($ga->checkCode($secret, $otp)) {
                return $this->respondCreated(['statuscode' => 201, 'message' => 'OTP Code accepted.']);
            } else {
                return $this->respondCreated(['statuscode' => 404, 'message' => 'Invalid OTP Code.']);
            }
        } catch(Exception $ex) {
            return $this->respondCreated(['statuscode' => 400,'message' => $ex->getMessage()]);
        }
    }
}