<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace Maintenance;

use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Thelia\Module\BaseModule;

class Maintenance extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'maintenance';

    const MAINTENANCE_FILE = THELIA_WEB_DIR.'maintenance.html';

    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */

    public function postActivation(ConnectionInterface $con = null): void
    {
        if (!file_exists(self::MAINTENANCE_FILE)) {
            copy(THELIA_MODULE_DIR . 'Maintenance' . DS . 'templates'. DS .'maintenance.html', self::MAINTENANCE_FILE);
        }
    }


    /**
     * @return SplFileInfo
     */
    public static function getMaintenanceFile()
    {
        if (!file_exists(self::MAINTENANCE_FILE)) {
            copy(THELIA_MODULE_DIR . 'Maintenance' . DS . 'templates'. DS .'maintenance.html', self::MAINTENANCE_FILE);
        }

        $finder = new Finder();
        $finder->files()->in(THELIA_WEB_DIR)->name("maintenance.html");

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            return $file;
        }
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR . ucfirst(self::getModuleCode()). "/I18n/*"])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
