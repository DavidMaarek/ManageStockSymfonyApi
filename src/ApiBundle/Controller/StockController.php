<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Stock;
use ApiBundle\Form\StockType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StockController extends Controller{
    /**
     * @Rest\View(serializerGroups={"stock"})
     * @Rest\Get("/stocks")
    */
    public function getStocksAction()
    {
        $em = $this->getDoctrine()->getManager();
        $stocks = $em->getRepository('ApiBundle:Stock')->findAll();

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
        $em = $this->getDoctrine()->getManager();
        $stock = $em->getRepository('ApiBundle:Stock')->find($request->get('id'));

        if (empty($stock)) {
            throw new NotFoundHttpException('Stock not found');
        }

        return $stock;
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
}
