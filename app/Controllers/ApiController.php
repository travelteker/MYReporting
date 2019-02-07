<?php

namespace App\Controllers;

use Ramsey\Uuid\Uuid;
use Tuupola\Base62Proxy as Base62;
use \Firebase\JWT\JWT;


class ApiController extends Controller
{
    const DURATION_TOKEN = 60;
    const FORMAT_DURATION = 'minutes';
    const MIN_LEN_SECRET = 10;


    public function getToken($request, $response, $args)
    {
        $myrtkn = $this->container->get('myrtkn');
        $attribute = 'token'; //Value default library 'tuupola/slim-jwt-auth'
        if(isset($myrtkn['attribute']))
        {
            $attribute = $myrtkn['attribute'];
        }
        
        $secret = null;
        if(isset($myrtkn['secret']))
        {
            $secret = $myrtkn['secret'];
        }
        if($this->checkSecretApi($secret) === false)
        {
            $data['status'] = 'KO';
            $data['description'] = 'Error de Sistema, por favor contacte con el departamento de soporte.';
            $data['code'] = 'B0a1p_xA11';
            return $response->withStatus(201)
                            ->withHeader("Content-Type", "application/json")
                            ->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        }


        $ipClient = $this->getClientIp();

        if(isset($args['code']))
        {
            $code = $args['code'];
        }
        
        //TODO --> Compara la IP con una BLACKLIST o WHITELIST para ver si bloqueamos la peticion o continuamos el proceso

        $now = new \DateTime();
        $future = new \DateTime("+".self::DURATION_TOKEN." ".self::FORMAT_DURATION);  //"+60 minutes"

        //Identificador unico para el JWT
        $jti = (new Base62)->encode(random_bytes(128));

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => $jti,
            "sub" => ['client' => $ipClient, 'code' => $code, 'attribute' => $attribute],
            "scope" => ['read']          //["read", "write", "delete"] //TODO --> usarlo para los permisos de rutas -->asignar en función del usuario que solicita el token
        ];

        $token = JWT::encode($payload, $secret, "HS256");
        $data["token"] = $token;
        $data["expires"] = $future->getTimeStamp();
        return $response->withStatus(201)
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode($data, JSON_UNESCAPED_SLASHES));
    }


    /**
     * Chequear validez del atributo SECRET_API
     *
     * @return void
     */
    private function checkSecretApi($secret)
    {
        $check = true;
        if(isset($secret) === false)
        {
            $check = false;
        }
        else
        {
            if(strlen($secret) < self::MIN_LEN_SECRET)
            {
                $check = false;
            }
        }
        
        return $check;
    }


    /**
     * Obtener la ip remota del cliente que hace la solicitud
     *
     * @return string
     */
    private function getClientIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
    
        return $ipaddress;
    }
}
    