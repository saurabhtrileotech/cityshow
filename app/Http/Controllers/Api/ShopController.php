<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Models\Shop;
use App\Models\ShopImage;
use Validator;
use Auth;
use Exception;

class ShopController extends Controller
{
    private $responseHelper;

    public function __construct(
        ResponseHelper $responseHelper,
        CommonHelper $commonHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }

    public function shops(Request $request){
       try{
      
            $shops = Shop::with('shop_images','products')->where('user_id',Auth::user()->id)->where('status',1);
            $totalCount = $shops->count();
        
         if($totalCount > 0){

            if($request->pagination == 'true'){
                $limit = isset($request->limit) ? $request->limit : 10;
                $page = ($request->page > 0) ? $request->page : 1; 
                $shops = $shops->limit($limit)->offset(($page - 1) * $limit)->get()->toArray();

                $response['shops'] = $shops;
                $response['total_counts'] = $totalCount;
                $response['total_pages'] = $totalCount != 0 ? ceil($totalCount / $limit) : 0;

                return $this->responseHelper->success('Shops gets successfully', $response);
            }else{
                $shops = $shops->get()->toArray();
                $response['shops'] = $shops;

                return $this->responseHelper->success('Shops gets successfully', $response);
            }

        }else{
            $response['shops'] = [];
            return $this->responseHelper->success('No shops found', $response);
        }

        }
        catch(Exception $e){
            return $this->responseHelper->error($e->getMessage());
        }

    }

    public function shopAdd(Request $request){
        $validator = Validator::make($request->all(), [
            'shop_name' => 'sometimes|required',
            'banner_image' => 'sometimes|required|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
            'category' => 'sometimes|required',
            'notes'   => 'sometimes|required',
            'address'  => 'sometimes|required',
            'banner_video' => 'sometimes|required',
            'images.*' => 'sometimes|required|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',                  
        ]);
        if ($validator->fails()) {
            $message = $validator->messages()->first();
            return $this->responseHelper->error($validator->messages()->first());
        }

        try {
            if(isset($request->id))
            {
                $shop = Shop::find($request->id);
                if($shop == null){
                    return $this->responseHelper->error('something went wrong');
                }
            }else{
                $shop = new Shop();
            }

            $shop->user_id  = Auth::user()->id;
            $shop->shop_name = ($request->shop_name) ? $request->shop_name : '';
            if(isset($request->category)){
                $shop->category_id =  $request->category;
            }
            if(isset($request->notes)){
                $shop->notes =  $request->notes;
            }
            if(isset($request->address)){
                $shop->address =  $request->address;
            }
            if(isset($request->latitude)){
                $shop->latitude =  $request->latitude;
            }
            if(isset($request->longitude)){
                $shop->longitude =  $request->longitude;
            }
            $banner_image = $request->file('banner_image');
            if ($banner_image) {
                $ext = $banner_image->getClientOriginalExtension();
                $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/banner_image/' . Auth::user()->id;
                /**create folder  **/
                $destinationPath = base_path() . '/public/banner_image/' . Auth::user()->id . "/";
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $banner_image->move($destinationPath, $newFileName);
                $shop->banner = $newFileName;
            }
            $banner_video = $request->file('banner_video');
            if ($banner_video) {
                $ext = $banner_video->getClientOriginalExtension();
                $newVideo = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/banner_video/' . $request->shop_keeper_id;
                /**create folder  **/
                $destinationPath = base_path() . '/public/banner_video/' . $request->shop_keeper_id . "/";
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $banner_video->move($destinationPath, $newVideo);
                $shop->video = $newVideo;
            }

            $shop->save();


            // if update the shop images then delete all images
            ShopImage::where('shop_id',$request->id)->delete();

            // save Multiple images
            $images = $request->file('images');
            if ($images) {
                foreach($images as $image){
                    $shop_image = new ShopImage();
                    $shop_image->shop_id = $shop->id;
                    $ext = $image->getClientOriginalExtension();
                    $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                    $destinationPath = '/images/' . $shop->id;
                    /**create folder  **/
                    $destinationPath = base_path() . '/public/images/' . $shop->id . "/";
                    if (!file_exists($destinationPath)) {
                        \File::makeDirectory($destinationPath, 0777, true);
                        chmod($destinationPath, 0777);
                    }
                    $image->move($destinationPath, $newFileName);
                    $shop_image->image = $newFileName;
                    $shop_image->save();

                    $shop->shop_images = $shop->shop_images;
                }
                
            }
            $shop_data = Shop::with('shop_images')->where('id',$shop->id)->first()->toArray();
            return $this->responseHelper->success('Shop added successfully!', $shop_data);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

   
}
