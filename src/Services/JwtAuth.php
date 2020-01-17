<?php
namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;

class JwtAuth{
    public $manager;
    public $key;

    public function __construct($manager){
        $this->manager = $manager;
        $this->key = 'esta_key_solo_esta_presente_en_el_backend_1245220553009';
    }
    public function signup($email, $password, $gettoken = null){
        // Comprobar si el usuario existe
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email,
            'password' => $password
        ]);

        $signup = false;
        if(is_object($user)){
            $signup = true;
        }

        // Si existe, generar el token de jwt
        if($signup){

            $token =[
                'sub' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'email' => $user->getEmail(),
                'iat' => time(),
                'exp' => time()+ (7*24*60*60)

            ];

            // Comprobar el flag gettoken, condición
            $jwt = JWT::encode($token, $this->key, 'HS256');
            if(!empty($gettoken)){
                $data = $jwt;
            }else{
                $decode = JWT::decode($jwt, $this->key, ['HS256']);
                $data = $decode;
            }
        }else{
            $data = [
                'status' => 'error',
                'message' => 'Login incorrecto'
            ];
        }

        

        // Devolver respuesta.
        return $data ;
    }

    public function checkToken($jwt){
        $auth = false;
        try{
            $decode = JWT::decode($jwt, $this->key, ['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }
       

        if(isset($decode) && !empty($decode) && is_object($decode) && isset($decode->sub)){
            $auth = true;
        }else{
            $auth = false;
        }

        return $auth;
    }
}