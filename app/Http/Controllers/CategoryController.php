<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Auth;
use DB;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$categories = Category::with('children')->whereNull('parent_id')->get();
        $categories = Category::all();

        return view('dashboard.categories.index')->with([
            'categories'  => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('dashboard.categories.create', ['categories'  => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
            'name'      => 'required|unique:categories|min:3|max:255|string',
            'parent_id' => 'sometimes|nullable|numeric',
            'description' => 'sometimes|nullable|string'
        ]);

        $message = "You have successfully created a Category!";
        Category::create($validatedData);

        return redirect()->route('category.index')->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editData = Category::find($id);
        $parentCategory = Category::all();
        return view('dashboard.categories.create', [ 'editData' => $editData, 'categories' => $parentCategory ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
        $validatedData = $this->validate($request, [
            'name'  => 'required|min:3|max:255|string',
            'parent_id' => 'sometimes|nullable|numeric',
            'description' => 'sometimes|nullable|string'
        ]);

        $category->update($validatedData);

        return redirect()->route('category.index')->withSuccess('You have successfully updated a Category!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
        if ($category->children) {
            foreach ($category->children()->with('posts')->get() as $child) {
                foreach ($child->posts as $post) {
                    $post->update(['category_id' => NULL]);
                }
            }
            
            $category->children()->delete();
        }

        foreach ($category->posts as $post) {
            $post->update(['category_id' => NULL]);
        }

        $category->delete();

        return redirect()->route('category.index')->withSuccess('You have successfully deleted a Category!');
    }

    public function getSubCategoryListFromParent(Request $request, $id)
    {
        $category = Category::find($id);
        $children = $category->children;

        return $children->toArray();
    }

    public function checkNestedLevelForVerticalCategory(Request $request, $id)
    {
        $category_id = $id;
        
    }

    public function categoryAttributes(Request $request, $id) {
        $attributes = DB::table('attributes')->where('category_id', $id)->where('flag', 1)->get();
        return view('dashboard.categories.attributes', [ 'category_id' => $id, 'attribute_data' => $attributes ]);
    }

    public function categoryAttributesStore(Request $request) {
        $title = $request->title;
        $message = 'Incomplete';
        if($title && $request->category_id) {
            DB::table('attributes')->where('category_id', $request->category_id)->update(['flag' => 2]);
            foreach ($title as $key => $value) {
                $cart = DB::table('attributes')->updateOrInsert(['category_id' => $request->category_id, 'title' => $value], ['flag' => 1]);
            }
            $message = 'Attribute added successfully';
        }

        return redirect()->route('category.index')->withSuccess($message);
    }
}
