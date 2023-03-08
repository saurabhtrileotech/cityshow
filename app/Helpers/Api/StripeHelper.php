<?php

namespace App\Helpers\Api;

use Illuminate\Http\Request;
use Stripe;
use Illuminate\Support\Facades\Validator;
use Exception;
use  Auth;
use app\Models\User;

class StripeHelper
{
    public function CreateCustomer($email)
    {
        try {
            
            $errors = [];
            $success = [];
            $checkIfUserExists = User::where('email', $email)->first();
            $stripe_key = config('constant.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            //check if user exist
            if ($checkIfUserExists) {
                //check if customer id exists
                if (!empty($checkIfUserExists->stripe_customer_id)) {
                    //Check if the customer really exists..
                    $checkIfUserExistOverStripe = Stripe\Customer::retrieve(
                        $checkIfUserExists->stripe_customer_id
                    );
                    //Means its valid customer ( else check errors will create a customer)
                    $success['code'] = 200;
                    $success['message'] = "Customer created successfully";
                    return $success;
                } else {
                    
                    //Create a new customer...
                    $response = Stripe\Customer::create([
                        'email' => $email
                    ]);
                    User::whereId($checkIfUserExists->id)->update(['stripe_customer_id' => $response['id']]);
                }
                // $response = Stripe\Customer::create([
                //     'email' => $request->email
                // ]);
                // User::whereId($checkIfUserExists->id)->update(['stripe_customer_id' => $response->id]);
            }
        } catch (\Stripe\Exception\CardException $e) {
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (\Stripe\Exception\InvalidRequestException $e) {

            //Meaning customer does not exist
            if ($e->getHttpStatus() == 404 && $e->getError()->code == "resource_missing") {
                $response = Stripe\Customer::create([
                    'email' => $email
                ]);
                User::whereId($checkIfUserExists->id)->update(['stripe_customer_id' => $response->id]);
                $success['code'] = 200;
                $success['message'] = "Customer created successfully";
                return $success;
            }
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        }
    }

    public function getAllProjects(){
        try {
            $errors = [];
            $success = [];
            $stripe_key = config('constant.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $stripe = new \Stripe\StripeClient($stripe_key);
            $products = $stripe->products->all(['limit' => 3]);
            $success['products'] = $products->data;  
            $success['code'] = 200;
            $success['message'] = "Subscription list get successful";
            return $success;
        } catch (\Stripe\Exception\CardException $e) {
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            //Meaning customer does not exist
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        }
    }

    public function createCard($data)
    {
        try {
            $errors = [];
            $success = [];
            $stripe_key = config('constants.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $createCardToken = Stripe\Token::create([
                'card' => [
                    'number' => $data['number'],
                    'exp_month' => $data['exp_month'],
                    'exp_year' => $data['exp_year'],
                    'cvc' => $data['cvc'],
                ],
            ]);
            Stripe\Customer::createSource(
                $data['customer_id'],
                ['source' => $createCardToken]
            );
            $success['code'] = 200;
            $success['message'] = "New payment method created successfully";
            return $success;
        } catch (\Stripe\Exception\CardException $e) {
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            //Meaning customer does not exist
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        }
    }

    public function setCardAsDefault($defaultArray)
    {
        try {
            $errors = [];
            $success = [];
            $stripe_key = config('constants.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $customerId = $defaultArray['stripe_customer_id'];
            $cardId = $defaultArray['card_id'];

            Stripe\Customer::update(
                $customerId,
                ['default_source' => $defaultArray['card_id']]
            );

            $success['code'] = 200;
            $success['message'] = "Card marked as default";
            return $success;
        } catch (\Stripe\Exception\CardException $e) {

            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (\Stripe\Exception\InvalidRequestException $e) {

            //Meaning customer does not exist
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {

            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        }
    }


    public function getAllCards($customerId)
    {
        try {
            $errors = [];
            $success = [];
            $card = [];
            $stripe_key = config('constants.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $getListOfCards = Stripe\Customer::allSources(
                $customerId
            );


            $getCustomerDetails = Stripe\Customer::retrieve(
                $customerId
            );

            if (!empty($getListOfCards->data)) {
                foreach ($getListOfCards->data as $key => $singleCard) {


                    $card[$key]['card_id'] = $singleCard['id'];
                    $card[$key]['brand'] = $singleCard['brand'];
                    $card[$key]['exp_month'] = $singleCard['exp_month'];
                    $card[$key]['exp_year'] = $singleCard['exp_year'];
                    $card[$key]['funding'] = $singleCard['funding'];
                    $card[$key]['country'] = $singleCard['country'];
                    $card[$key]['last_digits'] = $singleCard['last4'];
                    if ($getCustomerDetails->default_source == $singleCard['id']) {
                        $card[$key]['default'] = "true";
                    } else {
                        $card[$key]['default'] = "false";
                    }
                }
            }


            $success['code'] = 200;
            $success['message'] = "Card retrieved successfully";
            $success['data'] = $card;

            return $success;
        } catch (\Stripe\Exception\CardException $e) {
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            //Meaning customer does not exist
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        }
    }

    public function deleteCard($deleteCardArguments)
    {
        try {
            $stripe_key = config('constants.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);
            $deleteCard = Stripe\Customer::deleteSource(
                $deleteCardArguments['stripe_customer_id'],
                $deleteCardArguments['card_id']
            );
            $success['code'] = 200;
            $success['message'] = "Card deleted successfully";
            return $success;
        } catch (\Stripe\Exception\CardException $e) {
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            //Meaning customer does not exist
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        }
    }


    public function subscribeUser($data)
    {
        try {

            // $validator = Validator::make($request->all(), [
            //     'price_id' => 'required',
            //     'plan_id' => 'required',
            //     'card_id' => 'required',

            // ]);
            // if ($validator->fails()) {
            //     return redirect()->back()->with('errors', $validator->errors());
            // }
            //plan_id
            //price_id
            //stripe_customer_id
            $stripe_key = config('constants.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);

            $metaData = array(
                "user_id" => Auth::user()->id,
                "plan_id" => $data['plan_id'],
                //"prod_MwyEQXVlyjAF77"

            );

            $result = Stripe\Subscription::create([
                'customer' => $data['stripe_customer_id'],
                //'cus_My4ASKFkneBGdP',
                'items' => [
                    [
                        'price' => $data['price_id'],
                        //'price_1MD4HrGEz40KZWYeprZYPxm6'
                    ],
                ],
                'metadata' => $metaData
                // 'default_payment_method' => "card_1ME8SEGEz40KZWYe1sCWkoxC"
                //$request->card_id,
            ]);
            $data = [];
            $data['stripe_subscription_id'] = $result->id;
            $data['current_period_start'] = $result->current_period_start;
            $data['current_period_end'] = $result->current_period_end;

            $success['code'] = 200;
            $success['message'] = "User subscribed successfully";
            $success['data'] = $data;
            return $success;
        } catch (\Stripe\Exception\CardException $e) {
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            //Meaning customer does not exist
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        }
    }

    public function charge($amount){
        try{
            $errors = [];
            $success = [];
            $card = [];
            $stripe_key = config('constants.STRIPE_PUBLIC_KEY');
            \Stripe\Stripe::setApiKey($stripe_key);

            $LoggedInUser = Auth::user();
            $getCustomerDetails = Stripe\Customer::retrieve(
                $LoggedInUser->stripe_customer_id
            );
            if(!empty($getCustomerDetails)){
               $chargeDetails =  Stripe\charge::create([
                        'customer' => $LoggedInUser->stripe_customer_id,
                        'amount' => $amount,
                        'currency' => 'usd',
                        'source' => $getCustomerDetails->default_source,
                        'description' => 'My First Top up Charge ',
                    ]);

                $data = array(
                    'tran_id' => $chargeDetails->balance_transaction,
                );

                $success['code'] = 200;
                $success['message'] = "User Top up successfully";
                $success['data'] = $data;
                return $success;  
            }
            
        } catch (\Stripe\Exception\CardException $e) {
           
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            //Meaning customer does not exist
            
            $errors['code'] = $e->getHttpStatus();
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        } catch (Exception $e) {
            
            $errors['code'] = 400;
            $errors['message'] = $e->getMessage();
            return $errors;
        }
    }
}
