<?php

namespace Officine\StdLib;

class DynamicCall
{
    private $scope;
    private $callable;
    private $arguments;

    public function __construct($callable, $scope = null)
    {
        $this->callable = $callable;
        $this->attachTo($scope);
    }

    public function attachTo($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function withArguments(array $arguments)
    {
        $this->setArguments($arguments);
        return $this();
    }

    public function __invoke()
    {
        $arguments = $this->arguments ? $this->arguments : func_get_args();

        if ($this->scope) {
            return $this->callMethodWithArguments($this->callable, $this->scope, $arguments);
        }
        return $this->callFunctionWithArguments($this->callable, $arguments);
    }

    public function callMethodWithArguments($method, $scope, $arguments)
    {
        switch (count($arguments)) {
            case 0:
            return $scope->{$method}();

            case 1:
            return $scope->{$method}(
                $arguments[0]
            );

            case 2:
            return $scope->{$method}(
                $arguments[0],
                $arguments[1]
            );

            case 3:
            return $scope->{$method}(
                $arguments[0],
                $arguments[1],
                $arguments[2]
            );

            case 4:
            return $scope->{$method}(
                $arguments[0],
                $arguments[1],
                $arguments[2],
                $arguments[3]
            );

            case 5:
            return $scope->{$method}(
                $arguments[0],
                $arguments[1],
                $arguments[2],
                $arguments[3],
                $arguments[4]
            );

            default:
            return call_user_func_array([$scope, $method], $arguments);
        }
    }

    public function callFunctionWithArguments($function, $arguments)
    {
        switch (count($arguments)) {
            case 0:
            return $function();

            case 1:
            return $function(
                $arguments[0]
            );

            case 2:
            return $function(
                $arguments[0],
                $arguments[1]
            );

            case 3:
            return $function(
                $arguments[0],
                $arguments[1],
                $arguments[2]
            );

            case 4:
            return $function(
                $arguments[0],
                $arguments[1],
                $arguments[2],
                $arguments[3]
            );

            case 5:
            return $function(
                $arguments[0],
                $arguments[1],
                $arguments[2],
                $arguments[3],
                $arguments[4]
            );

            default:
            return call_user_func_array($function, $arguments);
        }
    }
}
