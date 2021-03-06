<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Product;
use ApiBundle\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ProductController extends MainController
{
    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Get("/products/{id}")
     */
    public function getProductAction(Request $request)
    {
        $productId = $request->get('id');
        $stockId = $this->giveMeStockIdByProductId($productId);

        if ($this->isUser($request, $stockId)){
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('ApiBundle:Product')->find($request->get('id'));

            if (empty($product)) {
                throw new NotFoundHttpException('Ce produit n\'existe pas');
            }

            return $product;
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour acceder à ce produit');
        }
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

        if($this->isAdmin($request, $stockId)){
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
            } else {

                return $form;
            }
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour ajouter un produit à ce stock');
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
        $productId = $request->get('id');
        $stockId = $this->giveMeStockIdByProductId($productId);

        if ($this->isSuperAdmin($request, $stockId)){
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('ApiBundle:Product')
                ->find($request->get('id'));

            if (empty($product)) {
                throw new NotFoundHttpException('Ce produit n\'existe pas');
            } elseif ($product) {
                $em->remove($product);
                $em->flush();
            }
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour supprimer un produit de ce stock');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(serializerGroups={"product"})
     * @Rest\Put("/products/{id}")
     */
    public function updateProductAction(Request $request)
    {
        return $this->updateProduct($request, true);
    }

    /**
     * @param Request $request
     * @return mixed
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Patch("/products/{id}")
     */
    public function patchProductAction(Request $request)
    {
        return $this->updateProduct($request, false);
    }

    private function updateProduct(Request $request, $clearMissing)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('ApiBundle:Product')->findOneById($request->get('id'));

        if (empty($product)) {
            throw new NotFoundHttpException('Ce produit n\'existe pas');
        }

        $stockId = $product->getStock();

        if($this->isAdmin($request, $stockId)){
            $form = $this->createForm(ProductType::class, $product);

            $form->submit($request->request->all(), $clearMissing);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

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
            } else {
                return $form;
            }
        } else {
            throw new BadCredentialsException('Vous n\'avez les droits pour modifier un produit dans ce stock');
        }
    }
}
