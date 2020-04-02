<?

namespace Arthedain\Translation;

trait HasTranslation
{

    public static $data = false;

    public static function locale($name)
    {
        
        $key = config('nova-translation.key') ?? 'key';
    
        $value = config('nova-translation.value') ?? 'value';

        if (!self::$data) {
            self::$data = self::all()->pluck($value, $key);
        }

        if (!isset(self::$data[$name])) {
            self::create([
                $key => $name,
                $value => $name
            ]);
            self::$data[$name] = $name;
        }

        return self::$data[$name];
    }
}
