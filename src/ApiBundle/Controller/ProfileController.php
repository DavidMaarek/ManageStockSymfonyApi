<?php

namespace ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

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
        $user = $em->getRepository('ApiBundle:User')->findOneById($userId);


        // Recuperer les stocks dont il est super admin
        $stocksId = $this->giveMeUserStocksSuperAdmin($request);
        $stocks = $em->getRepository('ApiBundle:Stock')->findById($stocksId);

        return [
            "user" => $user,
            "stocks" => $stocks
        ];
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"profileStock"})
     * @Rest\Get("/profile/stocks/{id}")
     */
    public function getProfileStockAction(Request $request)
    {
        $stockId = $request->get('id');

        if($this->isSuperAdmin($request, $stockId)){
            $em = $this->getDoctrine()->getManager();
            $stock = $em->getRepository('ApiBundle:Stock')->find($request->get('id'));

            if (empty($stock)) {
                throw new NotFoundHttpException('Ce stock n\'exxiste pas');
            }

            return $stock;
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour afficher ce stock et ses access');
        }
    }
}
