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
use Twig\TwigFunction;

class HomeController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $favorites = [];
            $postData = [];
            if (isset($_SESSION['user'])) {
                $postData = $request->getParsedBody();
                $favorites = $this->getFavorites($_SESSION['user']['id']);
            }
            if ($postData) {
                if (!$this->isFavourite($favorites, intval($postData['t_id']))) {
                    $this->setLikes($postData);
                } else {
                    $this->disLike($postData);
                }
            }

            $function_is_favourite = new TwigFunction('isFavourite', function (array $favorites, int $trailer_id) {
                return $this->isFavourite($favorites, $trailer_id);
            });
            $this->twig->addFunction($function_is_favourite);
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

    protected function disLike($postData)
    {
        $user = $this->em->getRepository(User::class)->find(intval($postData['u_id']));
        $movie = $this->em->getRepository(Movie::class)->find(intval($postData['t_id']));
        $user->removeMyLike($movie);
        $movie->disLikes();
        $this->em->persist($user);
        $this->em->persist($movie);
        $this->em->flush();
    }

    protected function isFavourite(array $favorites, int $trailer_id): bool
    {
        $arr = array_filter($favorites, function ($k) use ($trailer_id) {
            return $k->getId() == $trailer_id;
        });
        array_splice($arr, 0, 0);
        if (sizeof($arr) > 0) {
            return $arr[0]->getId() == $trailer_id;
        } else {
            return false;
        }
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
