<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Validator;
use Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $responseHelper;

    public function __construct(
        ResponseHelper $responseHelper
    ) {
        $this->responseHelper = $responseHelper;
    }

    public function register(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'username' => 'required',   
                'email'    => 'required|email|unique:users',
                'role'     => 'required',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required'
            ]);

            if ($validator->fails()) {
                $message = $validator->messages()->first();
                $apiCode = Response::HTTP_FORBIDDEN;
                $apiStatus = false;
                $apiMessage = $message;
                $apiData = (object) [];

                return $this->responseHelper->error($apiCode, $apiStatus, $apiMessage, $apiData);
            }

            $user = new User();
            $user->username =  $request->username;
            $user->email =  $request->email;
            $user->password = Hash::make($request->password);
            $user->status = 1;
            if($user->save()){
                if($request->role == 'shop_keeper'){
                    $user->assignRole('shop_keeper');
                }else{
                    $user->assignRole('customer');
                }
                
              $response['code'] = 200;
              $response['status'] = true;
              $response['message'] = 'Register successful.';
              $response['data'] = $user;
              return response()->json($response);   
            }else{
                $apiCode = Response::HTTP_FORBIDDEN;
                $apiStatus = false;
                $apiMessage = 'Issue in regster please contact to admin';
                $apiData = (object) [];
                return $this->responseHelper->error($apiCode, $apiStatus, $apiMessage, $apiData);
            }

        } catch(\Exception $e){
            return $e;
        }
        
    }

    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                $message = $validator->messages()->first();
                $apiCode = Response::HTTP_FORBIDDEN;
                $apiStatus = false;
                $apiMessage = $message;
                $apiData = (object) [];
                return $this->responseHelper->error($apiCode, $apiStatus, $apiMessage, $apiData);
            }
            $email = User::where('email', $request->email)->first();
            if (!empty($email)) {
                if ($email->status == 0) {
                    $response['code'] = 403;
                    $response['status'] = false;
                    $response['message'] = 'Your not active.Please contact admin.';
                    //$response['data'] = (object) [];
                    return response()->json($response);
                }
                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password,
                    'status' => true,
                ];
            } else {
                $apiCode = Response::HTTP_FORBIDDEN;
                $apiStatus = false;
                $apiMessage = 'You are not register with system. Please contact administrator.';
                $apiData = (object) [];
                return $this->responseHelper->error($apiCode, $apiStatus, $apiMessage, $apiData);
            }
            if (Auth::attempt($credentials)) {
                //DB::table('oauth_access_tokens')->where('user_id', Auth::id())->update(['revoked' => 1]);
                $user = Auth::user();
                $token = $user->createToken('MyApp')->accessToken;
                $user->device_type = isset($request->device_type) ? $request->device_type : null;
                $user->device_token = isset($request->device_token) ? $request->device_token : null;
                $user->save();
                $user->role = $user->roles()->get();
                $data = $user->toArray();
                //$data['other_details'] = $this->extraDetails($role);
                //$data = $this->removeNullValue($data);   
                $response['code'] = 200;
                $response['status'] = true;
                $response['message'] = 'Login successful.';
                $response['data'] = $data;
                $response['token'] = $token;
                return response()->json($response);
            } else {
                $apiCode = Response::HTTP_FORBIDDEN;
                $apiStatus = false;
                $apiMessage = 'You have entered an invalid password.';
                $apiData = (object) [];

                return $this->responseHelper->error($apiCode, $apiStatus, $apiMessage, $apiData);
            }
        } catch (\Exception $e) {
            return $e;
        } 
    }
}
