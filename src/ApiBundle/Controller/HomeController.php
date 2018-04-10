<?php

namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HomeController extends MainController
{
    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"home"})
     * @Rest\Get("/home")
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
}
