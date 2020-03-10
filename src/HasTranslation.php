<?

namespace Arthedain\Translation;

trait HasTranslation
{

    public static $key = false;
    public static $value = false;
    public static $data = false;

    public static function locale($name = null)
    {
        if (!self::$key) {
            self::$key = config('nova-translation.key') ?? 'key';
        }
        if (!self::$value) {
            self::$value = config('nova-translation.value') ?? 'value';
        }

        if (!self::$data) {
            self::$data = self::all();
        }

        if ($name) {
            $single = self::$data->where(self::$key, $name)->first();

            if ($single == null) {
                $single = self::create([
                    self::$key => $name,
                    self::$value => $name
                ]);
            }
            return $single[self::$value];
        }

        return self::$data->pluck(self::$value, self::$key);
    }
}
