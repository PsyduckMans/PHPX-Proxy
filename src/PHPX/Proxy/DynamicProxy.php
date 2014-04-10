<?php
/**
 * PHP Extendtion Library (https://github.com/PsyduckMans/PHPX-Proxy)
 *
 * @link      https://github.com/PsyduckMans/PHPX-Proxy for the canonical source repository
 * @copyright Copyright (c) 2014 PsyduckMans (https://ninth.not-bad.org)
 * @license   https://github.com/PsyduckMans/PHPX-Proxy/blob/master/LICENSE MIT
 * @author    Psyduck.Mans
 */

namespace PHPX\Proxy;

/**
 * Class DynamicProxy
 * @package PHPX\Proxy
 */
class DynamicProxy {
    /**
     * @var object
     */
    protected $target;

    /**
     * @var mix
     */
    private $result;

    /**
     * @param $target
     * @return DynamicProxy
     */
    public static function createFrom($target) {
        $proxy = new self();
        $proxy->setTarget($target);
        return $proxy;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws RuntimeException
     */
    public function __call($name, $arguments) {
        $this->__preCall($name, $arguments);
        $target = new \ReflectionClass($this->target);
        if ($target->hasMethod($name)) {
            $method = $target->getMethod($name);
            if ($method->isPublic() && !$method->isStatic()) {
                $this->result = $method->invokeArgs($this->target, $arguments);
            } else {
                throw new RuntimeException(get_class($this->target).'->'.$name.' can not public and static');
            }
        } else {
            if($this->target instanceof self) {
                $this->result = $this->target->__call($name, $arguments);
            } else {
                throw new RuntimeException(get_class($this->target).'->'.$name.' does not exist');
            }
        }
        $this->__postCall($name, $arguments);
        return $this->result;
    }

    /**
     * @param $name
     * @param $arguments
     */
    protected function __preCall($name, $arguments) { }

    /**
     * @param $name
     * @param $arguments
     */
    protected function __postCall($name, $arguments) { }

    /**
     * @return mix
     */
    protected function getResult() {
        return $this->result;
    }

    /**
     * @param object $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return object
     */
    public function getTarget()
    {
        return $this->target;
    }
}