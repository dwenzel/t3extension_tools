Switchable Caching For Plugins
==============================


TYPO3 allows to register controller action either cacheable or non-cacheable.

This feature allows to configure this behaviour independently for each plugin instance.

**Note** We extend the class `TYPO3\CMS\Extbase\Service\ExtensionService`. Make this does not collide with alternative Implementations.

### Configuration
Enable the feature "Extension Service" (`extensionServiceEnabled`) in the Extension Manager settings of this extension.

### Usage

When a request is routed to an Extbase Controller Action the ExtensionService checks whether the action is cacheable or not.
It considers the framework settings provided by the ConfigurationManager. 
If an option `notCacheable` is set the content of your plugin will not be cached.

#### TypoScript
```typo3_typoscript
plugin.my_plugin.settings.cache.notCacheable = 1
```

#### Flex Form
add a checkbox to your plugin flex form configuration:

```xml
<T3DataStructure>
    ...
    <sDEF>
        <ROOT>
            ...
            <el>
              <settings.cache.notCacheable>
                <TCEforms>
                  <label>Do not cache plugin content</label>
                  <config>
                    <type>check</type>
                  </config>
                </TCEforms>
              </settings.cache.notCacheable>
            </el>
        </ROOT>
    </sDEF>
    ...
</T3DataStructure>
```
