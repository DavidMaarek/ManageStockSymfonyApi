<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\StockAccess;
use ApiBundle\Form\AccessType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class StockAccessController extends MainController
{

    // A supprimer
    /**
     * @Rest\View(serializerGroups={"access"})
     * @Rest\Get("/accesses")
     */
    public function getAccessesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $stockAccesses = $em->getRepository('ApiBundle:StockAccess')->findAll();

        return $stockAccesses;
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"access"})
     * @Rest\Get("/accesses/stocks/{id}")
     */
    public function getAccessesOnOneStockAction(Request $request)
    {
        $stockId = $request->get('id');

        if($this->isSuperAdmin($request, $stockId)){
            $em = $this->getDoctrine()->getManager();
            $stockAccesses = $em->getRepository('ApiBundle:StockAccess')->findBy(['stock' => $stockId]);

            return $stockAccesses;
        }

        throw new BadCredentialsException('Vous n\'avez les droits pour afficher les access de ce stock');
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"access"})
     * @Rest\Post("/accesses/stocks/{id}")
     */
    public function postStockAccessAction(Request $request)
    {
        $stockId = $request->get('id');

        if($this->isSuperAdmin($request, $stockId)){
            $stockAccess = new StockAccess();

            $form = $this->createForm(AccessType::class, $stockAccess);

            $form->submit($request->request->all(), true);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($stockAccess);
                $em->flush();

                return $stockAccess;
            } else {

                return $form;
            }
        }

        throw new BadCredentialsException('Vous n\'avez les droits pour ajouter un utilisateur au stock');
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/accesses/stocks/{stock_id}/users/{user_id}")
     */
    public function removeStockAccessAction(Request $request)
    {
        $stockId = $request->get('stock_id');
        $userId = $request->get('user_id');

        if ($this->isSuperAdmin($request, $stockId)){
            $em = $this->getDoctrine()->getManager();
            $stockAccess = $em->getRepository('ApiBundle:StockAccess')
                ->findOneBy(array(
                    "user" => $userId,
                    "stock" => $stockId
                ));

            if (empty($stockAccess)) {
                throw new NotFoundHttpException('Cet access n\'existe pas');
            } elseif ($stockAccess) {
                $em->remove($stockAccess);
                $em->flush();
            }
        }

        throw new BadCredentialsException('Vous n\'avez les droits pour supprimer un utilisateur de ce stock');
    }
}
