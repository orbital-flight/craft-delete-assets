# Delete Unused Assets

*Free up space by getting rid of those unused assets!*

Delete Unused Assets is a simple plugin allowing you to optimize your project by deleting all unused assets in one click.

The plugin detects which assets are used only in a draft or a revision element, and offers an option to delete them as well.

The plugin checks the relations table to determine if an asset is used (related to) anything.

This plugin is in active development and will feature upcoming quality of life updates along with translations.

## Analyses
Analyses (also known as scans) are only started from the plugin section*, and thus won't impact your project performance in case it would feature a very large number of assets.

When you access the plugin page, a scan of any unanalyzed volumes is automatically triggered. The plugin also detects if the amount of assets in a given volume has changed since the last scan, which also triggers an analysis.

*As of 2.x, changes in assets relationships are **not** automatically detected (yet), and won't be displayed until the next analysis is performed. This however won't accidentally delete used assets, as any cleanup process always starts a fresh scan of the volume.*

You can disable the auto-scan feature in the plugin settings.

\**See below "Will it slow my project down?"*

## Will it slow my project down?
The plugin features several guards and routines to make in run like a breeze. 
Even if it keeps an eye out for changes in your volumes, the plugin would simply mark them as 'outdated' so they can be scanned later (we don't want a scan each time you add a new asset).

**Scans are only run from the plugin's dedicated section** and cannot take place anywhere else without you knowing about it. 
Those scans can be started manually or triggered automatically in case a volume is flagged as outdated. In the later case, only outdated volumes are scanned (you can disable the autoscan from the plugin settings).

The only exception is whenever you add your first asset in a new volume, in which case a micro-scan is performed to be able to keep track of that volume. This can still be avoided by disabling the autoscan feature in the plugin settings.

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
