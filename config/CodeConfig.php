<?php
declare(strict_types=1);
namespace Readdle\QuickBike\Config;

class CodeConfig
{
    public const APP_ROOT = APP_ROOT;
    public const HTTP_ACTIONS_NAMESPACE = 'Readdle\\QuickBike\\Test\\HttpHandlersForTests\\';
    public const HTTP_ACTIONS_PATH = APP_ROOT.'/tests/HttpHandlersForTests/';

    public const DI_APP_CONTAINERS = [
        'Readdle\\QuickBike\\Test\\DIContainerForTests\DIContainer'
    ];

}