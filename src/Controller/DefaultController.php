<?php

namespace App\Controller;

use Core\Kernel\AbstractController;

/**
 *
 */
class DefaultController extends BaseController
{
    public function index()
    {
        $message = 'Bienvenue sur le framework MVC';
        //$this->dump($message);
        $this->render('app.default.frontpage',array(
            'message' => $message,
        ));
    }

    /**
     * Ne pas enlever
     */
    public function Page404()
    {
        $this->render('app.default.404');
    }
}
