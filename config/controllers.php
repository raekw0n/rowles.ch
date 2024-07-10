<?php

/*------------------------------------------------------
 | Register controllers and inject services            |
 ------------------------------------------------------*/
require_once __DIR__ . '/../vendor/autoload.php';

use Composer\ClassMapGenerator\ClassMapGenerator;

$namespace = 'App\\Controllers';
$directory = __DIR__ . '/../src/Controllers';
$map = ClassMapGenerator::createMap($directory);

$controllers = [];
foreach ($map as $class => $path) {
    if (strpos($class, $namespace) === 0) {       
        if (class_exists($class)) {
            $controllers[] = $class;
        }
    }
}

foreach ($controllers as $controller) {
    $reflector = new ReflectionClass($controller);

    // Check if class can be instantiated (i.e. not abstract)
    if ($reflector->isInstantiable()) {

        // Get the constructor
        $constructor = $reflector->getConstructor();

        if ($constructor) {
            $params = $constructor->getParameters();
            
            // Resolve dependencies for constructor parameters
            $dependencies = [];
            foreach ($params as $param) {
                $type = $param->getType();

                if ($type && ! $type->isBuiltin()) {
                    // Dependency is a class
                    $dependency = $app[$type->getName()];
                } else {
                    // Dependency is not a class (e.g. scalar type or no typehint)
                    continue;
                }

                $dependencies[] = $dependency;
            }

            // Instantiate the controller with the resolved dependencies
            // and store the controller instance in the service container
            $app[$controller] = $reflector->newInstanceArgs($dependencies);
        } else {
            // Controller has no constructor or constructor with no params
            $app[$controller] = $reflector->newInstance();
        }
    }
}