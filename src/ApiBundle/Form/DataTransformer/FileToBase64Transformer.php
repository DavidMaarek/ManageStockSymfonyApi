<?php
namespace ApiBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileToBase64Transformer implements DataTransformerInterface
{
    public function transform($value){}

    public function reverseTransform($value)
    {
        if($value === null){return;}

        if (false === $tmpFilePath = tempnam($directory = sys_get_temp_dir(), 'TmpBase64EncodedFile')) {
            throw new FileException(sprintf('Unable to create a file into the "%s" directory', $directory));
        }

        $tmp = fopen($tmpFilePath, 'wb+');
        $matches = [];
        preg_match('/^data:([\w-]+\/[\w-]+);base64,(.+)$/', $value, $matches);
        if (false === $size = fwrite($tmp, base64_decode($matches[2]))) {
            throw new FileException(sprintf('Unable to create a file into the "%s" directory', $directory));
        };
        fclose($tmp);

        return new UploadedFile($tmpFilePath, '', $matches[1], $size, 0, true);
    }
}