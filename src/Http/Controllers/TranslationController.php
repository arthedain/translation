<?php

namespace Arthedain\Translation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public $model;

    public function __construct()
    {
        $this->model = config('nova-translation.model') ?? \App\Models\Translation::class;    
    }

    public function get(){
        $data = $this->model::all();

        return response()->json($data);
    }

    public function post(Request $request){
        foreach(request()->all() as $key => $item){
            $this->model::where('id', $key)->where('value', '!=', $item)->update([
                'value' => $item
            ]);
        }
        return \response('', 200);
    }
}
