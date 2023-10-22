<?php

namespace Schneidermanuel\Dynalinker\Controller;

use Schneidermanuel\Dynalinker\Core\Exception\ControllerException;

class CallMapper
{
    private array $controllers;

    public function __construct()
    {
        $this->controllers = array();
    }

    public function MapCall($path)
    {
        $path = $this->CleanupPath($path);
        foreach ($this->controllers as $controllerPath => $controller) {
            if (str_starts_with($path, $controllerPath)) {
                $this->ProcessCallOnController($path, $controllerPath, $controller);
                return;
            }
        }
        throw new ControllerException("Can not find a suitable controller for call to '" . $path . "'");
    }

    private function ProcessCallOnController($calledPath, $controllerPath, $controller)
    {
        $methodPath = substr($calledPath, strlen($controllerPath));
        $methodPath = $this->CleanupPath($methodPath);
        $reflection = new \ReflectionClass($controller::class);
        $methods = $reflection->getMethods();
        $methodAttributeName = $this->MapHttpCallToAttribute();
        foreach ($methods as $method) {
            $attribute = $method->getAttributes($methodAttributeName);
            if (count($attribute) > 0) {
                $functionPath = $attribute[0]->newInstance()->path;
                $functionPath = $this->CleanupPath($functionPath);
                $functionPath = str_replace("/", "\/", $functionPath);
                if (preg_match("/" . $functionPath . "$/", $methodPath)) {
                    $this->ProcessMethodCall($controller, $method, $functionPath, $methodPath);
                    return;
                }
            }
        }
    }

    public function RegisterController(string $path, $controller)
    {
        $path = $this->CleanupPath($path);
        $this->controllers[$path] = $controller;
    }

    private function CleanupPath(string $path)
    {
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        if (str_ends_with($path, '/')) {
            $path = substr($path, 0, strlen($path) - 1);
        }

        return $path;
    }

    private function MapHttpCallToAttribute()
    {
        return match ($_SERVER['REQUEST_METHOD']) {
            "GET" => HttpGet::class,
            "POST" => HttpPost::class,
            default => throw new ControllerException("The called method type '" . $_SERVER['REQUEST_METHOD'] . "' is not supported"),
        };
    }

    private function ProcessMethodCall($controller, \ReflectionMethod $method, string $functionPath, string $calledPath)
    {
        $parameters = array();
        $functionPath = str_replace("\/", "/", $functionPath);
        $functionSplit = preg_split('/\//', $functionPath);
        $callSplit = preg_split('/\//', $calledPath);

        if (count($functionSplit) != count($callSplit)) {
            throw new ControllerException('Parameter Count missmatch');
        }

        for ($i = 0; $i < count($functionSplit); $i++) {
            $calledPart = $callSplit[$i];
            $functionPart = $functionSplit[$i];
            if ($calledPart != $functionPart) {
                $parameters[] = $calledPart;
            }
        }
        $controller->{$method->name}(...$parameters);
    }
}