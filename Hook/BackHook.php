<?php


namespace Maintenance\Hook;


use Maintenance\Maintenance;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

class BackHook extends BaseHook
{
    public function onModuleConfig(HookRenderEvent $event)
    {
        $maintenanceFile = Maintenance::getMaintenanceFile();

        $content = $maintenanceFile->getContents();

        preg_match('/<!--TITLE START-->((.|\n)*?)<!--TITLE END-->/', $content, $title);
        preg_match('/<!--MESSAGE START-->((.|\n)*?)<!--MESSAGE END-->/', $content, $message);
        preg_match('/background_color\*\/((.|\n)*?)\/\*background_color/', $content, $backgroundColor);
        preg_match('/font_color\*\/((.|\n)*?)\/\*font_color/', $content, $fontColor);
        preg_match('/link_color\*\/((.|\n)*?)\/\*link_color/', $content, $linkColor);


        $finder = new Finder();
        $finder->files()->depth('== 0')->in(THELIA_WEB_DIR)->name('index.php');

        $indexContent = "";

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $indexContent = $file->getContents();
        }
        $isInMaintenance = 0;

        if (preg_match("/\/\/⚠((.|\n)*)⚠/", $indexContent)) {
            $isInMaintenance = 1;
        }

        $isIndexWritable = is_writable(THELIA_WEB_DIR."index.php");
        $isWebWritable = is_writable(THELIA_WEB_DIR);

        $event->add(
           $this->render(
            'maintenance/configuration.html',
            [
                'title' => html_entity_decode($title[1]),
                'message' => html_entity_decode($message[1]),
                'backgroundColor' => html_entity_decode($backgroundColor[1]),
                'fontColor' => html_entity_decode($fontColor[1]),
                'linkColor' => html_entity_decode($linkColor[1]),
                'isInMaintenance' => $isInMaintenance,
                'isIndexWritable' => $isIndexWritable,
                'isWebWritable' => $isWebWritable,
            ]
           )
        );
    }
}