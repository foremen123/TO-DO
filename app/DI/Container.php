<?php

namespace app\DI;

use app\Exceptions\IsNotInstantiable;
use app\Exceptions\RouteNotFoundException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class Container implements ContainerInterface
{

    private array $entries = [];

    public function get(string $id): object
    {
        if ($this->has($id)) {

            $entry = $this->entries[$id];
            return $entry($this);
        }

        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, callable $value): void
    {
        $this->entries[$id] = $value;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws IsNotInstantiable
     */

    public function resolve(string $id): Object
    {
        try {
            $reflection = new ReflectionClass($id);
            if (!$reflection->isInstantiable()) {
                throw new IsNotInstantiable();
            }
            $getConstruct = $reflection->getConstructor();
            if (!$getConstruct) {
                return new $id;
            }
            $params = $getConstruct->getParameters();
            if (!$params) {
                return new $id;
            }
            $dependencies = array_map(function (ReflectionParameter $param) {
                $name = $param->getName();
                $type = $param->getType();

                if (!$type) {
                    throw new RouteNotFoundException('This parameter' . $name . ' don\'t have type hint');
                }

                if ($type instanceof ReflectionUnionType) {
                    throw new RouteNotFoundException('This parameter' . $name . ' have union type');
                }

                if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                    return $this->get($type->getName());
                }

                throw new RouteNotFoundException('This parameter' . $name . ' have not built-in type');
            }, $params);
            return $reflection->newInstanceArgs($dependencies);
        } catch (RouteNotFoundException $e) {
            throw new RouteNotFoundException('This class' . $id . ' not found');
        } catch (Exception $e){
            throw new Exception('This class' . $id . ' not found');
        }
    }
}