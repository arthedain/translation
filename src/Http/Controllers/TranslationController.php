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
    }

    public function get(){
        $data = $this->model::all()->toArray();
        return response()->json(['data'=>$data]);
    }

    public function update(Request $request){
        
        foreach(request()->all() as $key => $item){
            $this->model::where('id', $key)->where('value', '!=', $item)->update([
                'value' => $item
            ]);
        }
        return \response('', 200);
    }

    public function delete(Request $request){
        $this->model::where('id', $request->element[0]['id'])->delete();
        return \response('', 200);
    }
}
