<?php
/**
 * 
 * version: @package_version@
 * 
 * 
 */
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Finder\Finder;

require __DIR__.'/vendor/autoload.php';

$container = new ContainerBuilder();
$loader = new XmlFileLoader($container, new FileLocator(__DIR__));
$loader->load('services.xml');

// Create application so we can register additional commands in plugins
$application = $container->get('symfony.application');

// run the app
$output = $container->get('symfony.console_output');
$application->run(null, $output);
