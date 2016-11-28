# Maintenance

This module allow you to put your site in maintenance mode.
In this mode all page of the site is replaced by a maintenance page configurable in module configuration.
But you can still navigate in your site with index_dev.php.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Maintenance.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require vlopes/maintenance-module:~1.0
```

## Usage

To enable maintenance or edit maintenance page go here : 
```/admin/module/Maintenance```
