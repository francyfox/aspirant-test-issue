<?php

namespace App\Controller;

use App\Entity\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class AuthController extends BaseController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();
            $log = $this->authUser($data);
            if ($log === 'valid') {
                return $response->withStatus(302)->withHeader('Location', '/');
            }
            $data = $this->twig->render('user/auth.html.twig', [
                'log' => $log,
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }
        $response->getBody()->write($data);

        return $response;
    }

    public function authUser($data): string | bool
    {
        if ($data['username'] && $data['password']) {
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $data['username']]);
            if ($user !== null) {
                $isValid = password_verify($data['password'], $user->getPassword());
                if ($isValid) {
                    $_SESSION['user'] = [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'password' => $user->getPassword(),
                    ];
                    return 'valid';
                } else {
                    return 'Password incorrect';
                }
            } else {
                return 'Invalid username. User not found';
            }
        }
        return false;
    }
}
