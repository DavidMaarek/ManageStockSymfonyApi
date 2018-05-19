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
        /*$stocksId = $this->giveMeUserStocks($request);

        if (empty($stocksId)) {
            throw new NotFoundHttpException('Vous n\'avez aucun stock');
        }

        $em = $this->getDoctrine()->getManager();

        $histories = [];

        foreach ($stocksId as $stockId){
            $products = $em->getRepository('ApiBundle:Product')->findByStock($stockId);

            foreach ($products as $product) {
                $histories[$stockId->getId()][] = $product->getHistory();
            }

        }

        if (empty($histories) || empty($products)) {
            throw new NotFoundHttpException('Aucun produit n\'a fait l\'objet d\'un retrait ou d\'un réapprovisionnement');
        }

        return $histories;*/

        $stocksId = $this->giveMeUserStocks($request);

        if (empty($stocksId)) {
            throw new NotFoundHttpException('Vous n\'avez aucun stock');
        }

        $em = $this->getDoctrine()->getManager();

        $stocksName = $em->getRepository('ApiBundle:Stock')->findById($stocksId);

        $stocks = [];
        foreach($stocksId as $stock) {
            $stocks[$stock->getId()] = $em->getRepository('ApiBundle:History')->findAllByStock($stock);
        }

        return [
            "histories" => $stocks,
            "stocksName" => $stocksName
        ];
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

        if($this->isUser($request, $stockId) && ($request->get('type') == 'add' || $request->get('type') == 'remove')){
            $history = new History();

            $form = $this->createForm(HistoryType::class, $history);

            $form->submit($request->request->all(), true);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $type = $request->get('type');
                $history->setType($type);

                $userId = $this->giveMeUserId($request);
                $user = $em->getRepository('ApiBundle:User')->findOneById($userId);
                $history->setUser($user);

                $em->persist($history);
                $em->flush();

                $this->historyUpdateProduct($productId, $request->get('quantity'), $type);
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
