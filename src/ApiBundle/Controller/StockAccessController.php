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
    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"access"})
     * @Rest\Get("/accesses/{id}")
     */
    public function getAccessAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $stockAccess = $em->getRepository('ApiBundle:StockAccess')->findOneById($request->get('id'));

        $stockId = $stockAccess->getStock();

        if($this->isSuperAdmin($request, $stockId)){
            return $stockAccess;
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour afficher les access de ce stock');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/accesses")
     */
    public function postStockAccessAction(Request $request)
    {
        $stockId = $request->get('stock');

        if($this->isSuperAdmin($request, $stockId)){
            $stockAccess = new StockAccess();

            $form = $this->createForm(AccessType::class, $stockAccess);

            $form->submit($request->request->all(), true);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($stockAccess);
                $em->flush();
            } else {
                return $form;
            }
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour ajouter un utilisateur au stock');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\Put("/accesses/stocks/{stock_id}/users/{user_id}")
     */
    public function updateStockAccessAction(Request $request)
    {
        return $this->updateStockAccess($request, true);
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\Patch("/accesses/stocks/{stock_id}/users/{user_id}")
     */
    public function patchStockAccessAction(Request $request)
    {
        return $this->updateStockAccess($request, false);
    }

    private function updateStockAccess(Request $request, $clearMissing)
    {
        $stockId = $request->get('stock_id');
        $userId = $request->get('user_id');

        if($this->isSuperAdmin($request, $stockId)){
            $stockAccess = $this->get('doctrine.orm.entity_manager')
                ->getRepository('ApiBundle:StockAccess')
                ->findOneBy(array(
                    "user" => $userId,
                    "stock" => $stockId
                ));

            if (empty($stockAccess)) {
                throw new NotFoundHttpException('Cet access n\'existe pas');
            }

            $form = $this->createForm(AccessType::class, $stockAccess);

            $form->submit($request->request->all(), $clearMissing);

            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($stockAccess);
                $em->flush();
            } else {
                return $form;
            }
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour modifier les access d\'un utilisateur dans ce stock');
        }
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
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour supprimer un utilisateur de ce stock');
        }
    }
}
