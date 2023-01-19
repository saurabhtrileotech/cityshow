<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Models\User;
use Validator;
use Auth;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $responseHelper;

    public function __construct(
        ResponseHelper $responseHelper,
        CommonHelper $commonHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }

    public function register(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'username' => 'required',   
                'email'    => 'required|email|unique:users',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required'
            ]);

            if ($validator->fails()) {
                $message = $validator->messages()->first();

                return $this->responseHelper->error($validator->messages()->first());
            }

            $user = new User();
            $user->first_name =  isset($request->first_name) ? $request->first_name : null;
            $user->lastname =  isset($request->last_name) ? $request->last_name : null;
            $user->username =  $request->username;
            $user->email =  $request->email;
            $user->password = Hash::make($request->password);
            $user->status = 1;
            $user->address =  isset($request->address) ? $request->address : null;
            $profile_pic = $request->file('profile_pic');   
            if ($profile_pic) {
                $ext = $profile_pic->getClientOriginalExtension();
                $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/profile_pic/';
                /**create folder  **/
                $destinationPath = base_path() . '/public/profile_pic/';
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $profile_pic->move($destinationPath, $newFileName);
                $user->profile_pic = $newFileName;
            }
            if($user->save()){
                $user->assignRole('shop_keeper');

                // get user profile
                $user = User::find($user->id);
                return $this->responseHelper->success('User created successfully',$user);
            }else{
                return $this->responseHelper->error('Issue in regster please contact to admin');
            }

        } catch(\Exception $e){ 
            return $this->responseHelper->error('Something went wrong');
        }
        
    }

    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseHelper->error($validator->messages()->first());
            }
            $email = User::where('email', $request->email)->first();
            if (!empty($email)) {
                if ($email->status == 0) {
                    return $this->responseHelper->error('You are not active.Please contact admin.');
                }
                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password,
                    'status' => true,
                ];
            } else {
                return $this->responseHelper->error('You are not register with system. Please contact administrator.');
            }
            if (Auth::attempt($credentials)) {
                //DB::table('oauth_access_tokens')->where('user_id', Auth::id())->update(['revoked' => 1]);
                $user = Auth::user();
                $token = $user->createToken('MyApp')->accessToken;
                $user->device_type = isset($request->device_type) ? $request->device_type : null;
                $user->device_token = isset($request->device_token) ? $request->device_token : null;
                $user->save();
                $user->role = $user->roles()->get()->toArray();
                //$user->profile_pic = $user->profile_pic?url('/public/profile_pic/'.$user->profile_pic):"";
                $data = $user->toArray();
                //$data['other_details'] = $this->extraDetails($role);
                //$data = $this->removeNullValue($data);  
                $response['user'] = $data;
                $response['token'] = $token;
                return $this->responseHelper->success('User created successfully',$response);

            } else {
                $response = (object)array();
                return $this->responseHelper->success('You have entered an invalid password.',$response);
            }
        } catch (\Exception $e) {
            return $this->responseHelper->error('Something went wrong');
            
        } 
    }


     /*
      Function name : forgotPassword
      Description : to forgot password
    */
    public function sendForgotPasswordOtp(Request $request)
    {

        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email|exists:users',
                ]
            );
            if ($validator->fails()) {
                return $this->responseHelper->error($validator->messages()->first());
            }
            $subject =  trans('OTP for forgot password');
            $templateName = "forgot_password";
            /** start  send OTP by email */
            $sendOtp =  $this->commonHelper->sendOtp($templateName, $request->email, null, $subject);
            /**end send OTP by email */
            if ($sendOtp['status'] == true) {
                $user = User::where('email', $request->email)->first();
                $user->otp = $sendOtp['otp'];
                $user->update();
                return $this->responseHelper->success(trans('Forgot password OTP send on your mail'), $user);
            } else {
                return $this->responseHelper->error(trans('Something went wrongs'));
            }
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }


        /*
      Function name : verifyOtp
      Description : to verify otp
    */
    public function verifyOtp(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'otp' => 'required',
                ]
            );
            if ($validator->fails()) {
                return $this->responseHelper->error($validator->messages()->first());
            }
            //check user or otp is valid
            $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
            if ($user) {
                $user->otp = null;
                //$user->is_verified_otp = 1;
                $user->update();
                //$is_subscribe = $user->where('is_subscribe', 1)->count();
               // $is_subscribe > 0 ? $user['is_subscribe'] = "true" : $user['is_subscribe'] = "false";
                return $this->responseHelper->success(trans('api_messages.success.OTP_VERIFY_SUCCESSFULLY'), $user);
            } else {
                return $this->responseHelper->error(trans('api_messages.error.WRONG_OTP'));
            }
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }

    /*
      Function name : forgotPassword
      Description : to forgot Password
    */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'password' => 'required|min:6|max:10|confirmed',
                    'password_confirmation' => 'required|min:6|max:10'
                ]
            );
            if ($validator->fails()) {
                return $this->responseHelper->error($validator->messages()->first());
            }
            $user = user::where('email', $request->email)->first();
            if ($user) {
                $user->password = Hash::make($request->password);
                if ($user->update()) {
                    $response = (object)[];
                    return $this->responseHelper->success(trans('api_messages.success.PASSWORD_UPDATED_SUCCESSFULLY'),$response);
                } else {
                    return $this->responseHelper->error(trans('api_messages.error.SOMETHING_WENT_WRONG'));
                }
            } else {
                return $this->responseHelper->error(trans('api_messages.error.USER_NOT_FOUND'));
            }
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $getUser = User::where('id', Auth::user()->id)->first();
            if (!$getUser) {
                return $this->responseHelper->error(trans('api_messages.error.USER_NOT_FOUND'));
            }
            // $validator = Validator::make($request->all(), [
            //     'username' => 'required',
            // ]);
            // if ($validator->fails()) {
            //     return $this->responseHelper->error($validator->messages()->first());
            // }

            $getUser->first_name = isset($request->first_name) ? $request->first_name : $getUser->first_name;
            $getUser->lastname = isset($request->last_name) ?  $request->last_name : $getUser->last_name;
            $getUser->email = isset($request->email) ?  $request->email : $getUser->email;
            $getUser->address = isset($request->address) ?  $request->address : $getUser->address;

            $profile_pic = $request->file('profile_picture');
            if ($profile_pic) {
                $ext = $profile_pic->getClientOriginalExtension();
                $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/profile_pic/';
                /**create folder  **/
                $destinationPath = base_path() . '/public/profile_pic/';
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $profile_pic->move($destinationPath, $newFileName);
                $getUser->profile_pic = $newFileName;
            }

            if ($getUser->update()) {
                return $this->responseHelper->success(trans('api_messages.success.USER_PROFILE_UPDATED'), $getUser);
            } else {
                return $this->responseHelper->error(trans('api_messages.error.SOMETHING_WENT_WRONG'));
            }
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }
}
