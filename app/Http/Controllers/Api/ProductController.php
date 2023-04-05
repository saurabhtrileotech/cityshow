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
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private $responseHelper;
    private $commonHelper;


    public function __construct(
        ResponseHelper $responseHelper,
        CommonHelper $commonHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }


    public function productList(Request $request){
        try{
             $category_id = isset($request->category_id) ? $request->category_id : '';
             $user_id = isset($request->user_id) ? $request->user_id : '';
             $filter = isset($request->filter) ? $request->filter : '';
             $search_text = isset($request->searchText) ? $request->searchText : '';
             $products = Product::with('ProductImage');
             if(!empty($category_id )){
                $products = $products->where('cat_id',$category_id);
             }
             if(!empty($user_id)){
                $products = $products->where('shopkeeper_id',$user_id);
             }

            if(!empty($filter)){
                if($filter == 1){
                $products = $products->orderBy('selling_price','ASC'); 
                }
                else if($filter == 2){
                $products = $products->orderBy('selling_price','DESC');
                }
                else if($filter == 3){
                $products = $products->orderBy('id','DESC');
                }
                else if($filter == 4){
                $products = $products->orderBy('counter','DESC');
                }
            }
             
            if (!empty($search_text)) {
                $split_array = explode(' ', $search_text);
                $products->where(function ($q) use ($split_array) {
                    foreach ($split_array as $txt) {
                        $q->orWhere('name', 'like', '%' . $txt . '%');
                        $q->orWhereIn('id',function($query) use($txt){
                            $query->select('product_id')
                            ->from('shop_products')
                            ->whereIn('shop_id',function($sub_query) use($txt){
                                $sub_query->select('id')
                                ->from('shops')
                                ->where('shop_name', 'like', '%' . $txt . '%');  
                            });
                            
                        });
                    }
                });
            }


             $totalCount = $products->count();
         
          if($totalCount > 0){  
 
             if(isset($request->pagination) &&  $request->pagination != 'false'){
                 $limit = isset($request->limit) ? $request->limit : 10;
                 $page = ($request->page > 0) ? $request->page : 1; 
                 $products = $products->limit($limit)->offset(($page - 1) * $limit)->get()->toArray();
 
                 $response['products'] = $products;
                 $response['total_counts'] = $totalCount;
                 $response['total_pages'] = $totalCount != 0 ? ceil($totalCount / $limit) : 0;
 
                 return $this->responseHelper->success('Products gets successfully', $response);
             }else{
                 $products = $products->get()->toArray();
                 $response['products'] = $products;
 
                 return $this->responseHelper->success('Products gets successfully', $response);
             }
 
         }else{
             $response['products'] = [];
             return $this->responseHelper->success('No Products found', $response);
         }
 
         }
         catch(Exception $e){
             return $this->responseHelper->error($e->getMessage());
         }
 
     }

    public function getDetails($id){
        try{
            $product = Product::with('ProductImage','ProductShop','Shopkeeper')->where('id',$id)->first()->toArray();
            if($product){
                $product_data = Product::find($id);
                $product_data->counter = $product['counter'] + 1;
                $product_data->save();
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
            'product_type' => 'required',
            'product_price' => 'required|numeric|min:0|not_in:0',
            'product_selling_price' => 'required|numeric|min:0|not_in:0',
            'images.*' => 'required|mimes:jpg,jpeg,png,bmp,gif,svg,webp',
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
            $product->product_type = ($request->product_type) ? $request->product_type : null;
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
            $product->emi = ($request->emi) ? $request->emi : null;
            $product->gross_weight = ($request->gross_weight) ? $request->gross_weight : null;
            $product->certified_jwellery = ($request->certified_jwellery) ? $request->certified_jwellery : null;
            $product->installation = ($request->installation) ? $request->installation : null;
            $product->footwear_size = ($request->footwear_size) ? $request->footwear_size : null;
            $product->guaranty = ($request->guaranty) ? $request->guaranty : null;
            $product->live_demo = ($request->live_demo) ? $request->live_demo : null;

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
                        //$product_images[] = url('/images/product/' . $product->id . "/".$product_image->image);
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
                $shop =  Shop::where('user_id',$product->shopkeeper_id)->first();
                $pushNotificationData = [];
                $pushNotificationData['send_by']  = $product->shopkeeper_id;
                $pushNotificationData['other_id']  = $product->id;
                $pushNotificationData['type']  = "add_product";
                $pushNotificationData['title']  = "New Product added";
                $pushNotificationData['message']  = "New Arriavs added by";
                $pushNotificationData['notification_payload']  = $product;

                // $sendPushNotificationRequest = (new \App\Jobs\sendPushNotifications($this->commonHelper, $pushNotificationData));
                // dispatch($sendPushNotificationRequest);

                $device_tokens = User::select('device_token')->whereNotNull('device_token')->pluck('device_token')->toArray();
                if(!empty($device_tokens)){
                    //foreach($users as $user){
                        $title = "New Product added";
                        $message = "Hey, We have new arrivals in ".$product_shops[0].", Kindly check cityshow app";
                        $type = 'add_product';
                        $notification_payload = $product;
                        $device_type = 'android';
                        //$icon_type = $pushNotificationData['icon_type'];
                        //$send_by = $pushNotificationData['send_by'];
                        //$user = User::where('id', $user->id)->first();
                        $this->commonHelper->sendNotificationNew($title, $message, $type,$device_tokens, $device_type, $notification_payload, '');

                        $notification = new Notification(); 
                        $notification->product_id = $product->id;
                        $notification->type = $type;
                        $notification->title = $title;
                        $notification->message = $message;
                        $notification->icon_type = 'success';
                        $notification->send_by = Auth::user()->id;
                        $notification->save();
                    //}
                }

                // Save data in notification data


                //$product->product_shops = $product_shops;
                $product_data = Product::with('ProductImage','Product_Shop')->where('id',$product->id)->first()->toArray();
                return $this->responseHelper->success('Product added successfully!',$product_data);
            }
            
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->responseHelper->error('Something went wrong');
        }
    }

    //update product api
    public function update(Request $request)
    {
        //dd('hii');
        try {

        $validated = Validator::make($request->all(),[
            'shop_keeper_id' => 'required',
            'shop_id.*' => 'required',
            'product_name' => 'required',
            'product_type' => 'required',
            'model_name' => 'required',
            'product_price' => 'required|numeric|min:0|not_in:0',
            'product_selling_price' => 'required|numeric|min:0|not_in:0',
            'images*' => 'required|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
        ],['shop_id.*.required' => "Please select any one shop."]);

         if($validated->fails()){
            return $this->responseHelper->error($validated->errors()->first());
         }


            $product = Product::where('id',$request->id)->first();
            $product->shopkeeper_id  = $request->shop_keeper_id;
            //$product->shop_id = $request->shop_id;
            $product->cat_id = ($request->category_id) ? $request->category_id : $product->cat_id;
            $product->subcat_id = ($request->sub_category_id) ? $request->sub_category_id : $product->subcat_id; 
            $product->name = $request->product_name?$request->product_name:$request->product_name;
            $product->product_type = ($request->product_type) ? $request->product_type : null;
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
            $product->emi = ($request->emi) ? $request->emi : null;
            $product->gross_weight = ($request->gross_weight) ? $request->gross_weight : null;
            $product->certified_jwellery = ($request->certified_jwellery) ? $request->certified_jwellery : null;
            $product->installation = ($request->installation) ? $request->installation : null;
            $product->footwear_size = ($request->footwear_size) ? $request->footwear_size : null;
            $product->guaranty = ($request->guaranty) ? $request->guaranty : null;
            $product->live_demo = ($request->live_demo) ? $request->live_demo : null;
            $deletedImagesId = ($request->deletedImagesId) ? explode(",",$request->deletedImagesId) : [];
           if($product->save()){
            // delete existing images
            if(count($deletedImagesId) > 0){
                $oldProductImages = ProductImage::whereIn('id',$deletedImagesId)->get();
                if(!empty($oldProductImages)){
                    foreach($oldProductImages as  $oldImage){
                        \File::delete('/public/images/product' . $product->id . "/".$oldImage->image);
    
                    }
                }
                ProductImage::whereIn('id',$deletedImagesId)->delete();
            }
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
                $product_date = Product::with('ProductImage','ProductShop')->where('id',$request->id)->first()->toArray();
            }
            return $this->responseHelper->success('Product updated successfully!',$product_date);
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
            $isFavourite =  Favourite::where('user_id',Auth::user()->id)->where('product_id',$request->product_id)->first();
            if($isFavourite){
                $isFavourite->delete();
                return $this->responseHelper->success(trans('Product remove from  Favourite!'));
            }
            $isFavourite = new Favourite();
            $isFavourite->user_id = Auth::user()->id;
            $isFavourite->product_id = $request->product_id;
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
            $products =  Product::with('ProductImage','Product_Shop')->whereIn('id',$isFavouriteIds);
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

                // foreach($products as $key => $product){
                //     $product_images = [];
                //     $productImages = ProductImage::where('product_id',$product['id'])->get();
                //         if(!empty($productImages)){
                //             foreach($productImages as  $oldImage){
                //                 $product_images[] = url('/images/product/' . $product['id'] . "/".$oldImage->image);
                //             }
                //         }
                //     $products[$key]['product_images'] = $product_images;
                //     $product_shops = [];
                //     $productShops =  ShopProduct::where('product_id',$product['id'])->get();
                //     if(!empty($productShops)){
                //         foreach($productShops as  $oldShop){
                //             $productShopName = Shop::select('shop_name')->where('id',$oldShop['shop_id'])->first();
                //             if($productShopName){
                //                 $product_shops[] =  $productShopName->shop_name;
                //             }
                //         }
                //     }
                //     $products[$key]['product_shops'] = $product_shops;
                // }
                $response['products'] = $products;

                return $this->responseHelper->success('Products gets successfully', $response);                


        }catch(\Exception $e){
            return $this->responseHelper->error('Something went wrong');
        }
    }
}
