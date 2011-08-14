<?php
namespace FlintLabs\Bundle\DoctrineBehavioursBundle\EventListener;

use FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration,
    Doctrine\ORM\Event\LifecycleEventArgs,
    FlintLabs\Bundle\DoctrineBehavioursBundle\SluggerInterface;

/**
 * A listener for the Doctrine2 Sluggable Behaviour
 * @throws \FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration\SluggableException
 * @author Cam Manderson (cameronmanderson@gmail.com)
 */
class SluggableListener
{
    private $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * Looks for configuration annotations for the sluggable behaviour
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     * @return void
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        // Obtain the entity from the lifecycle event
        $entity = $eventArgs->getEntity();
        $configuration = $this->obtainConfiguration($entity);
        if (!empty($configuration)) {
            // This entity has sluggable behaviour
            $this->handle($entity, $eventArgs->getEntityManager());
        }
    }

    /**
     * @throws \FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration\SluggableException
     * @param $entity
     * @param $entityManager
     * @return void
     */
    public function handle($entity, $entityManager)
    {
        // Obtain the configuration
        $configuration = $this->obtainConfiguration($entity);
        if (empty($configuration))
            throw new Configuration\SluggableConfigurationException('Entity does not contain a sluggable configuration');

        // Obtain configuration
        $slugField = $configuration->value;
        if (empty($slugField))
            throw new Configuration\SluggableConfigurationException('Missing slug @Sluggable("...") field in annotation');

        // Test for values in the entity
        $repository = $entityManager->getRepository(get_class($entity));

        $eliminated = array(); // Our prior eliminated slugs
        $foundSlug = false;
        do {
            // Obtain our slug
            $slug = $this->slugger->getSlug($this->getSlugCompositionValuesFromEntity($entity), $eliminated);

            // See if it is in our collection
            $result = $repository->findOneBy(array($slugField => $slug));

            // Check to see if we have found a slug that matches
            if (!empty($result) && $result !== $entity) {
                $eliminated[] = $slug;
            } else {
                // We have found a slug for this element
                $foundSlug = true;
            }
        } while ($foundSlug === false);

        // Set the slug back to the entity
        $this->setSlugValue($entity, $slug);
    }

    /**
     * Identifies the values to compose the slug from the object
     * @throws \FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration\SluggableException
     * @param $entity
     * @return array
     */
    public function getSlugCompositionValuesFromEntity($entity)
    {
        // Obtain the configuration
        $configuration = $this->obtainConfiguration($entity);
        if (empty($configuration))
            throw new Configuration\SluggableConfigurationException('Entity does not contain a sluggable configuration');

        // Obtain the configuration fields
        $slugFrom = $configuration->fields;
        if(!is_array($slugFrom)) $slugFrom = array($slugFrom);
        if (empty($slugFrom)) throw new SluggableException('Missing slug from fields @Sluggable(from={...}) in annotation');

        // Look through the properties
        $values = array();
        foreach($slugFrom as $from) {
            // Obtain the value
            if(method_exists($entity, 'get' . ucfirst($from))) {
                $value = call_user_func(array($entity, 'get' . ucfirst($from)));
            } else {
                $reflectionClass = new \ReflectionClass(get_class($entity));
                if(!$reflectionClass->hasProperty($from)) {
                    throw new Configuration\SluggableConfigurationException('Slug field "' . $from . '" does not exist for class ' . get_class($entity));
                } else {
                    $property = $reflectionClass->getProperty($from);
                    if($property->isPrivate() || $property->isProtected()) {
                        throw new Configuration\SluggableConfigurationException('Slug field "' . $from . '" is not accessible ' . get_class($entity));
                    } else {
                        $value = $entity->$from;
                    }
                }
            }
            // Push the value into the collection
            $values[$from] = $value;
        }

        // Obtain the values
        return $values;
    }

    /**
     * @throws \FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration\SluggableException
     * @param \FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration\Sluggable $configuration
     * @param $entity
     * @param $value
     * @return
     */
    public function setSlugValue($entity, $value)
    {
        // Obtain the configuration
        $configuration = $this->obtainConfiguration($entity);
        if (empty($configuration))
            throw new Configuration\SluggableConfigurationException('Entity does not contain a sluggable configuration');

        // Set the value on the object
        $field = $configuration->value;
        if(empty($field)) $field = 'slug';
        if(method_exists($entity, 'set' . ucfirst($field))) {
            call_user_func(array($entity, 'set' . ucfirst($field)), $value);
        } else {
            $reflectionClass = new \ReflectionClass(get_class($entity));
            if(!$reflectionClass->hasProperty($field)) {
                throw new Configuration\SluggableConfigurationException('Slug field "' . $field . '" does not exist for class ' . get_class($entity));
            } else {
                $property = $reflectionClass->getProperty($field);
                if($property->isPrivate() || $property->isProtected()) {
                    throw new Configuration\SluggableConfigurationException('Slug field "' . $field . '" is not accessible ' . get_class($entity));
                } else {
                    $entity->$field = $value;
                }
            }
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
        $configuration = $reader->getClassAnnotation($reflectionClass, 'FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration\Sluggable');
        return $configuration;
    }
}