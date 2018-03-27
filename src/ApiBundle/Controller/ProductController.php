<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Product;
use ApiBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends MainController
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

        $stockId = $request->get('stock');


        if(isSuperAdmin($request, $stockId)){
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

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/products/{id}")
     */
    public function removeProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('ApiBundle:Product')
            ->find($request->get('id'));

        if ($product) {
            $em->remove($product);
            $em->flush();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View()
     * @Rest\Put("/products/{id}")
     */
    public function updateProductAction(Request $request)
    {
        return $this->updateProduct($request, true);
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View()
     * @Rest\Patch("/products/{id}")
     */
    public function patchProductAction(Request $request)
    {
        return $this->updateProduct($request, false);
    }

    private function updateProduct(Request $request, $clearMissing)
    {
        $product = $this->get('doctrine.orm.entity_manager')
            ->getRepository('ApiBundle:Product')
            ->find($request->get('id'));

        if (empty($product)) {
            throw new NotFoundHttpException('Product not found');
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');

            /*$picture1 = $this->container->get('app.file_uploader')->upload($product->getPicture1());
            $product->setPicture1($picture1);

            $picture2 = $this->container->get('app.file_uploader')->upload($product->getPicture2());
            $product->setPicture2($picture2);

            $picture3 = $this->container->get('app.file_uploader')->upload($product->getPicture3());
            $product->setPicture3($picture3);

            $picture4 = $this->container->get('app.file_uploader')->upload($product->getPicture4());
            $product->setPicture4($picture4);

            $picture5 = $this->container->get('app.file_uploader')->upload($product->getPicture5());
            $product->setPicture5($picture5);*/

            $em->persist($product);
            $em->flush();
            return $product;
        } else {
            return $form;
        }
    }

}
