<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

class MovieController
{
    public function __construct(
        private RouteCollectorInterface $routeCollector,
        private Environment $twig,
        private EntityManagerInterface $em
    ) {
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface
    {
        $movie = $this->em->getRepository(Movie::class)
            ->find($id);
        if (!empty($movie)) {
            try {
                $data = $this->twig->render('movie/index.html.twig', [
                    'trailer' => $movie,
                ]);
            } catch (\Exception $e) {
                throw new HttpBadRequestException($request, $e->getMessage(), $e);
            }

            $response->getBody()->write($data);

            return $response;
        } else {
            $response->getBody()->write($this->twig->render('404.html.twig', [
                'trailer' => $movie,
            ]));
            return $response;
        }
    }
}
