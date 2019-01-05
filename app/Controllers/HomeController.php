<?php

namespace App\Controllers;


use App\Models\User;
use Slim\Views\Twig as View;


class HomeController extends Controller
{

    public function index($request, $response)
    {
        //Uso de flashMessage
        //$this->flash->addMessage('info', 'Test flash message');
        
        //return $this->container->view->render($response, 'home.twig');
        //Eliminamos 'container' en la invocacion $this ya que la clase Controller usa el metodo mágico __get($property)
        return $this->view->render($response, 'home.twig');
    }

}