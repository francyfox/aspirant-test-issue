<?php declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

class BaseController
{
    public function __construct(
        protected RouteCollectorInterface $routeCollector,
        protected Environment $twig,
        protected EntityManagerInterface $em
    ) {
    }
}
