<?php
namespace FlintLabs\Bundle\DoctrineBehavioursBundle\EventListener;

use FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration,
    Doctrine\ORM\Event\LifecycleEventArgs,
    Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Timestampable behaviour for Doctrine2
 * @throws \FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration\BehaviourConfigurationException
 * @author Cam Manderson (cameronmanderson@gmail.com)
 */
class TimestampableListener
{
    /**
     * @param LifecycleEventArgs $eventArgs
     * @return void
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        // Obtain the entity from the lifecycle event
        $entity = $eventArgs->getEntity();
        $configuration = $this->obtainConfiguration($entity);
        if (!empty($configuration)) {
            // Set the default create date
            $created = $this->getField($entity, (empty($configuration->created) ? 'created' : $configuration->created));
            if(empty($created))
                $this->setField($entity, (empty($configuration->created) ? 'created' : $configuration->created), new \DateTime('now'));

            // Look for the updated field
            $this->setField($entity, (empty($configuration->updated) ? 'updated' : $configuration->updated), new \DateTime('now'));
        }
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        // Obtain the entity from the lifecycle event
        $entity = $eventArgs->getEntity();
        $configuration = $this->obtainConfiguration($entity);
        if (!empty($configuration)) {
            // Update the value
            $eventArgs->setNewValue((empty($configuration->updated) ? 'updated' : $configuration->updated), new \DateTime('now'));
        }
    }


    /**
     * Uses annotations to determine the configuration object
     * Although called several times the annotation reader uses a local cache to speed.
     * @param $entity
     * @return
     */
    public function obtainConfiguration($entity)
    {
        // Read the entity annotations
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reflectionClass = new \ReflectionClass(get_class($entity));

        // Look for our sluggable heaviour
        $configuration = $reader->getClassAnnotation($reflectionClass, 'FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration\Timestampable');
        return $configuration;
    }

    public function getField($entity, $field)
    {
        // Obtain the value
        if(method_exists($entity, 'get' . ucfirst($field))) {
            $value = call_user_func(array($entity, 'get' . ucfirst($field)));
        } else {
            $reflectionClass = new \ReflectionClass(get_class($entity));
            if(!$reflectionClass->hasProperty($field)) {
                throw new Configuration\BehaviourConfigurationException('Slug field "' . $field . '" does not exist for class ' . get_class($entity));
            } else {
                $property = $reflectionClass->getProperty($field);
                if($property->isPrivate() || $property->isProtected()) {
                    throw new Configuration\BehaviourConfigurationException('Slug field "' . $field . '" is not accessible ' . get_class($entity));
                } else {
                    $value = $entity->$field;
                }
            }
        }
        return $value;
    }

    public function setField($entity, $field, $value)
    {
        if(method_exists($entity, 'set' . ucfirst($field))) {
            call_user_func(array($entity, 'set' . ucfirst($field)), $value);
        } else {
            $reflectionClass = new \ReflectionClass(get_class($entity));
            if(!$reflectionClass->hasProperty($field)) {
                throw new Configuration\BehaviourConfigurationException('Slug field "' . $field . '" does not exist for class ' . get_class($entity));
            } else {
                $property = $reflectionClass->getProperty($field);
                if($property->isPrivate() || $property->isProtected()) {
                    throw new Configuration\BehaviourConfigurationException('Slug field "' . $field . '" is not accessible ' . get_class($entity));
                } else {
                    $entity->$field = $value;
                }
            }
        }
    }
}
