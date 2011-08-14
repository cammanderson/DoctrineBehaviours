<?php
namespace DoctrineBehaviours\Entity;
use FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration as Behaviour;

/**
 * @Behaviour\Sluggable("custom", fields="title")
 */
class GenericEntityVariation {
    private $title;
    private $custom;


    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setCustom($custom)
    {
        $this->custom = $custom;
    }

    public function getCustom()
    {
        return $this->custom;
    }
}
