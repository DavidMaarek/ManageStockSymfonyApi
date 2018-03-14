<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\User;
use ApiBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

class UserController extends Controller
{

    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("/users")
     */
    public function getProductsAction()
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
    public function getProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ApiBundle:User')->find($request->get('id'));

        if (empty($user)) {
            return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/users")
     */
    public function postProductAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all(), true);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return $user;
        } else {

            return $form;
        }
    }
}
