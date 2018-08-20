<?php
namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth{
    
    public $manager;
    public $key;

    public function __construct($manager){
        $this->manager = $manager;
        $this->key = "ClaveSecreta7878878787878961454*";
    }

    public function singup($email,$password,$getHash=null){
        
        $user= $this->manager->getRepository('BackendBundle:User')->findOneBy(array(
            "email" => $email,
            "password" => $password
        ));
        $singnup = false;

        if(is_object($user)){
            $singnup = true;
        }


        if($singnup==true){
            //Generar un token jwt

            $token = array(
                "sub" => $user->getId(),
                "email" =>$user->getEmail(),
                "name" =>$user->getName(),
                "surname" => $user->getSurname(),
                "iat"=> time(),
                "exp" => time()+(7*24*60*60)//fecha de caducidad para el token, una semana                                     despues de crearse
            );

            $jwt = JWT::encode($token,$this->key,'HS256');
            $decode = JWT::decode($jwt,$this->key,array('HS256'));
            if($getHash ==null){
                $data = $jwt;
            }else{
                $data = $decode;
            }


           //
            }else{
                
                $data = array(
                    'status'=>'error',
                    'data'=>'Login failed'
                    );
        
            }
        return $data;
    }


    public function checkToken($jwt,$getIdentity=false){
        $auth = false;

        try{
            $decode = JWT::decode($jwt,$this->key,array('HS256'));
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }


        if(isset($decode) && is_object($decode) && isset($decode->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        if($getIdentity==false){
            return $auth;
        }else{
            return $decode;
        }
    }
}


?>