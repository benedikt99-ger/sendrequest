<?php

declare(strict_types=1);

namespace nuenemann\AfterbuyExport;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * @deprecated not required for 7.1.x shop, as there is getService method in Base class
 */
trait ServiceContainer
{
    /**
     * @template T
     * @psalm-param class-string<T> $serviceName
     *
     * @return T
     *
     * @throws ServiceNotFoundException
     */
    protected function getServiceFromContainer(string $serviceName)
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get($serviceName);
    }
}
