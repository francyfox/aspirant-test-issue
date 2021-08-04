<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;

class RegistrationController extends BaseController
{
    public function index(ResponseInterface $response)
    {
        $response->getBody()->write($this->twig->render('404.html.twig'));
    }
}