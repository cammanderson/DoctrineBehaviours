<?php
namespace FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration;

/**
 * An annotation for the Sluggable behaviour
 * e.g. @Behaviour\Sluggable("slug", fields={"c1", "c2"})
 * @author Cam Manderson (cameronmanderson@gmail.com)
 */
class Sluggable extends \Doctrine\Common\Annotations\Annotation
{
    public $value = 'slug';
    public $fields;
}
