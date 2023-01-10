<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Models\Favourite;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private $responseHelper;

    public function __construct(
        ResponseHelper $responseHelper,
        CommonHelper $commonHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }

    public function getDetails($id){
        try{
            $product = Product::where('id',$id)->first();
            if($product){
                $product_images = [];
                $productImages = ProductImage::where('product_id',$product->id)->get();
                    if(!empty($productImages)){
                        foreach($productImages as  $oldImage){
                            $product_images[] = url('/images/product/' . $product->id . "/".$oldImage->image);
                        }
                    }
                $product->product_images = $product_images;
                $product_shops = [];
                $productShops =  ShopProduct::where('product_id',$product->id)->get();
                if(!empty($productShops)){
                    foreach($productShops as  $oldShop){
                        $productShopName = Shop::select('shop_name')->where('id',$oldShop['shop_id'])->first();
                        if($productShopName){
                            $product_shops[] =  $productShopName->shop_name;
                        }
                    }
                }
                $product->product_shops = $product_shops;


            return $this->responseHelper->success('Product details successfully!',$product);
            }
            return $this->responseHelper->error(trans('Product Not Found!'));
        }catch (\Exception $e) {
                return $this->responseHelper->error('Something went wrong');
        }
        
    }

    //create Product API
    public function store(Request $request)
    {
        try {

        $validated = Validator::make($request->all(),[
            'shop_keeper_id' => 'required',
            'shop_id.*' => 'required',
            'product_name' => 'required',
            'product_price' => 'required|numeric|min:0|not_in:0',
            'images.*' => 'required|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
        ],['shop_id.*.required' => "Please select any one shop."]);
        if($validated->fails()){
            return $this->responseHelper->error($validated->errors()->first());
         }
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
                $product_images = [];
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
                        $product_images[] = url('/images/product/' . $product->id . "/".$product_image->image);
                    }
                    
                }
                $product->product_images = $product_images;

                $product_shops =  [];
                // Save Product to all shops
                if($request->shop_id){
                    foreach($request->shop_id as $shop_id){
                        $shop_product = new ShopProduct();
                        $shop_product->shop_id = $shop_id;
                        $shop_product->product_id = $product->id;
                        $shop_product->save();
                        $productShopName = Shop::select('shop_name')->where('id',$shop_id)->first();
                        if($productShopName){
                            $product_shops[] =  $productShopName->shop_name;
                        }
                    }

                }
                $product->product_shops = $product_shops;



            }
            return $this->responseHelper->success('Product added successfully!',$product);
        } catch (Exception $e) {
            return $this->responseHelper->error('Something went wrong');
        }
    }

    //update product api
    public function update(Request $request)
    {
        try {

        $validated = Validator::make($request->all(),[
            'shop_keeper_id' => 'required',
            'shop_id.*' => 'required',
            'product_name' => 'required',
            'model_name' => 'required',
            'product_price' => 'required|numeric|min:0|not_in:0',
            'images*' => 'required|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
        ],['shop_id.*.required' => "Please select any one shop."]);

         if($validated->fails()){
            return redirect()->back()->with('error', $validated->errors()->first());
         }


            $product = Product::where('id',$request->id)->first();
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
                $product_images = [];
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
                        $product_images[] = url('/images/product/' . $product->id . "/".$product_image->image);
                    }
                    
                }

                $product->product_images = $product_images;

                $product_shops =  [];
                // Save Product to all shops
                if($request->shop_id){
                    ShopProduct::where('product_id',$product->id)->delete();
                    foreach($request->shop_id as $shop_id){
                        $shop_product = new ShopProduct();
                        $shop_product->shop_id = $shop_id;
                        $shop_product->product_id = $product->id;
                        $shop_product->save();
                        $productShopName = Shop::select('shop_name')->where('id',$shop_id)->first();
                        if($productShopName){
                            $product_shops[] =  $productShopName->shop_name;
                        }
                    }
                }
                $product->product_shops = $product_shops;
            }
            return $this->responseHelper->success('Product updated successfully!',$product);
        } catch (\Exception $e) {
            return $this->responseHelper->error('Something went wrong');
        }
    }

    /**
     * delete product api
     */
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
                return $this->responseHelper->success(trans('Product Deleted successfully!'));

            }
            return $this->responseHelper->error(trans('Product Not Found!'));
        } catch (\Exception $e) {
            return $this->responseHelper->error('Something went wrong');
        }
    }

    public  function addToFavourite(Request $request){
        try{
            $isFavourite =  Favourite::where('user_id',Auth::user()->id)->where('product_id',$request->id)->first();
            if($isFavourite){
                $isFavourite->delete();
                return $this->responseHelper->success(trans('Product remove from  Favourite!'));
            }
            $isFavourite = new Favourite();
            $isFavourite->user_id = Auth::user()->id;
            $isFavourite->product_id = $request->id;
            $isFavourite->save();
            return $this->responseHelper->success(trans('Product added in your Favourite!'));

        }catch(\Exception $e){
            return $this->responseHelper->error('Something went wrong');
        }
    }

    public  function getFavouriteList(Request $request){
        try{
            $isFavouriteIds =  Favourite::where('user_id',Auth::user()->id)->pluck('product_id');
            if(empty($isFavouriteIds)){
                return $this->responseHelper->error(trans('Favourite product not Found!'));
            }
            $products =  Product::whereIn('id',$isFavouriteIds);
            $productsCount =  $products->count();
            if($productsCount <= 0){
                $response['products'] = [];
                return $this->responseHelper->success('Favourite product not Found!', $response);   
            }

                if(isset($request->page) && isset($request->pagination)){
                    $limit = isset($request->limit) ? $request->limit : 10;
                    $page = ($request->page > 0) ? $request->page : 1; 
                    $products = $products->limit($limit)->offset(($page - 1) * $limit)->get()->toArray();
    
                    $response['total_counts'] = $productsCount;
                    $response['total_pages'] = $productsCount != 0 ? ceil($productsCount / $limit) : 0;
    
                }else{
                    $products = $products->get()->toArray();
                }

                foreach($products as $key => $product){
                    $product_images = [];
                    $productImages = ProductImage::where('product_id',$product['id'])->get();
                        if(!empty($productImages)){
                            foreach($productImages as  $oldImage){
                                $product_images[] = url('/images/product/' . $product['id'] . "/".$oldImage->image);
                            }
                        }
                    $products[$key]['product_images'] = $product_images;
                    $product_shops = [];
                    $productShops =  ShopProduct::where('product_id',$product['id'])->get();
                    if(!empty($productShops)){
                        foreach($productShops as  $oldShop){
                            $productShopName = Shop::select('shop_name')->where('id',$oldShop['shop_id'])->first();
                            if($productShopName){
                                $product_shops[] =  $productShopName->shop_name;
                            }
                        }
                    }
                    $products[$key]['product_shops'] = $product_shops;
                }
                $response['products'] = $products;

                return $this->responseHelper->success('Products gets successfully', $response);                


        }catch(\Exception $e){
            return $this->responseHelper->error('Something went wrong');
        }
    }
}
