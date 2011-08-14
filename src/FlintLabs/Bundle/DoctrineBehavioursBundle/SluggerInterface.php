<?php
namespace FlintLabs\Bundle\DoctrineBehavioursBundle;

/**
 * An interface for creating services that can generate a slug for a doctrine entity
 * @author Cam Manderson (cameronmanderson@gmail.com)
 */
interface SluggerInterface
{
    /**
     * Return a slug, ensuring it does not appear in exclude (prior collisions)
     * @abstract
     * @param $fields
     * @param array $exclude list of slugs to exclude
     * @return void
     */
    public function getSlug($fields, $exclude = array());
}
