# Arthedain/Translation
```h
composer require arthedain/translation
```


#### Publish migrations
```h
php artisan vendor:publish --provider="Arthedain\Translation\ToolServiceProvider" --tag="migration"
```
>In migration you can change or add fields except ```custom_properties``` field
#### Publish model
```h
php artisan vendor:publish --provider="Arthedain\Translation\ToolServiceProvider" --tag="model"
```
#### Publish config
```h
php artisan vendor:publish --provider="Arthedain\Translation\ToolServiceProvider" --tag="config"
```
## How to use

```h
\App\Models\Translation::locale('some text to add in db', ['tab' => 'All', 'editor' => false]);
```
> ```tab``` optinal **string** parameter, create group of fields. The default is ```All```

The value of the `tab` parameter can be changed in the localization file
```h
"All" => "Все"
```
> ```tab``` optinal **boolean** parameter, sets to use a visual editor or not 

##### Localization
Go to ```/lang/vendor/nova/en.json``` 
```h
"Translation" => "Translation",
"Update" => "Update",
"Translations updated successfully" => "Переводы успешно обновлены",
```