<?php
namespace GiovanniMansillo\Component\Dory\Administrator\Extension;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Psr\Container\ContainerInterface;

# Entry point for the backend
class DoryComponent extends MVCComponent implements BootableExtensionInterface
{
    public function boot(ContainerInterface $container)
    {
    }
}