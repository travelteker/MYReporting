<?php

namespace App\ExtensionsViews;

use \Slim\Csrf\Guard;

class CsrfExtension extends \Twig_Extension
{
    protected $guard;


    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }


    public function getFunctions()
    {
        return [                      //function name --> usar underscore y se llama en la vista       //method class --> camelCase
            new \Twig_SimpleFunction('csrf_field', array($this, 'csrfField')),
        ];
    }

    /**
     * Inyectar el par de campos hidden para validación CSRF
     *
     * @return void
     */
    public function csrfField()
    {
        // CSRF token name y value
        $csrfNameKey = $this->guard->getTokenNameKey();
        $csrfName = $this->guard->getTokenName();

        $csrfValueKey = $this->guard->getTokenValueKey();
        $csrfValue = $this->guard->getTokenValue();
        
        return '
            <input type="hidden" name="'.$csrfNameKey.'" value="'.$csrfName.'">
            <input type="hidden" name="'.$csrfValueKey.'" value="'.$csrfValue.'">
        ';
    }

}
