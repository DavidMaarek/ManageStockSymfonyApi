<?php
namespace ApiBundle\Controller;

use ApiBundle\Entity\AuthToken;
use ApiBundle\Entity\Credentials;
use ApiBundle\Form\CredentialsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

class AuthTokenController extends Controller
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

        $em = $this->get('doctrine.orm.entity_manager');

        $user = $em->getRepository('ApiBundle:User')
            ->findOneByEmail($credentials->getLogin());

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
        return View::create(['message' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
    }
}