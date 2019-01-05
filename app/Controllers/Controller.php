<?php

namespace App\Controllers;

class Controller
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    //Para poder invocar las propiedades de '$this->container' desde las clases hijas
    public function __get($property)
    {
        if($this->container->{$property})
        {
            return $this->container->{$property};
        }
    }
}