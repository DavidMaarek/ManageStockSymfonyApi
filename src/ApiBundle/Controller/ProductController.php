<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Product;
use ApiBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{

    /**
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Get("/products")
    */
    public function getProductsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('ApiBundle:Product')->findAll();

        return $products;
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Get("/products/{id}")
     */
    public function getProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('ApiBundle:Product')->find($request->get('id'));

        if (empty($product)) {
            throw new NotFoundHttpException('Product not found');
        }

        return $product;
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"product"})
     * @Rest\Post("/products")
     */
    public function postProductAction(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->submit($request->request->all(), true);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $picture1 = $this->container->get('app.file_uploader')->upload($product->getPicture1());
            $product->setPicture1($picture1);

            $picture2 = $this->container->get('app.file_uploader')->upload($product->getPicture2());
            $product->setPicture2($picture2);

            $picture3 = $this->container->get('app.file_uploader')->upload($product->getPicture3());
            $product->setPicture3($picture3);

            $picture4 = $this->container->get('app.file_uploader')->upload($product->getPicture4());
            $product->setPicture4($picture4);

            $picture5 = $this->container->get('app.file_uploader')->upload($product->getPicture5());
            $product->setPicture5($picture5);

            $em->persist($product);
            $em->flush();

            return $product;
        } else {

            return $form;
        }
    }
}
