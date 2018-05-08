<?php
namespace ApiBundle\Controller;

use ApiBundle\Entity\AuthToken;
use ApiBundle\Entity\Credentials;
use ApiBundle\Form\CredentialsType;
use ApiBundle\Security\AuthTokenAuthenticator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AuthTokenController extends AuthTokenAuthenticator
{
    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/auth-tokens")
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('ApiBundle:User')->findOneByEmail($credentials->getLogin());

        if (!$user) { // L'utilisateur n'existe pas
            return $this->invalidCredentials();
        }

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) { // Le mot de passe n'est pas correct
            return $this->invalidCredentials();
        }

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return $authToken;
    }

    private function invalidCredentials()
    {
        throw new NotFoundHttpException('Invalid credentials');
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"auth-token"})
     * @Rest\Get("/valid-auth-tokens")
     */
    public function getIsValidAuthTokenAction(Request $request)
    {
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/auth-tokens/{id}")
     */
    public function removeAuthTokenAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $authToken = $em->getRepository('ApiBundle:AuthToken')->find($request->get('id'));

        $connectedUser = $this->get('security.token_storage')->getToken()->getUser();

        if ($authToken && $authToken->getUser()->getId() === $connectedUser->getId()) {
            $em->remove($authToken);
            $em->flush();
        } else {
            throw new BadRequestHttpException();
        }
    }


}