<?php
namespace Consolidation\SiteAlias;

trait FunctionUtils
{
    protected $sut;

    protected function callProtected($methodName, $args = [])
    {
        $r = new \ReflectionMethod($this->sut, $methodName);
        if (\PHP_VERSION_ID < 80100) {
            $r->setAccessible(true);
        }
        return $r->invokeArgs($this->sut, $args);
    }
}
