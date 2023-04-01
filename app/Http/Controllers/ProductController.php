<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ShopProduct;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    public function index()
    {
        return view('product.index');
    }

    public function getList(Request $request)
    {
        
        $page_length = $request->length;
        $search_text = $request->search['value'];
        $offset = $request->start;

        $data = [];
        $order_column = $request->order[0]['column'];
        $order_by = $request->order[0]['dir'];

        $sql_query = Product::select('products.*');

        if (!empty($search_text)) {
            $split_array = explode(' ', $search_text);
            $sql_query->where(function ($q) use ($split_array) {
                foreach ($split_array as $txt) {
                    $q->orWhere('products.name', 'like', '%' . $txt . '%');
                    $q->orWhere('products.price', 'like', '%' . $txt . '%');
                    $q->orWhereHas('shopkeeper', function ($query) use ($txt) {
                        $query->where('username', 'like', '%' . $txt . '%');
                    });
                }
            });
        }
        if ($order_column == 0) {
            $sql_query->orderBy('products.name', $order_by);
        }
        else if ($order_column == 1) {
            $sql_query->orderBy('products.price', $order_by);
        }
        else if ($order_column == 2) {
            $sql_query->WhereHas('shopkeeper', function ($query) use ($order_by) {
                $query->orderBy('username', $order_by);
            });
        }
        else if ($order_column == 3) {
            $sql_query->WhereHas('shop', function ($query) use ($order_by) {
                $query->orderBy('shop_name', $order_by);
            });
        }
        else {
            $sql_query->orderBy('products.create_at', "desc");
        }
        $query = clone $sql_query;
        $query->offset($offset)->limit($page_length);
        $list_data = $query->get();
        $list_data_count = $sql_query->get()->count();
        foreach ($list_data as $key => $val) {
            $action = '';
            $action .= '<a href="' . route('product.edit', ['id' => $val->id]) . '" title="edit" class="btn btn-warning btn-icon btn-sm edit" style="margin-right:5px;"><i class="fa fa-edit"></i></a>
            <a href="' . route('product.view', ['id' => $val->id]) . '" title="view" class="btn btn-primary btn-icon btn-sm view" style="margin-right:5px;"><i class="fa fa-eye"></i></a>
            <a   title="delete" class="btn btn-danger btn-icon btn-sm remove js-product-delete" data-id="'.$val->id.'" id="js-product-delete"><i class="fa fa-trash"></i></a>';
            //$action .= '';
            $nestedData['product_name'] = $val->name;
            $nestedData['price'] = $val->price;
            $nestedData['shopkeeper'] = $val->shopkeeper->username;
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
        $shop_keepers = User::select('id','username','email')->whereHas('roles', function ($query) {
            $query->whereIn('name', ['shop_keeper']);
            })->get();
        
        $categories = Category::get();
        
        return view('product.create',compact('shop_keepers','categories'));
    }

    

    public function store(Request $request)
    {
        try {

        $validated = Validator::make($request->all(),[
            'shop_keeper_id' => 'required',
            'shop_id' => 'required',
            'product_name' => 'required',
            'product_price' => 'required|numeric|min:0|not_in:0',
            'images*' => 'required|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
        ]);
            $product = new Product(); 
            $product->shopkeeper_id  = $request->shop_keeper_id;
            //$product->shop_id = $request->shop_id;
            $product->cat_id = ($request->category_id) ? $request->category_id : null;
            $product->subcat_id = ($request->sub_category_id) ? $request->sub_category_id : null; 
            $product->name = $request->product_name;
            $product->brand_name = ($request->brand_name) ? $request->brand_name : null;
            $product->model_name = ($request->model_name) ? $request->model_name : null;
            $product->price = $request->product_price;
            $product->selling_price = ($request->product_selling_price) ? $request->product_selling_price : null;
            $product->gender = ($request->gender) ? $request->gender : null;
            $product->size = ($request->size) ? implode(",",$request->size) : null;
            $product->color = ($request->color) ? $request->color : null;
            $product->material = ($request->material) ? $request->material : null;
            $product->wight = ($request->weight) ? $request->weight : null;
            $product->is_gold = ($request->is_gold) ? $request->is_gold : "0";
            $product->device_os = ($request->device_os) ? $request->device_os : null;
            $product->ram = ($request->ram) ? $request->ram : null;
            $product->storage = ($request->storage) ? $request->storages : null;
            $product->connectivity = ($request->connectivity) ? $request->connectivity : null;
            $product->key_featurees = ($request->key_feature) ? implode(",",$request->key_feature) : null;    
            $product->description = ($request->description) ? $request->description : null;

           if($product->save()){
            // save Multiple images
                $images = $request->file('images');
                if ($images) {
                    foreach($images as $image){
                        $product_image = new ProductImage();
                        $product_image->product_id = $product->id;
                        $ext = $image->getClientOriginalExtension();
                        $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                        $destinationPath = '/images/product/' . $product->id;
                        /**create folder  **/
                        $destinationPath = base_path() . '/public/images/product/' . $product->id . "/";
                        if (!file_exists($destinationPath)) {
                            \File::makeDirectory($destinationPath, 0777, true);
                            chmod($destinationPath, 0777);
                        }
                        $image->move($destinationPath, $newFileName);
                        $product_image->image = $newFileName;
                        $product_image->save();
                    }
                    
                }
                // Save Product to all shops
                if($request->shop_id){
                    foreach($request->shop_id as $shop_id){
                        $shop_product = new ShopProduct();
                        $shop_product->shop_id = $shop_id;
                        $shop_product->product_id = $product->id;
                        $shop_product->save();
                    }
                }



            }
            return redirect()->route('products')->with('success', 'Product added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $shop_keepers = User::select('id','username','email')->whereHas('roles', function ($query) {
                $query->whereIn('name', ['shop_keeper']);
                })->get();
            
            $categories = Category::get();
            
            $product = Product::find($id);
            if (empty($product)) {
                return redirect()->back()->with('error', 'Product not found');
            }
            $product->size = $product->size?explode(",",$product->size):[];
            $product->key_featurees = $product->key_featurees?explode(",",$product->key_featurees):[];
            //get shop of product by shopkeeper
            $productShopID = ShopProduct::where('product_id',$product->id)->pluck('shop_id');
            return view('product.edit', compact('product','shop_keepers','categories','productShopID'));

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }


    public function view($id)
    {
        try {
            
            
            $product = Product::with('Shopkeeper:id,username','Category:id,name','Sub_Category:id,name','Product_Image:product_id,image',)->find($id);
            if (empty($product)) {
                return redirect()->back()->with('error', 'Product not found');
            }

            
            //get shop of product by shopkeeper
            $productShopID = ShopProduct::where('product_id',$product->id)->pluck('shop_id');
            $productShopName = Shop::whereIn('id',$productShopID)->pluck('shop_name')->toArray();
            // dd($productShopName);
            $product->shop_name = null;
            if(!empty($productShopName)){
                $product->shop_name = implode(",",$productShopName);
            }
            if(!empty($product->Product_Image)){
                foreach($product->Product_Image as $key =>$image){
                    $product->Product_Image[$key]['image']   = url('/images/product/' . $image->product_id . "/".$image->image);
                }
            }

            return view('product.view', compact('product'));

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }


    public function update(Request $request,$id)
    {
        try {

        $validated = Validator::make($request->all(),[
            'shop_keeper_id' => 'required',
            'shop_id.*' => 'required',
            'product_name' => 'required',
            'model_name' => 'required',
            'product_price' => 'required|numeric|min:0|not_in:0',
            'images*' => 'required|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
        ]);

         if($validated->fails()){
            return redirect()->back()->with('error', $validated->errors()->first());
         }


            $product = Product::where('id',$id)->first();
            $product->shopkeeper_id  = $request->shop_keeper_id;
            //$product->shop_id = $request->shop_id;
            $product->cat_id = ($request->category_id) ? $request->category_id : $product->cat_id;
            $product->subcat_id = ($request->sub_category_id) ? $request->sub_category_id : $product->subcat_id; 
            $product->name = $request->product_name?$request->product_name:$request->product_name;
            $product->brand_name = ($request->brand_name) ? $request->brand_name : $product->brand_name;
            $product->model_name = ($request->model_name) ? $request->model_name : $product->model_name;
            $product->price = $request->product_price?$request->product_price:$product->price;
            $product->selling_price = ($request->product_selling_price) ? $request->product_selling_price : $product->selling_price;
            $product->gender = ($request->gender) ? $request->gender : $product->gender;
            $product->size = ($request->size) ? implode(",",$request->size) : $product->size;
            $product->color = ($request->color) ? $request->color : $product->color;
            $product->material = ($request->material) ? $request->material : $product->material;
            $product->wight = ($request->weight) ? $request->weight :  $product->wight;
            $product->is_gold = ($request->is_gold) ? $request->is_gold : $product->is_gold;
            $product->device_os = ($request->device_os) ? $request->device_os : $product->device_os;
            $product->ram = ($request->ram) ? $request->ram : $product->ram;
            $product->storage = ($request->storage) ? $request->storages : $product->storage;
            $product->connectivity = ($request->connectivity) ? $request->connectivity : $product->connectivity;
            $product->key_featurees = ($request->key_feature) ? implode(",",$request->key_feature) : $product->key_featurees;    
            $product->description = ($request->description) ? $request->description : $product->description;

           if($product->save()){
            // save Multiple images
                $images = $request->file('images');
                if ($images) {
                    $oldProductImages = ProductImage::where('product_id',$product->id)->get();
                    if(!empty($oldProductImages)){
                        foreach($oldProductImages as  $oldImage){
                            \File::delete('/public/images/product' . $product->id . "/".$oldImage->image);

                        }
                    }
                    ProductImage::where('product_id',$product->id)->delete();
                    foreach($images as $image){
                        $product_image = new ProductImage();
                        $product_image->product_id = $product->id;
                        $ext = $image->getClientOriginalExtension();
                        $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                        $destinationPath = '/images/product/' . $product->id;
                        /**create folder  **/
                        $destinationPath = base_path() . '/public/images/product/' . $product->id . "/";
                        if (!file_exists($destinationPath)) {
                            \File::makeDirectory($destinationPath, 0777, true);
                            chmod($destinationPath, 0777);
                        }
                        $image->move($destinationPath, $newFileName);
                        $product_image->image = $newFileName;
                        $product_image->save();
                    }
                    
                }
                // Save Product to all shops
                if($request->shop_id){
                    ShopProduct::where('product_id',$product->id)->delete();
                    foreach($request->shop_id as $shop_id){
                        $shop_product = new ShopProduct();
                        $shop_product->shop_id = $shop_id;
                        $shop_product->product_id = $product->id;
                        $shop_product->save();
                    }
                }



            }
            return redirect()->route('products')->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $product = Product::find($id);

            if(!empty($product)){
                $product->delete();
                $oldProductImages = ProductImage::where('product_id',$id)->get();
                if(!empty($oldProductImages)){
                    foreach($oldProductImages as  $oldImage){
                        \File::delete('/public/images/product' . $product->id . "/".$oldImage->image);
                        
                    }
                }
                ProductImage::where('product_id',$id)->delete();
                ShopProduct::where('product_id',$id)->delete();
                $response['status'] = true;
                $response['message'] = "Product Deleted successfully!";
                return $response;
            }
            $response['status'] = false;
            $response['message'] = "Product Not Found!";
            return $response;
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = "Something went wrong!";
        }
    }

    public function getShopByShopkeeper(Request $request)
    {
        $shopkeeper_id = $request->shopkeeper_id;
        $shops = Shop::where('user_id', $shopkeeper_id)->get();
        $html = '';
        //$html .= '<option value="">Please Shop</option>';
        if (count($shops) > 0) {
            foreach ($shops as $shop) { 
                $html .= '<option value="' . $shop->id . '">' . $shop->shop_name . '</option>';
            }
            return json_encode(array('status' => 1, 'html' => $html));
        } else {
            return json_encode(array('status' => 0, 'html' => $html));
        }
    }

    public function getSubcatByCategory(Request $request)
    {
        $category_id = $request->category_id;
        $sub_cats= SubCategory::where('category_id', $category_id)->get();
        $html = '';
        // /$html .= '<option value="">Please Sub Category</option>';
        if (count($sub_cats) > 0) {
            foreach ($sub_cats as $sub_cat) { 
                $html .= '<option value="' . $sub_cat->id . '">' . $sub_cat->name . '</option>';
            }
            return json_encode(array('status' => 1, 'html' => $html));
        } else {
            return json_encode(array('status' => 0, 'html' => $html));
        }
    }

}
