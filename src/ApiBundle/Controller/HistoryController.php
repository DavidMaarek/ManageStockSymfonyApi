<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\History;
use ApiBundle\Form\HistoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


class HistoryController extends MainController
{
    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"histories"})
     * @Rest\Get("/histories")
     */
    public function getHistoriesAction(Request $request)
    {
        $stocksId = $this->giveMeUsersStocks($request);

        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('ApiBundle:Product')->findByStock($stocksId);

        $histories = [];

        foreach ($products as $product){
            $histories = $product->getHistory();
        }

        return $histories;
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"history"})
     * @Rest\Post("/histories/{type}")
     */
    public function postHistoryAction(Request $request)
    {
        $productId = $request->get('product');
        $stockId = $this->giveMeStockIdByProductId($productId);

        if($this->historyCheckSameUser($request) && $this->isUser($request, $stockId) && ($request->get('type') == 'add' || $request->get('type') == 'remove')){
            $history = new History();

            $form = $this->createForm(HistoryType::class, $history);

            $form->submit($request->request->all(), true);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $type = $request->get('type');
                $history->setType($type);

                $em->persist($history);
                $em->flush();

                $this->historyUpdateProduct($productId, $request->get('quantity'), $type);

                return $history;
            } else {
                return $form;
            }
        } else {
            if($request->get('type') == 'add') {
                throw new BadCredentialsException('Vous n\'avez les droits pour reapprovisionner un produit');
            } elseif($request->get('type') == 'remove') {
                throw new BadCredentialsException('Vous n\'avez les droits pour retirer un produit');
            }
        }
    }
}
