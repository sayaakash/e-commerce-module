<?php
class Container {
    private array $services = [];
    private array $instances = [];

    public function set(string $name, callable $resolver): void {
        $this->services[$name] = $resolver;
    }

    public function get(string $name) {
        if (!isset($this->instances[$name])) {
            if (!isset($this->services[$name])) {
                throw new InvalidArgumentException("Service '{$name}' not found in container");
            }
            $this->instances[$name] = $this->services[$name]($this);
        }
        return $this->instances[$name];
    }

    public function has(string $name): bool {
        return isset($this->services[$name]);
    }

    public function singleton(string $name, callable $resolver): void {
        $this->set($name, function($container) use ($resolver, $name) {
            if (!isset($this->instances[$name])) {
                $this->instances[$name] = $resolver($container);
            }
            return $this->instances[$name];
        });
    }
}
