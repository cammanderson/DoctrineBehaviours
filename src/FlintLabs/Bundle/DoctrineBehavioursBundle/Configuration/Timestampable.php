<?php
namespace FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration;
 
class Timestampable extends \Doctrine\Common\Annotations\Annotation
{
    public $updated;
    public $created;
}
