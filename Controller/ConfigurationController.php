<?php

namespace Maintenance\Controller;

use Maintenance\Maintenance;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;

class ConfigurationController extends BaseAdminController
{
    public function viewAction()
    {
        $maintenanceFile = Maintenance::getMaintenanceFile();

        $content = $maintenanceFile->getContents();

        preg_match('/<!--TITLE START-->((.|\n)*?)<!--TITLE END-->/', $content, $title);
        preg_match('/<!--MESSAGE START-->((.|\n)*?)<!--MESSAGE END-->/', $content, $message);
        preg_match('/background_color\*\/((.|\n)*?)\/\*background_color/', $content, $backgroundColor);
        preg_match('/font_color\*\/((.|\n)*?)\/\*font_color/', $content, $fontColor);
        preg_match('/link_color\*\/((.|\n)*?)\/\*link_color/', $content, $linkColor);


        $finder = new Finder();
        $finder->files()->in(THELIA_WEB_DIR)->name('index.php');

        $indexContent = "";

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $indexContent = $file->getContents();
        }
        $isInMaintenance = 0;

        if (preg_match("/\/\/⚠((.|\n)*)⚠/", $indexContent)) {
            $isInMaintenance = 1;
        }

        return $this->render(
            'maintenance/configuration',
            [
                'title' => html_entity_decode($title[1]),
                'message' => html_entity_decode($message[1]),
                'backgroundColor' => html_entity_decode($backgroundColor[1]),
                'fontColor' => html_entity_decode($fontColor[1]),
                'linkColor' => html_entity_decode($linkColor[1]),
                'isInMaintenance' => $isInMaintenance
            ]
        );
    }

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

    public function toggleMaintenanceAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'Maintenance', AccessManager::VIEW)) {
            return $response;
        }

        $form = $this->createForm("toggle_maintenance_form");

        try {
            $data = $this->validateForm($form)->getData();

            $finder = new Finder();
            $finder->files()->in(THELIA_WEB_DIR)->name('index.php');

            $contents = "";

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                $contents = $file->getContents();
            }

            if (preg_match("/\/\/⚠((.|\n)*)⚠/", $contents)) {
                $newContent = preg_replace("/\/\/⚠((.|\n)*)⚠/", "", $contents);
            } else {
                $maintenanceTag = "**/\n\n//⚠--MAINTENANCE\nhttp_response_code(503);\ninclude('../local/modules/Maintenance/templates/maintenance.html');\ndie();\n//__⚠\n";
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