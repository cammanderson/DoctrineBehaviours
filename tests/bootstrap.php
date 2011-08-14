<?php
// Utilise the Doctrine2 as required
spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'DoctrineBehaviours\\')) {
        $path = __DIR__.'/../tests/'.strtr($class, '\\', '/').'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;
            return true;
        }
    } else if (0 === strpos($class, 'FlintLabs\\Bundle\\DoctrineBehavioursBundle\\')) {
        $path = __DIR__.'/../src/'.($class = strtr($class, '\\', '/')).'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;
            return true;
        }
    } else if (0 === strpos($class, 'Doctrine\\Common\\')) {
        $path = __DIR__.'/../vendor/doctrine-common/lib/'.($class = strtr($class, '\\', '/')).'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;
            return true;
        }
    } else if (0 === strpos($class, 'Doctrine\\DBAL\\')) {
        $path = __DIR__.'/../vendor/doctrine-dbal/lib/'.($class = strtr($class, '\\', '/')).'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;
            return true;
        }
    } else if (0 === strpos($class, 'Doctrine')) {
        $path = __DIR__.'/../vendor/doctrine/lib/'.($class = strtr($class, '\\', '/')).'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;
            return true;
        }
    }
});

// Use annotations
use Doctrine\Common\Annotations\AnnotationRegistry;
AnnotationRegistry::registerLoader(function($class) {
    return class_exists($class);
});
AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

