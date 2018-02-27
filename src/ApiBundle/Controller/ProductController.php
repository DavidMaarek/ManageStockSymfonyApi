<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Product;
use ApiBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

class ProductController extends Controller
{

    /**
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Get("/products")
    */
    public function getProductsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('ApiBundle:Product')->findAll();

        return $products;
    }

    /**
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Get("/products/{id}")
     */
    public function getPlaceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository('ApiBundle:Product')->find($request->get('id'));

        if (empty($place)) {
            return View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        return $place;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"product"})
     * @Rest\Post("/products")
     */
    public function postPlacesAction(Request $request)
    {

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->submit($request->request->all()); // Validation des données

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $product;
        } else {
            return $form;
        }
    }
}
