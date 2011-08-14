<?php
namespace DoctrineBehaviours\Entity;
use FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration as Behaviour;

/**
 * @Behaviour\Sluggable(fields={"title","author"})
 */
class GenericEntity {
    public $slug;
    public $title;
    public $author;
}
