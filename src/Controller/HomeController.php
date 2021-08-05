<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\ORMException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class HomeController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $postData = $request->getParsedBody();
            if ($postData['u_id']) {
                $this->setLikes($postData);
            }
            $favorites = [];
            if (isset($_SESSION['user'])) {
                $favorites = $this->getFavorites($_SESSION['user']['id']);
            }

            $data = $this->twig->render('home/index.html.twig', [
                'user' => $_SESSION['user'],
                'trailers' => $this->fetchData(),
                'favorites' => $favorites,
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    /**
     * @throws ORMException
     */
    protected function setLikes($postData)
    {
        $user = $this->em->getRepository(User::class)->find(intval($postData['u_id']));
        $movie = $this->em->getRepository(Movie::class)->find(intval($postData['t_id']));
        $movie->setLikes();
        $user->addMyLike($movie);
        $this->em->persist($user);
        $this->em->persist($movie);
        $this->em->flush();
    }

    protected function fetchData(): Collection
    {
        $data = $this->em->getRepository(Movie::class)
            ->findAll();

        return new ArrayCollection($data);
    }

    protected function getFavorites($id): array
    {
        return $this->em->getRepository(User::class)->find($id)->getMyLikes()->getValues();
    }
}
