<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use App\Entity\User;
use App\Entity\Video;
use App\Services\JwtAuth;



class VideoController extends AbstractController
{
    
    private function resjson($data){
        // Serializar datos con servicio de serializer
        $json = $this->get('serializer')->serialize($data,'json');

        //  REsponse con httpfoundation
        $response = new Response();

        // Asignar contenido a la respuesta
        $response->setContent($json);

        // Indicar formato a la respuesta
        $response->headers->set('Content-Type', 'application/json');

        // DEvolver la respuesta
        return $response;
    }

    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VideoController.php',
        ]);
    }

    public function create(Request $request, JwtAuth $jwt_auth){

        // Respuesta por defecto
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'El video no ha podido crearse'
        ];

        // Recoger el token
        $token = $request->headers->get('Authorization', null);

        // Comprobar si es correcto
        
        $authCheck = $jwt_auth->checkToken($token);
        
        
        if($authCheck){

            // Recoger datos por post
            $json = $request->get('json', null);
            $params = json_decode($json);

            // Recoger el objeto del usuario identificado
            $identity = $authCheck = $jwt_auth->checkToken($token, true);

            // Comprobar y validar datos

            if(!empty($json)){

                $user_id = ($identity->sub != null) ? $identity->sub : null;
                $title  = (!empty($params->title)) ? $params->title : null;
                $description  = (!empty($params->description)) ? $params->description : null;
                $url  = (!empty($params->url)) ? $params->url : null;

                if(!empty($user_id) && !empty($title)){
                    // Guardar el nuevo video en la bd

                    $em = $this->getDoctrine()->getManager();
                    $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
                        'id' => $user_id
                    ]);

                    // Crear y guardar objeto
                    $video = new Video();
                    $video->setUser($user);
                    $video->setTitle($title);
                    $video->setDescription($description);
                    $video->setUrl($url);
                    $video->setStatus('ver luego');

                    $createdAt = new \Datetime('now');
                    $updatedAt = new \Datetime('now');

                    $video->setCreatedAt($createdAt);
                    $video->setUpdatedAt($updatedAt);

                    // Guardar bd
                    $em->persist($video);
                    $em->flush();

                    // Respuesta 
                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El video se ha guardado correctamente.',
                        'video' => $video
                    ];

                }   
 
            }
            

            
        }
        

       
        // Devolver una respuesta
        return $this->resjson($data);
    }
}
