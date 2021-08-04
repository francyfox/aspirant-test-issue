<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class MovieController extends BaseController
{
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
        } else {
            $response->getBody()->write($this->twig->render('404.html.twig'));
        }

        return $response;
    }
}
