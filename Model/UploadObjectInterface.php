<?php

namespace ConnectHolland\FileUploadBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * UploadObjectInterface.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */

interface UploadObjectInterface
{
    /**
     * Returns the array with uploaded file instances.
     *
     * @return UploadedFile[]
     */

    public function getFileUploads();
    /**
     * Sets the base path for file uploads.
     *
     * @param string $path
     */
    public function setFileUploadPath($path);
}
