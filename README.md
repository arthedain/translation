Publish migrations
php artisan vendor:publish --provider="Arthedain\Translation\ToolServiceProvider" --tag="migration"

composer require arthedain/translation