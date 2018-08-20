<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\User;
use AppBundle\Services\Helpers;
use AppBundle\Services\JwtAuth;

class UserController extends Controller {

    public function newAction(Request $request){
        $helpers = $this->get(Helpers::class);

        $json = $request->get("json",null);
        $params = json_decode($json);

        $data = array(
            'status' => 'error',
            'code' => 400,
            'msg' => 'User not created !!'
        );
        if($json!=null){
            $createdAt = new \Datetime("now");
            $role ='user';

            $email = (isset($params->email))?$params->email:null;
            $name = (isset($params->name))?$params->name:null;
            $surname = (isset($params->surname))?$params->surname:null;
            $password = (isset($params->password))?$params->password:null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "this email is not valid";
            $validate_email = $this->get("validator")->validate($email,$emailConstraint);


        if($email!=null && count($validate_email)==0 && $password!=null && $name!=null && $surname !=null){
            $user = new User();
            $user->setCreatedAt($createdAt);
            $user->setRole($role);
            $user->setEmail($email);
            $user->setName($name);
            $user->setSurname($surname);


            //Cifrar la contraseña o password
            $pwd = hash('sha256',$password);
            $user->setPassword($pwd);

            $em = $this->getDoctrine()->getManager();
            $isset_user = $em->getRepository('BackendBundle:User')->findBy(array(
                "email"=>$email
            ));

            if(count($isset_user)==0){
                $em->persist($user);
                $em->flush();

                $data = array(
                    'status' => 'Success',
                    'code' => 200,
                    'msg' => 'User created !!',
                    'user'=> $user
                );
                
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => 400,
                    'msg' => 'User not created, Duplicated !!'
                );
            }
        }

        }
        return $helpers->json($data);
    }


    public function editAction(Request $request){
        $helpers = $this->get(Helpers::class);
        $jwt_auth = $this->get(JwtAuth::class);

        $token = $request->get('authorization',null);
        $authCheck = $jwt_auth->checkToken($token);

        if($authCheck){
            $em = $this->getDoctrine()->getManager(); //entiti manager

            //conseguir los datos del usuario identificado con el token
            $identity = $jwt_auth->checkToken($token,true);

            //conseguir el objeto a actualizar
            $user = $em->getRepository('BackendBundle:User')->findOneBy(array(
                'id' => $identity->sub
            ));

            //REcoger datos post
            $json = $request->get("json",null);
            $params = json_decode($json);

            //array error por defecto
            $data = array(
                'status' => 'error',
                'code' => 400,
                'msg' => 'User not update !!'
            );
            if($json!=null){
                //$createdAt = new \Datetime("now");
                $role ='user';
    
                $email = (isset($params->email))?$params->email:null;
                $name = (isset($params->name))?$params->name:null;
                $surname = (isset($params->surname))?$params->surname:null;
                $password = (isset($params->password))?$params->password:null;
    
                $emailConstraint = new Assert\Email();
                $emailConstraint->message = "this email is not valid";
                $validate_email = $this->get("validator")->validate($email,$emailConstraint);
    
    
            if($email!=null && count($validate_email)==0 && $name!=null && $surname !=null){

                //$user->setCreatedAt($createdAt);
                $user->setRole($role);
                $user->setEmail($email);
                $user->setName($name);
                $user->setSurname($surname);

                
                
                if($password!=null){
                    //Cifrar la contraseña o password
                        $pwd = hash('sha256',$password);
                        $user->setPassword($pwd);
                    
                }
    
                
                $isset_user = $em->getRepository('BackendBundle:User')->findBy(array(
                    "email"=>$email
                ));
    
                if(count($isset_user)==0 || $identity->email==$email ){
                    $em->persist($user);
                    $em->flush();
    
                    $data = array(
                        'status' => 'Success',
                        'code' => 200,
                        'msg' => 'User Update !!',
                        'user'=> $user
                    );
                    
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'msg' => 'User not update, Duplicated !!'
                    );
                }
            }
            
        }
    }else{
                    $data = array(
                        'status' => 'error',
                        'code' => 400,
                        'msg' => 'Authorization not valid !!'
                    );

        }

        
        return $helpers->json($data);
    }
}

?>