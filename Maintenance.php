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

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Thelia\Module\BaseModule;

class Maintenance extends BaseModule
{
    /** @var string */
    const DOMAIN_NAME = 'maintenance';

    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */


    /**
     * @return SplFileInfo
     */
    public static function getMaintenanceFile()
    {
        $finder = new Finder();
        $finder->files()->in(THELIA_MODULE_DIR . 'Maintenance' . DS . 'templates')->name("maintenance.html");

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            return $file;
        }
    }
}
