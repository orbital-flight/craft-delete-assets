# Delete Unused Assets

*Free up space by getting rid of those unused assets!*

Delete Unused Assets is a simple plugin allowing you to optimize your project by deleting all unused assets in one click.

The plugin detects which assets are used only in a draft or a revision element, and offers an option to delete them as well.

The plugin checks the relations table to determine if an asset is used (related to) anything.

This plugin is in active development and will feature upcoming quality of life updates along with translations.

## Disclaimer
The plugin checks the relations table, so it should work in almost every case.

However, and even though the plugin has been thoroughly tested with Craft, Craft Commerce, and most typical Craft installations, it still involves the hard deletion of content, you should therefore use it with caution. 

**We strongly advise you to have backups of your data, assets, and database in case anything goes wrong.**

If any third-party plugin or custom code uses assets from your volumes without recording the relation in the Craft database, you \***will**\* permanently lose them.

Additionally, if the volume from which you want to delete assets is shared by other Craft installations (*i.e a staging installation sharing an Amazon S3 volume with the production environment*), the plugin will check for relations with the database linked to the project from which it is run. 
This means it may delete assets that are potentially used in other installation(s).

## Requirements

This plugin requires Craft CMS 4.3.5 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Delete Unused Assets”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require orbital-flight/craft-delete-assets

# tell Craft to install the plugin
./craft plugin/install delete-assets
```
