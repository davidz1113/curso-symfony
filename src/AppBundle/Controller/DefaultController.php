<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Services\Helpers;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    //Login

    public function loginAction(Request $request)
    {
        $helpers = $this->get(Helpers::class);

        //recibir Json por Post
        $json = $request->get('json', null);
        //array para devolver por defecto
        $data = array(
            'status' => 'error',
            'data' => 'Send Json via post'
        );
        if ($json != null) {
                //pasamos al login
                //convertimos el json en un objeto de php
            $params = json_decode($json);

            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This email is not valid";
            $validate_email = $this->get("validator")->validate($email, $emailConstraint);

            if ($email != null && count($validate_email) == 0 && $password != null) {

                $data = array(
                    'status' => 'success',
                    'data' => 'Login success'
                );
            } else {
                $data = array(
                    'status' => 'error',
                    'data' => 'Email or Password Incorrect'
                );

            }

        }

        return $helpers->json($data);
    }
    //fin login

    public function pruebasAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('BackendBundle:User');
        $users = $userRepo->findAll();

        $helpers = $this->get(Helpers::class);
        return $helpers->json(array(
            'status' => 'success',
            'users' => $users[1]

        ));
        /*
        die();

        return new JsonResponse(array
            (
                'status' => 'success',
                'users' => $users[0]->getName()
            
            ));
         */
    }
}
