<?php

namespace Officine\StdLib;

class DynamicCall
{
    private $scope;
    private $callable;

    public function __construct($callable, $scope = null)
    {
        $this->callable = $callable;
        $this->scope = $scope;
    }

    public function __invoke()
    {
        if ($this->scope) {
            return $this->callMethodWithArguments($this->callable, $this->scope, func_get_args());
        }
        return $this->callFunctionWithArguments($this->callable, func_get_args());
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
