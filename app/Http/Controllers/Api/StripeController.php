<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Helpers\Api\StripeHelper;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Validator;
use Auth;
use Stripe;
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

    public function createEphemeralKey(){
        try {
            $stripe_key = config('constant.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $stripe = new \Stripe\StripeClient($stripe_key);
            
            $response = Stripe\EphemeralKey::create(['customer' => Auth::user()->stripe_customer_id ], ['stripe_version' => '2020-08-27']);
            //dd($data);
            return $this->responseHelper->success('Ephemeral Key created successful',$response);
        } catch (\Stripe\Exception\CardException $e) {
            dd($e);
            return $this->responseHelper->error('Something went wrong');
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            dd($e);
            return $this->responseHelper->error('Something went wrong');
        } catch (Exception $e) {
            dd($e);
            return $this->responseHelper->error('Something went wrong');
        }
    }

    public function getSubscription(){
        try {
            $stripe_key = config('constant.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $stripe = new \Stripe\StripeClient($stripe_key);
            
            // Set your secret key. Remember to switch to your live secret key in production.
            // See your keys here: https://dashboard.stripe.com/apikeys

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

    public function subscribeUser(Request $request)
     {   
        
         $validator = Validator::make($request->all(), [
             'price_id' => 'required',
             'card_id' => 'required',
         ]);
 
         if ($validator->fails()) {
            return $this->responseHelper->error($validator->messages()->first());
        }

        try {

            $checkUserSubscription =  UserSubscription::where('user_id', Auth::id())->first();
            if (!empty($checkUserSubscription)) {
                return $this->responseHelper->error('You already have active subscription');
            }
            $user_subscription = new UserSubscription();
            // $getDefaultPlan = SubscriptionPlan::where('id', $request->plan_id)->first();
            // if (!$getDefaultPlan) {
            //     return $this->responseHelper->error('Subscription plan not found');
            // }
            $metaData = array(
                "user_id" => Auth::user()->id,
            );

            $stripe_key = config('constant.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $stripe = new \Stripe\StripeClient($stripe_key);

            //Create payment link for subscription
            // $response = $stripe->paymentLinks->create([
            //     [
            //         'line_items' => [['price' => 'price_1MiMizSERxmiFRLSUPRmiDm9', 'quantity' => 1]],
            //     ],

            //   ]
            // );
            // dd($response);

            $response = Stripe\Subscription::create([
                'customer' => Auth::User()->stripe_customer_id,
                'items' => [
                    ['price' => $request->price_id],
                ],
                'default_payment_method' => $request->card_id,
                'metadata' => $metaData,
                'currency' => 'inr',
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            if($response){
            User::where('id', Auth::id())->update(['is_subscribe_user' => 1]);

            $user_subscription = new UserSubscription();
            //$user_subscription->subscription_id = $getDefaultPlan->id;
            $user_subscription->stripe_subscription_id = $response->id;
            $user_subscription->plan_stripe_id = $response->items->data[0]['price']['product'];
            $user_subscription->price_stripe_id = $response->items->data[0]['price']['id'];
            $user_subscription->is_current_subscription = 1;
            $user_subscription->user_id = Auth::id();
            //$user_subscription->amount = $getDefaultPlan->amount;
            $user_subscription->amount = $response->items->data[0]['price']['unit_amount']/100;
            $user_subscription->from_date = date('Y-m-d',  $response->current_period_start);
            $user_subscription->to_date = date('Y-m-d', $response->current_period_end);
            $subscription_data = $user_subscription->save();
            if($user_subscription->save()){
                $data  =  UserSubscription::find($user_subscription->id)->toArray();
                return $this->responseHelper->success('User Subscribe sucessfully',$data);
            }
            }else{
                return $this->responseHelper->error('something went wrong');
            }             

        }catch (\Stripe\Exception\CardException $e) {
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $this->responseHelper->error($e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            //Meaning customer does not exist
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $this->responseHelper->error($e->getMessage());
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $this->responseHelper->error($e->getMessage());
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $this->responseHelper->error($e->getMessage());
        }
         
     }

}
