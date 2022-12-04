<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\CategoryImages;

class CategoryController extends Controller
{
    public function index()
    {
        return view('category.index');
    }

    public function getList(Request $request)
    {
        
        $page_length = $request->length;
        $search_text = $request->search['value'];
        $offset = $request->start;

        $data = [];
        $order_column = $request->order[0]['column'];
        $order_by = $request->order[0]['dir'];

        $sql_query = Category::select('categories.*');

        if (!empty($search_text)) {
            $split_array = explode(' ', $search_text);
            $sql_query->where(function ($q) use ($split_array) {
                foreach ($split_array as $txt) {
                    $q->orWhere('categories.name', 'like', '%' . $txt . '%');
                }
            });
        }
        if ($order_column == 0) {
            $sql_query->orderBy('categories.name', $order_by);
        }
        else {
            $sql_query->orderBy('categories.create_at', "desc");
        }
        $query = clone $sql_query;
        $query->offset($offset)->limit($page_length);
        $list_data = $query->get();
        $list_data_count = $sql_query->get()->count();
        foreach ($list_data as $key => $val) {
            $action = '';
            $action .= '<a href="' . route('category.edit', ['id' => $val->id]) . '" title="edit" class="btn btn-warning btn-icon btn-sm edit" style="margin-right:5px;"><i class="fa fa-edit"></i></a>';
            //  $action .= '<a onclick="return deleteconfirm()" href="' . route('student.delete', array('id' => $val->id)) . '" title="delete" class="btn btn-danger btn-icon btn-sm remove"><i class="fa fa-trash"></i></a>';
            $nestedData['name'] = $val->name;
            $nestedData['action'] = $action;
            $data[] = $nestedData;
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($list_data_count),
            "recordsFiltered" => intval($list_data_count),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories',
        ]);
        try {
            $category = new Category();
            $category->name  = $request->name;
            $category->slug = str_replace("-"," ",$request->name);
            $category->save();

            // save Multiple images
            $images = $request->file('images');
            if ($images) {
                foreach($images as $image){
                    $category_image = new CategoryImages();
                    $category_image->category_id = $category->id;
                    $category_image->type = 0;
                    $ext = $image->getClientOriginalExtension();
                    $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                    $destinationPath = '/images/category/' . $category->id;
                    /**create folder  **/
                    $destinationPath = base_path() . '/public/images/category/' . $category->id . "/";
                    if (!file_exists($destinationPath)) {
                        \File::makeDirectory($destinationPath, 0777, true);
                        chmod($destinationPath, 0777);
                    }
                    $image->move($destinationPath, $newFileName);
                    $category_image->image = $newFileName;
                    $category_image->save();
                } 
            }
            return redirect()->route('categories')->with('success', 'Category added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $category = Category::find($id);
            if ($category) {
                return view('category.edit', compact('category'));
            } else {
                return redirect()->back()->with('error', 'Camp manager not found');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|unique:categories,name,' . $id
        ]);

        try {
            $category = Category::find($id);
            $category->name = $request->name;
            $category->slug = str_replace("-"," ",$request->name);
            $category->save();
            return redirect()->route('categories')
                ->with('success', 'Category updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            return redirect()->route('camp_manager')
                ->with('success', 'Camp manager deleted successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }

}
