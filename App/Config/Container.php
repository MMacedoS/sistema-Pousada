<?php

namespace App\Config;

use ReflectionClass;

class Container 
{

    protected $instances = [];

    public function set($key, $value) {
        $this->instances[$key] = $value;
    }

    public function get($key) {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (class_exists($key)) {
            $reflection = new ReflectionClass($key);
            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                $this->instances[$key] = new $key();
                return $this->instances[$key];
            }

            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $dependencyClass = $parameter->getType()->getName();
                $dependencies[] = $this->get($dependencyClass);
            }

            $this->instances[$key] = $reflection->newInstanceArgs($dependencies);
            return $this->instances[$key];
        }

        throw new \Exception("Dependência {$key} não encontrada.");
    }
}