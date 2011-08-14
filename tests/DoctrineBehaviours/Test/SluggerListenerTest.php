<?php
namespace DoctrineBehaviours\Test;

use FlintLabs\Bundle\DoctrineBehavioursBundle\EventListener\SluggableListener,
    FlintLabs\Bundle\DoctrineBehavioursBundle\Slugger,
    DoctrineBehaviours\Entity;

class SluggerListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        // TODO: Test the Handle function with an entity manager
    }

    public function testGetSlugCompositionValuesFromEntity()
    {
        $sluggerListener = new SluggableListener(new Slugger());

        // Prepare the entity
        $entity = new Entity\GenericEntity();
        $entity->author = 'Cameron';
        $entity->title = 'DoctrineBehaviours';

        $values = $sluggerListener->getSlugCompositionValuesFromEntity($entity);
        $this->assertTrue(!empty($values), 'Should have obtained some composition values');
        $this->assertTrue(count($values) == 2, 'Should have 2 values');
        $this->assertArrayHasKey('author', $values, 'Should have returned the author field');
        $this->assertEquals($values['title'], 'DoctrineBehaviours', 'Should have obtained the title value (using public property) in the composition data');
        $this->assertArrayHasKey('title', $values, 'Should have returned the title field');
        $this->assertEquals($values['author'], 'Cameron', 'Should have obtained the author value (using public property) in the composition data');

        $entity = new Entity\GenericEntityVariation();
        $entity->setTitle('test');
        $values = $sluggerListener->getSlugCompositionValuesFromEntity($entity);
        $this->assertTrue(!empty($values), 'Should have obtained some composition values');
        $this->assertTrue(count($values) == 1, 'Should have 1 value');
        $this->assertArrayHasKey('title', $values, 'Should have returned the title field');
        $this->assertEquals($values['title'], 'test', 'Should have obtained the title value (using getTitle) in the composition data');
    }

    public function testSetSlugValue()
    {
        $sluggerListener = new SluggableListener(new Slugger());

        // Prepare the entity
        $entity = new Entity\GenericEntity();
        $sluggerListener->setSlugValue($entity, 'test');
        $this->assertEquals($entity->slug, 'test', 'Should be setting the slug field to the correct value (using public property)');

        $entity = new Entity\GenericEntityVariation();
        $sluggerListener->setSlugValue($entity, 'test');
        $this->assertEquals($entity->getCustom(), 'test', 'Should be setting the slug field to the correct value (using setSlug)');
    }

    public function testObtainConfiguration()
    {
        $sluggerListener = new SluggableListener(new Slugger());

        // Prepare the entity
        $entity = new Entity\GenericEntity();
        $configuration = $sluggerListener->obtainConfiguration($entity);
        $this->assertEquals($configuration->value, 'slug');
        $this->assertEquals($configuration->fields, array('title', 'author'));

        $entity = new Entity\GenericEntityVariation();
        $configuration = $sluggerListener->obtainConfiguration($entity);
        $this->assertEquals($configuration->value, 'custom');
        $this->assertEquals($configuration->fields, 'title');
    }

}
