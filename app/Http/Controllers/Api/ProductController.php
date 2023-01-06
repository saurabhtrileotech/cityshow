<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Models\ShopProduct;
use Illuminate\Http\Request;

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
}
