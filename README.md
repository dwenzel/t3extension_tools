# Extension Tools
Provide tools for extensions of the TYPO3 CMS

### Installation
```
composer require dwenzel/t3extension-tools
```
**Note**: This extension will be updated in TYPO3 Extension Repository sporadically only.  
Use the latest composer version instead.

### Features

* [switchable caching for plugin](docs/PluginCache.md)
* register plugins
* register modules
* configure tables
  * allow tables on standard pages
  * add localized description for tables (context sensitive help)
* register bitmap and SVG icons
* load TypoScript and PageTS config
* use traits for console commands (see `Classes/)
### Caveats

Make sure, no other extension is extending `TYPO3\CMS\Extbase\Service\ExtensionService`.  
If an extending class is registered via TypoScript it will be found at 
`config.tx_extbase.objects.TYPO3\CMS\Extbase\Service\ExtensionService.className`.
