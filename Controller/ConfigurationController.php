<?php

namespace Maintenance\Controller;

use Maintenance\Maintenance;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/Maintenance", name="maintenance")
 */
class ConfigurationController extends BaseAdminController
{
    /**
     * @Route("/configuration", name="_save", methods="POST")
     */
    public function configurationAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'Maintenance', AccessManager::VIEW)) {
            return $response;
        }

        $form = $this->createForm("maintenance_configuration_form");

        try {
            $data = $this->validateForm($form)->getData();

            $maintenanceFile = Maintenance::getMaintenanceFile();

            $content = $maintenanceFile->getContents();

            $newContent = preg_replace("/<!--TITLE START-->((.|\n)*)<!--TITLE END-->/", "<!--TITLE START-->".$data['title']."<!--TITLE END-->", $content);
            $newContent = preg_replace("/<!--MESSAGE START-->((.|\n)*)<!--MESSAGE END-->/", "<!--MESSAGE START-->".$data['message']."<!--MESSAGE END-->", $newContent);
            $newContent = preg_replace("/background_color\*\/((.|\n)*)\/\*background_color/", "background_color*/".$data['background_color']."/*background_color", $newContent);
            $newContent = preg_replace("/font_color\*\/((.|\n)*)\/\*font_color/", "font_color*/".$data['font_color']."/*font_color", $newContent);
            $newContent = preg_replace("/link_color\*\/((.|\n)*)\/\*link_color/", "link_color*/".$data['link_color']."/*link_color", $newContent);

            file_put_contents($maintenanceFile->getPathname(), $newContent);

        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans(
                    "Error",
                    [],
                    Maintenance::DOMAIN_NAME
                ),
                $e->getMessage(),
                $form
            );
            return $this->viewAction();
        }
        return $this->generateSuccessRedirect($form);
    }

    /**
     * @Route("/toggle", name="_toggle_maintenance", methods="POST")
     */
    public function toggleMaintenanceAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'Maintenance', AccessManager::VIEW)) {
            return $response;
        }

        $form = $this->createForm("toggle_maintenance_form");

        try {
            $data = $this->validateForm($form)->getData();

            $finder = new Finder();
            $finder->files()->depth('== 0')->in(THELIA_WEB_DIR)->name('index.php');

            $contents = "";

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                $contents = $file->getContents();
            }

            if (preg_match("/\/\/⚠((.|\n)*)⚠/", $contents)) {
                $newContent = preg_replace("/\/\/⚠((.|\n)*)⚠/", "", $contents);
            } else {
                $maintenanceTag = "**/\n\n//⚠--MAINTENANCE\nhttp_response_code(503);\ninclude('maintenance.html');\ndie();\n//__⚠\n";
                $newContent = preg_replace("/\*\*\/\n\n/i", $maintenanceTag, $contents);
            }


            file_put_contents($file->getFilename(), $newContent);
        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans(
                    "Error",
                    [],
                    Maintenance::DOMAIN_NAME
                ),
                $e->getMessage(),
                $form
            );
            return $this->viewAction();
        }
        return $this->generateSuccessRedirect($form);
    }
}
