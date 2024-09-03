<?php

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use GiovanniMansillo\Component\Dory\Administrator\Extension\DoryComponent;

return new class implements ServiceProviderInterface {

    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\GiovanniMansillo\\Component\\Dory'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\GiovanniMansillo\\Component\\Dory'));

        # start to register the services the component is about to use
        $container->set(
            ComponentInterface::class,
            function (Container $container) {

                $component = new DoryComponent($container->
                    get(ComponentDispatcherFactoryInterface::class));

                $component->setMVCFactory($container->
                    get(MVCFactoryInterface::class));
                return $component;
                
            }
        );
    }

};