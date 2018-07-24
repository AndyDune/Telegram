<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 23.07.2018                            |
 * -----------------------------------------------
 *
 * Класс для тестов.
 * В работе внедрить собственную фабрику.
 *
 */


namespace AndyDune\WebTelegram\DoctrineOdm;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

class DocumentManagerFactory
{
    public function get()
    {
        $file = __DIR__ . '/../../vendor/autoload.php';

        if (! file_exists($file)) {
            throw new RuntimeException('Install dependencies to run test suite.');
        }

        $loader = require $file;
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);

        $doctrineFolder = __DIR__ . '/../../doctrine/';

        $config = new Configuration();
        $config->setProxyDir($doctrineFolder . 'proxies');
        $config->setProxyNamespace('AndyDune\WebTelegram\Proxies');
        $config->setHydratorDir($doctrineFolder . 'hydrators');
        $config->setHydratorNamespace('AndyDune\WebTelegram\Hydrators');
        $config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__ . '/Documents'));

        return DocumentManager::create(new Connection(), $config);
    }
}