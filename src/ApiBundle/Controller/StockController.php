<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Stock;
use ApiBundle\Form\StockType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class StockController extends MainController
{
    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"stock"})
     * @Rest\Get("/stocks")
    */
    public function getStocksAction(Request $request)
    {
        $stocksId = $this->giveMeUsersStocks($request);

        if (empty($stocksId)) {
            throw new NotFoundHttpException('Vous n\'avez aucun stock');
        }

        $em = $this->getDoctrine()->getManager();

        $stocks = $em->getRepository('ApiBundle:Stock')->findById($stocksId);

        return $stocks;
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"stock"})
     * @Rest\Get("/stocks/{id}")
     */
    public function getStockAction(Request $request)
    {
        $stockId = $request->get('id');

        if($this->isUser($request, $stockId)){
            $em = $this->getDoctrine()->getManager();
            $stock = $em->getRepository('ApiBundle:Stock')->find($request->get('id'));

            if (empty($stock)) {
                throw new NotFoundHttpException('Stock not found');
            }

            return $stock;
        }

        throw new BadCredentialsException('Vous n\'avez les droits');
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"stock"})
     * @Rest\Post("/stocks")
     */
    public function postStockAction(Request $request)
    {
        $stock = new Stock();

        $form = $this->createForm(StockType::class, $stock);

        $form->submit($request->request->all(), true);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($stock);
            $em->flush();

            return $stock;
        } else {

            return $form;
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/stocks/{id}")
     */
    public function removeStockAction(Request $request)
    {

        $stockId = $request->get('id');

        if($this->isSuperAdmin($request, $stockId)){
            $em = $this->getDoctrine()->getManager();
            $stock = $em->getRepository('ApiBundle:Stock')->find($request->get('id'));

            if ($stock) {
                $em->remove($stock);
                $em->flush();
            }
        }

        throw new BadCredentialsException('Vous n\'avez les droits sur ce stock pour le supprimer');
    }


    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"stock"})
     * @Rest\Put("/stocks/{id}")
     */
    public function updateStockAction(Request $request)
    {
        return $this->updateStock($request, true);
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"stock"})
     * @Rest\Patch("/stocks/{id}")
     */
    public function patchStockAction(Request $request)
    {
        return $this->updateStock($request, false);
    }

    private function updateStock(Request $request, $clearMissing)
    {
        $stockId = $request->get('id');

        if($this->isSuperAdmin($request, $stockId)){
            $stock = $this->get('doctrine.orm.entity_manager')
                ->getRepository('ApiBundle:Stock')
                ->find($request->get('id'));

            if (empty($stock)) {
                throw new NotFoundHttpException('Stock not found');
            }

            $form = $this->createForm(StockType::class, $stock);

            $form->submit($request->request->all(), $clearMissing);

            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($stock);
                $em->flush();
                return $stock;
            } else {
                return $form;
            }
        }

        throw new BadCredentialsException('Vous n\'avez les droits sur ce stock pour le modifier');
    }

}
