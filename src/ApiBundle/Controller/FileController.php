<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\File;
use ApiBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;

class FileController extends Controller
{

    /**
     * @Rest\View(serializerGroups={"file"})
     * @Rest\Get("/files")
     */
    public function getFilesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $files = $em->getRepository('ApiBundle:File')->findAll();

        return $files;
    }

    /**
     * @Rest\View(serializerGroups={"file"})
     * @Rest\Get("/files/{id}")
     */
    public function getFileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository('ApiBundle:File')->find($request->get('id'));

        if (empty($file)) {
            return View::create(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        return $file;
    }
}