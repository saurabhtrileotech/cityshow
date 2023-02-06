<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Models\User;
use App\Models\Discount;
use App\Models\Shop;
use App\Models\ShopDiscount;
use Exception,Auth;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    
    private $responseHelper;

    public function __construct(
        ResponseHelper $responseHelper,
        CommonHelper $commonHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'coupon_name' => 'required',
            'coupon_code' => 'required',
            'discount' => 'required',
            'product_id' => 'sometimes',
            'shop_id' => 'sometimes',
            'image' => 'sometimes'
        ]);

        if ($validated->fails()) {
            return $this->responseHelper->error($validated->errors()->first());
        }
        try {
            if($request->id){
                $discount = Discount::find($request->id);
            }else{
                $discount = new Discount();
            }
            $discount->shop_keeper_id = Auth::user()->id;
            $discount->coupon_name = $request->coupon_name;
            $discount->coupon_code = $request->coupon_code;
            $discount->is_price = $request->is_price;
            $discount->discount =  $request->discount;
            $discount->notes =  ($request->notes) ? $request->notes : null;

            $discount->start_date = \Carbon\Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
            $discount->end_date = \Carbon\Carbon::parse($request->end_date)->format('Y-m-d H:i:s');

            $image = $request->file('image');
            if ($image) {
                $ext = $image->getClientOriginalExtension();
                $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/discount_image/';
                /**create folder  **/
                $destinationPath = base_path() . '/public/discount_image/';
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $image->move($destinationPath, $newFileName);
                $discount->image = $newFileName;
            }

            if ($discount->save()) {
             // if edit discount then delete shop_discounts for that coupon
             if($request->id){
                ShopDiscount::where('discount_id',$discount->id)->delete();
             }
                if($request->shop_id){
                    $shops = explode(",",$request->shop_id);
                    if (count($shops) > 0) {
                        foreach ($shops as $shop_id) {
                            $shop_discount = new ShopDiscount();
                            $shop_discount->shop_id = $shop_id;
                            $shop_discount->discount_id = $discount->id;
                            $shop_discount->save();
                        }
                    }
                }
                if($request->product_id){
                    $products = explode(",",$request->product_id);
                    if (count($products) > 0) {
                        foreach ($products as $product_id) {
                            $shop_discount = new ShopDiscount();
                            $shop_discount->product_id = $product_id;
                            $shop_discount->discount_id = $discount->id;
                            $shop_discount->save();
                        }
                    }
                }
                return $this->responseHelper->success('Discount saved successfully!',$discount);
            }else{
                return $this->responseHelper->error('Something went wrong');
            }
            
        } catch (Exception $e) {
            return $this->responseHelper->error('Something went wrong');
        }
    }

    public function discountList(Request $request){
        try{

            $user_id = isset($request->user_id) ? $request->user_id : '';
            $discounts = Discount::select('*');
            if(!empty($user_id)){
               $discounts = $discounts->where('shop_keeper_id',$user_id);
            }
            $totalCount = $discounts->count();
        
         if($totalCount > 0){

            if(isset($request->pagination) ||  $request->pagination != 'false'){
                $limit = isset($request->limit) ? $request->limit : 10;
                $page = ($request->page > 0) ? $request->page : 1; 
                $discounts = $discounts->limit($limit)->offset(($page - 1) * $limit)->get()->toArray();

                $response['discounts'] = $discounts;
                $response['total_counts'] = $totalCount;
                $response['total_pages'] = $totalCount != 0 ? ceil($totalCount / $limit) : 0;

                return $this->responseHelper->success('Discounts gets successfully', $response);
            }else{
                $shops = $discounts->get()->toArray();
                $response['discounts'] = $discounts;

                return $this->responseHelper->success('Discounts gets successfully', $response);
            }

        }else{
            $response['discounts'] = [];
            return $this->responseHelper->success('No Discounts found', $response);
        }

        }
        catch(Exception $e){
            return $this->responseHelper->error($e->getMessage());
        } 
    }

    public function getDetails($id){
        try{
            $discount = Discount::with('DiscountProducts.ProductImage','DiscountShop.shopImages')->where('id',$id)->first()->toArray();
            if($discount){
                return $this->responseHelper->success('Discount details successfully!',$discount);
            }
            return $this->responseHelper->error('Discount Not Found!');
        }catch (\Exception $e) {
            return $this->responseHelper->error('Something went wrong');
        }
    }
    
    
}
