<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 24.07.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDune\WebTelegram;
use Zend\ServiceManager\ServiceManager;


class Registry
{
    protected $serviceManager;

    protected $data = [];

    static protected $instance = null;


    protected function __construct()
    {
        $this->serviceManager = new ServiceManager();
    }

    static public function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

}