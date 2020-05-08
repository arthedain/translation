<?

namespace Arthedain\Translation;

trait HasTranslation
{

    public $data = false;
    public $default_custom_properties = [
        'tab'       => 'All',
        'editor'    => false,
        'key'       => false
    ];

    public function locale(string $name, array $custom_properties = []): string
    {
        if (!$this->data) {
            $this->data = $this->all()->pluck('value', 'key');
        }

        if (!isset($this->data[$name])) {
            $this->create([
                'key' => $name,
                'value' => $name,
                'custom_properties' => array_merge($this->default_custom_properties, $custom_properties)
            ]);
            $this->data[$name] = $name;
        }

        return $this->data[$name];
    }
}
