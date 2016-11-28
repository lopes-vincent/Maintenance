<?php

namespace Maintenance\Form;

use Thelia\Form\BaseForm;

class ConfigurationForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                "title",
                "text"
            )
            ->add(
                "message",
                "text"
            )
            ->add(
                "background_color",
                "text"
            )
            ->add(
                "font_color",
                "text"
            )
            ->add(
                "link_color",
                "text"
            );
    }
    public function getName()
    {
        return "maintenance_configuration_form";
    }

}