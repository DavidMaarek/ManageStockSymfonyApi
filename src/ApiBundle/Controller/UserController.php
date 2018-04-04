<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\User;
use ApiBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class UserController extends MainController
{

    // A supprimer
    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/users")
     */
    public function getUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('ApiBundle:User')->findAll();

        return $users;
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/users/{id}")
     */
    public function getUserAction(Request $request)
    {
        if($this->isThisUser($request)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('ApiBundle:User')->find($request->get('id'));

            if (empty($user)) {
                throw new NotFoundHttpException('User not found');
            }

            return $user;
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour afficher cet utilisateur');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/users")
     */
    public function postUserAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all(), true);

        if ($form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return $user;
        } else {

            return $form;
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View()
     * @Rest\Put("/users/{id}")
     */
    public function updateUserAction(Request $request)
    {
        return $this->updateUser($request, true);
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View()
     * @Rest\Patch("/users/{id}")
     */
    public function patchUserAction(Request $request)
    {
        return $this->updateUser($request, false);
    }

    private function updateUser(Request $request, $clearMissing)
    {
        if($this->isThisUser($request)){
            $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('ApiBundle:User')
                ->find($request->get('id'));

            if (empty($user)) {
                throw new NotFoundHttpException('Cet utilisateur n\'existe pas');
            }

            $form = $this->createForm(UserType::class, $user);

            $form->submit($request->request->all(), $clearMissing);

            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($user);
                $em->flush();
                return $user;
            } else {
                return $form;
            }
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour modifier cet utilisateur');
        }
    }
}
