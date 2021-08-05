<?php

namespace App\Controller;

use App\Entity\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class RegistrationController extends BaseController
{
    /**
     * @throws HttpBadRequestException
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $request->getParsedBody();
            $log = $this->postData($data);
            $data = $this->twig->render('user/reg.html.twig', ['log' => $log]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);
        return $response;
    }

    public function postData($data)
    {
        if ($data && !$this->checkForm($data)) {
            $user = new User();
            $user
                ->setUsername($data['username'])
                ->setPassword(password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]))
            ;
            $this->em->persist($user);
            $this->em->flush();

            return 'New User created';
        }
        if (gettype($this->checkForm($data)) === 'string') {
            return $this->checkForm($data);
        }
    }

    public function checkForm($data): bool | string
    {
        if ($data['username'] && $data['password'] && $data['repeat_password']) {
            $findUser = $this->em->getRepository(User::class)->findOneBy(['username' => $data['username']]);
            if ($data['password'] !== $data['repeat_password']) {
                return 'Password does not match';
            } elseif ($findUser !== null) {
                return 'User already exists. Use another username';
            }
        }

        return false;
    }
}
