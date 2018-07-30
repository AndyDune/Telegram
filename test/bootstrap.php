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

$registry = \AndyDune\WebTelegram\Registry::getInstance();

$file = __DIR__ . '/../vendor/autoload.php';

if (! file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}

$loader = require $file;
\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$doctrineFolder = __DIR__ . '/doctrine/';

$config = new \Doctrine\ODM\MongoDB\Configuration();
$config->setDefaultDB(DOCTRINE_MONGODB_DATABASE);
$config->setProxyDir($doctrineFolder . 'proxies');
$config->setProxyNamespace('AndyDune\WebTelegram\Proxies');
$config->setHydratorDir($doctrineFolder . 'hydrators');
$config->setHydratorNamespace('AndyDune\WebTelegram\Hydrators');

$config->setMetadataDriverImpl(\Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::create([__DIR__ . '/../src/DoctrineOdm/Documents']));

//$cache = $config->getMetadataCacheImpl();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\FilesystemCache($doctrineFolder . 'cache'));


//return \Doctrine\ODM\MongoDB\DocumentManager::create(new \Doctrine\MongoDB\Connection(), $config);

$sm = $registry->getServiceManager();
$sm->setService('document_manager', \Doctrine\ODM\MongoDB\DocumentManager::create(new \Doctrine\MongoDB\Connection(DOCTRINE_MONGODB_SERVER), $config));

$sm->setFactory(\AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages::class, function (\Zend\ServiceManager\ServiceManager $serviceLocator) {
    /** @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
    $dm = $serviceLocator->get('document_manager');
    $instance = new \AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages();
    $dm->persist($instance);
    return $instance;
});
$sm->setShared(\AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelMessages::class, false);


$sm->setFactory(\AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages::class, function (\Zend\ServiceManager\ServiceManager $serviceLocator) {
    /** @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
    $dm = $serviceLocator->get('document_manager');
    $instance = new \AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages();
    $dm->persist($instance);
    return $instance;
});
$sm->setShared(\AndyDune\WebTelegram\DoctrineOdm\Documents\ChannelsInfoForMessages::class, false);