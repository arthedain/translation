<?php


namespace Arthedain\Translation;


use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class TranslationField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'translation-field';

    public function tabName(string $name)
    {
        return $this->withMeta([
            'tab' => $name,
        ]);
    }



}
