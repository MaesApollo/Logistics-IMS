<?php

namespace App\Http\Controllers;

use App\Item;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id', 'ASC')->get();

        return response()->json($categories);
    }

    public function store(Request $request)
    {

        // $category = Category::firstOrCreate([
        // 	'name' => $request->name,
        // ]);

        // return response()->json($this->index()->original);

        $category = Category::where('name', '=', $request->name)->first();

        if (empty($category)) {
            DB::table('categories')->insert([
                [
                    'name' => $request->name,
                    'created_at' =>  \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]
            ]);
            return response()->json($this->index());
        } else {
            return response()->json(['error' => 'Category already exists!'], 500);
        }
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!empty($category))
            return response()->json($category);

        return response()->json(['error' => 'Resource not found!'], 404);
    }

    public function update(Request $request, $id)
    {
        DB::table('categories')->where('id', $id)->update(['name' => $request->name, 'updated_at' => \Carbon\Carbon::now()]);
        return response()->json($this->index()->original);
    }

    public function destroy($id)
    {
        $item = Item::where('category_id', '=', $id)->first();

        if (empty($item)) {
            Category::destroy($id);
            return response()->json($this->index()->original);
        } else {
            return response()->json(["error" => "Can't delete this category, it is still in use."], 500);
        }
    }
}
