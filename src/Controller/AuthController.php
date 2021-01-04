<?php
namespace App\Controller;

// src/Controller/LuckyController.php
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

// ...

class AuthController extends AbstractController
{
    /**
     * @Route("/lucky/number")
     */
    public function number(): Response
    {
        $number = random_int(0, 100);
        
        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }


    /**
     *Login a user.
     *
     * @Route(
     *     "login",
     *     methods={"POST"}
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \HttpRequestException
     */
    public function login(Request $request)
    {
      
        $entityManager = $this->getDoctrine()->getManager();
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $email = $request->get('email');
        $password = $request->get('password');

        if (null === $email) {
            throw new \HttpRequestException();
        }

        if (null === $password) {
            throw new \HttpRequestException();
        }

        $user = $userRepository->findOneBy(['email' => $email]);
        
        $now = new \DateTime();

        return new JsonResponse(
            [
                'token' => $user->getApiToken(), // token a revoir 
                'expiresIn' => $now
            ]
        );
    }

    /**
     *Login a user.
     *
     * @Route(
     *     "token",
     *     methods={"POST"}
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \HttpRequestException
     */
    public function token(Request $request)
    {
      
        $entityManager = $this->getDoctrine()->getManager();
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $token = $request->get('token');

        if (null === $token) {
            throw new \HttpRequestException();
        }
        $user = $userRepository->findOneBy(['apiToken' => $token]);

        return new JsonResponse(
            [
                'email' => $user->getEmail(), // token a revoir 
            ]
        );
    }

    /**
     *logout a user.
     *
     * @Route(
     *     "deconexion",
     *     methods={"POST"}
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \HttpRequestException
     */
    public function deconexion(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $token = $request->get('token');

        if (null === $token) {
            throw new \HttpRequestException();
        }
        $user = $userRepository->findOneBy(['apiToken' => $token]);
        // il manque la gestion d'erreur si il ne trouve pas l'utilisateur
        $entityManager->remove($user);
        $entityManager->flush();

        
        return new JsonResponse(
            [
                'logout' =>' utilisateur avec cette email '. $user->getEmail().' a etais deconnecter', // token a revoir 
            ]
        );
    }

}
