<?

namespace Arthedain\Translation;

trait HasTranslation
{

    public static $data = false;
    public static $default_custom_properties = [
        'tab'       => 'All',
        'editor'    => false,
    ];

    public static function locale(string $name, array $custom_properties = ['tab' => 'All', 'editor' => false]): string
    {
        if (!self::$data) {
            self::$data = self::all()->pluck('value', 'key');
        }

        if (!isset(self::$data[$name])) {
            self::create([
                'key' => $name,
                'value' => $name,
                'custom_properties' => array_merge(self::$default_custom_properties, $custom_properties)
            ]);
            self::$data[$name] = $name;
        }

        return self::$data[$name];
    }
}
