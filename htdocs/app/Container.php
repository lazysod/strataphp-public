<?php
// Simple Dependency Injection Container for StrataPHP
class Container
{
    protected $services = [];
    protected $factories = [];

    // Register a service instance
    public function set($name, $service)
    {
        $this->services[$name] = $service;
    }

    // Register a factory/closure for lazy instantiation
    public function factory($name, callable $factory)
    {
        $this->factories[$name] = $factory;
    }

    // Get a service, instantiate via factory if needed
    public function get($name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }
        if (isset($this->factories[$name])) {
            $this->services[$name] = $this->factories[$name]($this);
            return $this->services[$name];
        }
        throw new Exception("Service '{$name}' not found in container.");
    }
}
