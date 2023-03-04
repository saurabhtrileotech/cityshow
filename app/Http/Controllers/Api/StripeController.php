<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Helpers\Api\StripeHelper;

class StripeController extends Controller
{
    private $responseHelper;

    public function __construct(
        ResponseHelper $responseHelper,
        CommonHelper $commonHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }

    public function getSubscription(){
        try {
            $stripe_key = config('constant.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $stripe = new \Stripe\StripeClient($stripe_key);
            $products = $stripe->products->all();
            $data['plans'] = $products->data;
            foreach($products->data as $price)
            {
               $price_data =  $stripe->prices->retrieve(
                    $price->default_price,
                  ); 
               $price->price_data   = ($price_data) ?  $price_data : [];
            }
            //dd($data);
            return $this->responseHelper->success('Subscription list get successful',$data);
        } catch (\Stripe\Exception\CardException $e) {
            return $this->responseHelper->error('Something went wrong');
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return $this->responseHelper->error('Something went wrong');
        } catch (Exception $e) {
            return $this->responseHelper->error('Something went wrong');
        }
    }
}
