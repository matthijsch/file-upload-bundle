<?php

namespace ConnectHolland\FileUploadBundle\EventListener;

use FileUploadBundle\Model\UploadObjectInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadListener implements EventSubscriber
{
    /**
     * Target directory.
     */
    private $fileUploadPath;

    /**
     * The files scheduled for deletion.
     *
     * @var array
     */
    private $filesScheduledForDeletion = [];

    /*
     * Constructor.
     *
     * @param String $fileUploadPath
     */
    public function __construct($fileUploadPath)
    {
        $this->fileUploadPath = $fileUploadPath;
    }

   /**
    * {@inheritdoc}
    */
   public function getSubscribedEvents()
   {
       return [
           Events::preFlush,
           Events::prePersist,
           Events::postPersist,
           Events::postUpdate,
           Events::postFlush,
       ];
   }

    /**
     * Prepares upload file references for all objects implementing the UploadObjectInterface.
     *
     * @param PreFlushEventArgs $args
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $objectManager = $args->getEntityManager();
        $unitOfWork = $objectManager->getUnitOfWork();
        $entityMap = $unitOfWork->getIdentityMap();
        foreach ($entityMap as $objectClass => $objects) {
            if (in_array(UploadObjectInterface::class, class_implements($objectClass))) {
                foreach ($objects as $object) {
                    $this->prepareUploadFileReferences($object);
                }
            }
        }
    }

    /**
     * Prepares upload file references for a new object implementing the UploadObjectInterface.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof UploadObjectInterface) {
            $objectManager = $args->getEntityManager();
            $this->prepareUploadFileReferences($object, $objectManager);
        }
    }

    /**
     * Stores the file uploads.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof UploadObjectInterface) {
            $objectManager = $args->getEntityManager();
            $this->storeFileUploads($object, $objectManager);
        }
    }

    /**
     * Stores the file uploads.
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postPersist($args);
    }

    /**
     * Removes files scheduled for deletion.
     */
    public function postFlush()
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($this->filesScheduledForDeletion);
    }

    /**
     * Sets the new file references to the uploaded files on the object and schedules the previous file reference for deletion.
     *
     * @param UploadObjectInterface $object
     */
    private function prepareUploadFileReferences(UploadObjectInterface $object, $objectManager)
    {
        $object->setFileUploadPath($this->fileUploadPath);
        $reflectionClass = new ReflectionClass($object);
        $objectName = $reflectionClass->getShortName();
        $fileUploads = $object->getFileUploads();

        foreach ($fileUploads as $fieldName => $fileUpload) {
            $camelizedFieldName = Container::camelize($fieldName);
            $fileFieldProperty = lcfirst($camelizedFieldName);

            $getter = 'get'.$camelizedFieldName;
            $setter = 'set'.$camelizedFieldName;
            $previousFileReference = $object->$getter();
            if (empty($previousFileReference) === false) {
                $this->filesScheduledForDeletion[] = sprintf('%s/%s', $this->getFilePath($objectName, $fieldName), $previousFileReference);
            }
            $fileName = md5(uniqid()).'.'.$fileUpload->guessExtension();
            $object->$setter($fileName);
        }
    }

    /**
     * Stores the uploaded files to the specified file system location.
     *
     * @param UploadObjectInterface $object
     */
    private function storeFileUploads(UploadObjectInterface $object, $objectManager)
    {
        $reflectionClass = new ReflectionClass($object);
        $objectName = $reflectionClass->getShortName();
        $fileUploads = $object->getFileUploads();

        foreach ($fileUploads as $fieldName => $fileUpload) {
            $camelizedFieldName = Container::camelize($fieldName);
            $fileFieldProperty = lcfirst($camelizedFieldName);

            $getter = 'get'.$camelizedFieldName;
            $setter = 'set'.$camelizedFieldName.'Upload';
            $fileUpload->move($this->getFilePath($objectName, $fieldName), $object->$getter());
            $object->$setter(null);
        }
    }
    /**
     * Returns the file path for a field name of an object.
     *
     * @param string $objectName
     * @param string $fieldName
     *
     * @return string
     */
    private function getFilePath($objectName, $fieldName)
    {
        return sprintf(
            '%s/%s/%s',
            $this->fileUploadPath,
            strtolower($objectName),
            $fieldName
        );
    }
}
