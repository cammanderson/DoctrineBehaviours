# DoctrineBehaviours
The DoctrineBehaviour bundle provides implementations of behaviours for Doctrine2 and Symfony2.

## Symfony2 Installation

Modify your ``deps`` file in your Symfony2 project

    [DoctrineBehaviours]
        git=https://github.com/FlintLabs/DoctrineBehaviours.git

Update your vendors

    php bin/vendors install

Add the bundle to your ``app/AppKernel.php``

    new FlintLabs\Bundle\DoctrineBehavioursBundle\FlintLabsDoctrineBehavioursBundle(),

Add to the autoloader ``app/autoload.php``

    'FlintLabs\\Bundle\\DoctrineBehavioursBundle' => __DIR__.'/../vendor/DoctrineBehaviours/src',


## Behaviours

### Sluggable
* Simple behaviour for generating unique slugs for your entities
* Done using annotations
* Ensures slugs don't duplicate
* Support iconv/transliterate e.g. é -> e
* Uses dependency injection allowing you to implement custom slugger

#### Using the behaviour

Add the annotation to your entity class

    <?php
    namespace FlintLabs\Bundle\ExampleBundle\Entity;
    use Doctrine\ORM\Mapping as ORM,
        FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration as Behaviour;

    /**
     * @ORM\Entity
     * @ORM\Table
     * @Behaviour\Sluggable("slug", fields={"title","author"})
     */
    class BlogPost  {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue
         */
        public $id;

        /**
         * @ORM\Column(type="string")
         */
        public $title;

        /**
         * @ORM\Column(type="string")
         */
        public $author;

        /**
         * @ORM\Column(type="string")
         */
        public $slug;
    }

##### Custom Slugger

You can create your own Slugger (for instance changing the way slugs are presented) using Dependency Injection.

Create a class that implements the class \FlintLabs\Bundle\DoctrineBehavioursBundle\SluggerInterface

    <?php
    namespace FlintLabs\Bundle\ExampleBundle;
    use FlintLabs\Bundle\DoctrineBehavioursBundle\SluggerInterface;

    class MySlugger implements SluggerInterface
    {
        public function getSlug($fields, $exclude = array())
        {
            // Return a slug based on the fields. e.g. Array('title' => 'foo', 'author' => 'bar')
        }
    }

Add to your ``service.xml``

    <parameters>
        <parameter key="doctrinebevahiour.sluggable.slugger.class">FlintLabs\Bundle\ExampleBundle\MySlugger</parameter>
    </parameters>

### Timestampable
* Simple behaviour for handling created/updated timestamps
* Done using annotations

#### Using the behaviour

Add the annotation to your entity class

    <?php
    namespace FlintLabs\Bundle\ExampleBundle\Entity;
    use Doctrine\ORM\Mapping as ORM,
        FlintLabs\Bundle\DoctrineBehavioursBundle\Configuration as Behaviour;

    /**
     * @ORM\Entity
     * @ORM\Table
     * @Behaviour\Timestampable
     */
    class BlogPost  {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue
         */
        public $id;

        /**
         * @ORM\Column(type="string")
         */
        public $title;

        /**
         * @ORM\Column(type="string")
         */
        public $author;

        /**
         * @ORM\Column(type="datetime")
         */
        public $updated;

        /**
         * @ORM\Column(type="datetime")
         */
        public $created;
    }