<?php

namespace Maintenance\Form;

use Thelia\Form\BaseForm;

class ToggleMaintenanceForm extends BaseForm
{
    protected function buildForm()
    {

    }

    public function getName()
    {
        return "toggle_maintenance_form";
    }
}