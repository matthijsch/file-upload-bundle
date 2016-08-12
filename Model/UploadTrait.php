<?php

namespace ConnectHolland\FileUploadBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * UploadTrait.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
trait UploadTrait
{
    /**
     * The array with uploaded file instances.
     *
     * @var UploadedFile[]
     */
    private $fileUploads = array();

    /**
     * The base path for file uploads.
     *
     * @var string
     */
    private $fileUploadPath;

    /**
     * Returns the uploaded file.
     *
     * @return UploadedFile|null
     */
    public function getFileUpload()
    {
        $propertyName = $this->getFileUploadPropertyName();
        if (isset($this->fileUploads[$propertyName])) {
            return $this->fileUploads[$propertyName];
        }
    }

    /**
     * Returns the array with uploaded file instances.
     *
     * @return UploadedFile[]
     */
    public function getFileUploads()
    {
        return $this->fileUploads;
    }

    /**
     * Sets the uploaded file.
     *
     * @param UploadedFile|null $file
     */
    public function setFileUpload(UploadedFile $file = null)
    {
        $propertyName = $this->getFileUploadPropertyName();
        unset($this->fileUploads[$propertyName]);
        if ($file instanceof UploadedFile) {
            $this->fileUploads[$propertyName] = $file;
        }
    }

    /**
     * Sets the base directory for file uploads.
     *
     * @param string $directory
     */
    public function setFileUploadPath($directory)
    {
        $this->fileUploadPath = $directory;
    }

    /**
     * Returns file upload name based on the called method name.
     *
     * @return string
     */
    private function getFileUploadPropertyName()
    {
        return lcfirst(substr(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1]['function'], 3, -6));
    }
}