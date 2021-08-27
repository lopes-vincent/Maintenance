<?php

namespace Maintenance\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Form\BaseForm;

class ConfigurationForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                "title",
                TextType::class
            )
            ->add(
                "message",
                TextType::class
            )
            ->add(
                "background_color",
                TextType::class
            )
            ->add(
                "font_color",
                TextType::class
            )
            ->add(
                "link_color",
                TextType::class
            );
    }
    public static function getName()
    {
        return "maintenance_configuration_form";
    }

}