<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MainController extends Controller{

    // Retourne le user id du token de la requete
    public function giveMeUserId($request){
        $token = $request->headers->get('X-Auth-Token');

        $user =  $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:AuthToken')
            ->findOneBy(array(
                "value" => $token
            ));

        $userId = $user->getUser()->getId();

        return $userId;
    }

    // Retourne
    public function giveMeStockAccess($request, $stockId){
        $userId = $this->giveMeUserId($request);

        $stockAccess =  $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:StockAccess')
            ->findOneBy(array(
                "user" => $userId,
                "stock" => $stockId
            ));

        return $stockAccess;
    }

    // Retourne un tableau avec tous les ids des stocks dont l'utilisateur est contributeur
    public function giveMeUsersStocks($request){
        $token = $request->headers->get('X-Auth-Token');

        $user =  $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:AuthToken')
            ->findOneBy(array(
                "value" => $token
            ));

        $stocks =  $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:StockAccess')
            ->findBy(array(
                "user" => $user->getUser()->getId()
            ));

        $stocksId = [];

        foreach ($stocks as $stock){
            $stocksId[] = $stock->getId();
        }

        return $stocksId;
    }

    // Retourne l'id du stock du produit en question
    public function giveMeStockIdByProductId($productId){
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('ApiBundle:Product')->find($productId);

        if (empty($product)) {
            throw new NotFoundHttpException('Ce produit n\'existe pas');
        }

        $stockId = $product->getStock()->getId();

        return $stockId;
    }

    public function isSuperAdmin($request, $stockId){
        $stockAccess = $this->giveMeStockAccess($request, $stockId);

        if(empty($stockAccess)){
            return false;
        } elseif($stockAccess->getRole() === 0){
            return true;
        }

        return false;
    }

    public function isAdmin($request, $stockId){
        $stockAccess = $this->giveMeStockAccess($request, $stockId);

        if(empty($stockAccess)){
            return false;
        } elseif($stockAccess->getRole() === 0 || $stockAccess->getRole() === 1){
            return true;
        }

        return false;
    }

    public function isUser($request, $stockId){
        $stockAccess = $this->giveMeStockAccess($request, $stockId);

        if(empty($stockAccess)){
            return false;
        } elseif($stockAccess->getRole() === 0 || $stockAccess->getRole() === 1 || $stockAccess->getRole() === 2){
            return true;
        }

        return false;
    }

    public function isThisUser($request){
        $token = $request->headers->get('X-Auth-Token');

        $userToken =  $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:AuthToken')
            ->findOneBy(array(
                "value" => $token
            ));

        $userIdToken = $userToken->getUser()->getId();

        $userIdRequest = $request->get('id');

        if($userIdToken == $userIdRequest){
            return true;
        }

        return false;
    }

    public function historyCheckSameUser($request){
        $token = $request->headers->get('X-Auth-Token');

        $userToken =  $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:AuthToken')
            ->findOneBy(array(
                "value" => $token
            ));

        $userIdToken = $userToken->getUser()->getId();

        $userIdRequest = $request->get('user');

        if($userIdToken == $userIdRequest){
            return true;
        }

        return false;
    }

    public function historyUpdateProduct($productId, $quantity, $type){
        $em = $this->getDoctrine()->getManager();

        $product =  $em->getRepository('ApiBundle:Product')
            ->findOneBy(array(
                "id" => $productId
            ));

        $oldQuantity = $product->getQuantity();

        if($type == 'add'){
            $newQuantity = $oldQuantity + $quantity;
        } elseif ($type == 'remove') {
            $newQuantity = $oldQuantity - $quantity;
        }


        $product->setQuantity($newQuantity);
        $em->persist($product);
        $em->flush();
    }

}
