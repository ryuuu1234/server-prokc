<?php

namespace App\Http\Controllers\API\Admin;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;



class DataKategori extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function get_all()
    {
        $data = Kategori::orderBy(request()->sortby, request()->sorting)
        ->when(request()->q != '', function($search) {
            $i = 0;
            $column_search = ['name'];
            foreach ($column_search as $item ) {
                if ($i === 0) {
                    $search->where($item, 'LIKE', '%' . request()->q . '%');
                }else{
                    $search->orWhere($item, 'LIKE', '%' . request()->q . '%');
                }
                $i++;
            }
            
        })->paginate(request()->per_page);

       if (!$data) {
           return response()->json(['message'=>'failed'], 500);
       }

       return response()->json(['message'=>'success', 'result'=>$data], 200);
    }

    public function add_data(Request $request)
    {
        $request->validate(['name'=>'required']);

        $kategori = new Kategori();
        $kategori->name = $request->name;
        $save = $kategori->save();

        if (!$save) {
            return response()->json(['message'=>'failed'], 500);
        }

        return response()->json(['message'=>'success', 'result'=>$kategori], 200);

    }

    public function remove_data(Request $request)
    {
        $remove = Kategori::whereIn('id', $request->id)->delete();
        if (!$remove) {
            return response()->json(['message'=>'failed'], 500);
        }
        return response()->json(['message'=>'success'], 200);
    }

    public function update_data(Request $request)
    {
        $update = Kategori::where('id', $request->id)->update([
            'name'=>$request->name
        ]);
        if (!$update) {
            return response()->json(['message'=>'failed'], 500);
        }
        return response()->json(['message'=>'success'], 200);
    }

    


   
}
