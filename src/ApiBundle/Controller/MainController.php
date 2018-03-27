<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller{
    public function isSuperAdmin($request, $stockId){
        $token = $request->headers->get('X-Auth-Token');

        $user =  $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:AuthToken')
            ->findOneBy(array(
                "value" => $token
            ));


        $stockAccess =  $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:StockAccess')
            ->findOneBy(array(
                "user" => $user->getUser()->getId(),
                "stock" => $stockId
            ));

        if($stockAccess->getRole() === 0){
            return true;
        }

        return false;
    }
}
