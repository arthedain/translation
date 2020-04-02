<?php

namespace Arthedain\Translation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public $model;
    public $key;
    public $value;

    public function __construct()
    {
        $this->model = config('nova-translation.model') ?? \App\Models\Translation::class;    
        $this->key = config('nova-translation.key') ?? 'key';
        $this->value = config('nova-translation.value') ?? 'value';
    }

    public function get(){
        $data = $this->model::all();

        return response()->json(['data'=>$data, 'key' => $this->key, 'value' => $this->value]);
    }

    public function update(Request $request){
        
        foreach(request()->all() as $key => $item){
            $this->model::where('id', $key)->where('value', '!=', $item)->update([
                'value' => $item
            ]);
        }
        return \response('', 200);
    }
}
