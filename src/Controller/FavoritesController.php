<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class FavoritesController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, $id): ResponseInterface
    {
        try {
            $data = $this->twig->render('user/favorites.html.twig', [
                'favorites' => $this->fetchData($id),
                'user' => $_SESSION['user'],
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    protected function fetchData($id): array
    {
        return $this->em->getRepository(User::class)->find($id)->getMyLikes()->getValues();
    }
}
