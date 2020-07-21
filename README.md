# Arthedain/Translation
```h
composer require arthedain/translation
```


#### Publish migrations
```
php artisan vendor:publish --provider="Arthedain\Translation\ToolServiceProvider" --tag="migration"
```
>In migration you can change or add fields except ```custom_properties``` field
#### Publish model
```
php artisan vendor:publish --provider="Arthedain\Translation\ToolServiceProvider" --tag="model"
```
#### Publish config
```
php artisan vendor:publish --provider="Arthedain\Translation\ToolServiceProvider" --tag="config"
```
## How to use

```h
\App\Models\Translation::locale('some text to add in db', ['tab' => 'All', 'editor' => false, 'key' => false]);
```
> ```tab``` optinal **string** parameter, create group of fields. The default is ```All```

The value of the `tab` parameter can be changed in the localization file
```h
"All": "Все"
```
> ```editor``` optinal **boolean** parameter, sets to use a visual editor or not 

##### Localization
Go to ```/lang/vendor/nova/en.json``` 
```h
"Translation": "Translation",
"Update": "Update",
"Translations updated successfully": "Переводы успешно обновлены",
"Show default value": "Show default value",
"Show delete button": "Show delete button",
"Saved successfully": "Saved successfully",
"Deleted successfully": "Deleted successfully",
"Error": "Error",
```