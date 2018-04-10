<?php

namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfileController extends MainController
{
    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"profile"})
     * @Rest\Get("/profile")
     */
    public function getProfileAction(Request $request)
    {

        // Recupere les informations du user du token
        $userId = $this->giveMeUserId($request);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ApiBundle:User')->findById($userId);


        // Recuperer les stocks dont il est super admin
        $stocksId = $this->giveMeUserStocksSuperAdmin($request);
        $stocks = $em->getRepository('ApiBundle:Stock')->findById($stocksId);

        return [
            "user" => $user,
            "stocks" => $stocks
        ];
    }
}
