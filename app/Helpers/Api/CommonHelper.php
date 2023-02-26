<?php

namespace App\Helpers\Api;

use Response;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\EmailHelper;
use Exception;
use URL;
use Artisan;
use DB;
use Mail;
use File;
use Auth;
use App\Models\Notification;
use App\Models\GuruRating;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CommonHelper
{
    private $responseHelper;
    private $EmailHelper;
    public function __construct(ResponseHelper $responseHelper, EmailHelper $EmailHelpers)
    {
        $this->responseHelper = $responseHelper;
        $this->EmailHelper = $EmailHelpers;
    }

    /*
          Function name : userdetails
          Description : common function to get login user data
          Developed by : Srushti Shah
          Date : 06/10/2022
    */
    public function userdetails()
    {
        $user = Auth::user();
        if ($user) {
            $path = config('constant.profile_image_path');
            $profile_image_type = config('constant.type.profile_image_type');
            /**get image url */
            $getImage = $this->getImages($user->id, $profile_image_type, $path);
            /**end get image url*/
            if ($getImage['status'] == 'true') {
                $image = $getImage['data'];
            } else {
                $path = config('constant.profile_image_path');
                $image_name = "profile.png";
                $image = $path . $image_name;
            }
            $user->profile = $image;
            return $user;
        } else {
            return false;
        }
    }

    /*
          Function name : imageUpload
          Description : common single image upload function
          Developed by : Pratik Prajapati
          Date : 04/08/2022
    */

    public function imageUpload($imageToUpload, $imageSavePath, $id, $type)
    {
        try {

            if (!file_exists(public_path($imageSavePath))) {
                mkdir(public_path($imageSavePath));
                chmod(public_path($imageSavePath), 0777);
            }
            $newImage = new MultipleFileUpload();
            if (is_array($imageToUpload)) {
                $imageResponse = [];
                foreach ($imageToUpload as $key => $image) {
                    $fileName = time() . rand(100, 1000) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path($imageSavePath), $fileName);
                    chmod(public_path($imageSavePath) . $fileName, 0777);
                    $newImage = new MultipleFileUpload();
                    $newImage->name = $fileName;
                    $newImage->media_id = $id;
                    $newImage->type = $type;
                    $newImage->save();
                    $imageResponse[] = asset('/public' . $imageSavePath . $fileName);
                }
            } else {
                $fileName = time() . rand(100, 1000) . '.' . $imageToUpload->getClientOriginalExtension();
                $imageToUpload->move(public_path($imageSavePath), $fileName);
                chmod(public_path($imageSavePath) . $fileName, 0777);
                $newImage = new MultipleFileUpload();
                $newImage->name = $fileName;
                $newImage->media_id = $id;
                $newImage->type = $type;
                $newImage->save();
                $imageResponse = asset('/public' . $imageSavePath . $fileName);
            }
            return $imageResponse;
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }

    /*
          Function name : imageWebUpload
          Description : common single image upload function
          Developed by : Srushti Shah
          Date : 17/10/2022
    */

    public function imageWebUpload($imageToUpload, $imageSavePath, $id, $type)
    {
        try {
            if (!file_exists(public_path($imageSavePath))) {
                mkdir(public_path($imageSavePath));
                chmod(public_path($imageSavePath), 0777);
            }
            $newImage = new MultipleFileUpload();
            if (is_array($imageToUpload)) {
                $imageResponse = [];
                foreach ($imageToUpload as $key => $image) {
                    $fileName = time() . rand(100, 1000) . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path($imageSavePath), $fileName);
                    chmod(public_path($imageSavePath) . $fileName, 0777);
                    $newImage = new MultipleFileUpload();
                    $newImage->name = $fileName;
                    $newImage->media_id = $id;
                    $newImage->type = $type;
                    $newImage->save();
                    $imageResponse[] = asset('/public' . $imageSavePath . $fileName);
                }
            } else {
                $fileName = time() . rand(100, 1000) . '.' . $imageToUpload->getClientOriginalExtension();
                $imageToUpload->move(public_path($imageSavePath), $fileName);
                chmod(public_path($imageSavePath) . $fileName, 0777);
                $newImage = new MultipleFileUpload();
                $newImage->name = $fileName;
                $newImage->media_id = $id;
                $newImage->type = $type;
                $newImage->save();
                $imageResponse = asset('/public' . $imageSavePath . $imageSavePath . $fileName);
            }
            return $imageResponse;
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }
    /*
        Function name : getImages
        Description : to get all image.
        Developed by : Pratik Prajapti
        Date : 07/10/2022
    */

    public function getImages($mediaId, $type, $path)
    {
        try {
            $query = MultipleFileUpload::where('type', $type)->where('media_id', $mediaId)->where('deleted_at', null);

            $imageCount = $query->count();

            if ($imageCount > 1 || ($imageCount > 0 && $type == 'feedImageBlogDocuments') || ($imageCount > 0 && $type == 'feedImageDocuments') || ($imageCount > 0 && $type == 'serviceDocuments') || ($imageCount > 0 && $type == 'serviceVideoDocuments') || ($imageCount > 0 && $type == 'howToListDocument'  || ($imageCount > 0 && $type == 'certificateDocuments'))) {
                $getImages = $query->get();

                $allImages = [];
                foreach ($getImages as $key => $image) {
                    $allImages[] = $image->getImageAttribute();
                }

                $response['status'] = true;
                $response['data'] = $allImages;

            }
            else if($imageCount > 0 && $imageCount == 1 ){
                $images = $query->first();
                $response['status'] = true;
                $response['data'] = $images->getImageAttribute();
            }
            else{
                $response['status'] = false;
            }
            return $response;
        } catch (Exception $e) {
            \Log::info($e);
            $response['status'] = false;
            return $response;
        }
    }

    /*
          Function name : cacheClear
          Description : for clearing cache
          Developed by : Srushti Shah
          Date : 29/09/2022
    */

    public function cacheClear()
    {
        try {
            $cacheClear =   [
                Artisan::call('config:clear'),
                Artisan::call('route:clear'),
                Artisan::call('view:clear'),
                Artisan::call('clear-compiled')
            ];

            return $cacheClear;
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }

    /*
          Function name : emailTemplate
          Description : for sendiang email
          Developed by : Srushti Shah
          Date : 29/09/2022
    */

    public function emailTemplate($templateName, $emailData)
    {
        try {
            $get_email_data = DB::table('email_templates')->where('name', $templateName)->first();
            if ($emailData) {
                if (isset($emailData['first_name'])) {
                    $emailData['name'] = $emailData['first_name'];
                }
                if (isset($emailData['subject'])) {
                    $emailData['subject'] = $emailData['subject'];
                }
                if (isset($emailData['email'])) {
                    $emailData['email'] = $emailData['email'];
                }

                if (isset($emailData['otp'])) {
                    $emailData['otp'] = $emailData['otp'];
                }

                if (isset($emailData['token'])) {
                    $emailData['token'] = $emailData['token'];
                }

                if (isset($emailData['reason_rejected'])) {
                    $emailData['reason_rejected'] = $emailData['reason_rejected'];
                }
                if (isset($emailData['guru_name'])) {
                    $emailData['guru_name'] = $emailData['guru_name'];
                }
                if (isset($emailData['service_name'])) {
                    $emailData['service_name'] = $emailData['service_name'];
                }
                if (isset($emailData['amount_per_session'])) {
                    $emailData['amount_per_session'] = $emailData['amount_per_session'];
                }
                if (isset($emailData['session_details'])) {
                    $emailData['session_details'] = $emailData['session_details'];
                }
                if (isset($emailData['total_amt'])) {
                    $emailData['total_amt'] = $emailData['total_amt'];
                }
                if (isset($emailData['customer_name'])) {
                    $emailData['customer_name'] = $emailData['customer_name'];
                }
                if (isset($emailData['user_name'])) {
                    $emailData['user_name'] = $emailData['user_name'];
                }
                if (isset($emailData['feed_name'])) {
                    $emailData['feed_name'] = $emailData['feed_name'];
                }

                $emailData['date'] = date('Y-m-d');
                $emailData['email_format'] = $get_email_data->body;
                return $this->EmailHelper->sendEmail($emailData);
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /*
          Function name : sendOtp
          Description : for sendiang OTP
          Developed by : Pratik Prajapati
          Date : 04/10/2022
    */
    public function sendOtp($templateName, $email = null, $phoneNumber = null, $subject)
    {
        try {
            $otp = mt_rand(100000, 999999);
            if (!empty($email)) {
                $templateName = $templateName;
                $emailData = array();
                $emailData['subject'] = $subject;
                $emailData['email'] = $email;
                $emailData['otp'] = $otp;
                /**start set email template */
                $sendmail =  $this->emailTemplate($templateName, $emailData);
                /**end set email template */

                if ($sendmail) {
                    $data['status'] = true;
                    $data['otp'] = $otp;
                } else {
                    $data['status'] = false;
                }
            } elseif (!empty($number)) {
                $data['status'] = true;
            }
            return $data;
        } catch (Exception $e) {
            $data['status'] = false;
            return $data;
        }
    }

    

    /*
        Function name : getPagination
        Description : This function used for set the pagination
        Developed by : Pratik Prajapati
        Date : 10/10/2022
    */
    public function getPagination($query, $limit, $page)
    {
        try {

            $getQuery = $query;
            $totalCount = $getQuery->count();
            $getAllData = $getQuery->limit($limit)->offset(($page - 1) * $limit)->get()->toArray();
            $response['data'] = $getAllData;
            $response['total_counts'] = $totalCount;
            $response['total_pages'] = $totalCount != 0 ? ceil($totalCount / $limit) : 0;
            return $response;
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }

    /*
        Function name : getSettingValue
        Description : This function used for get value from setting table
        Developed by : Srushti Shah
        Date : 18/10/2022
    */
    public function getSettingValue($id)
    {
        $setting_data = DB::table('settings')->where(array('id' => $id))->first();
        return $setting_data->value;
    }
    /*
        Function name : dateformat
        Description : This function used for date format
        Developed by : Rajesh Koladiya
        Date : 02/11/2022
    */
    public function getDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('m-d-Y');
        // return \Carbon\Carbon::parse($date)->format('m-d-Y H:i:s');
    }

    /*
        Function name : Gettagname
        Description : This function used for tagname
        Developed by : Rajesh Koladiya
        Date : 02/11/2022
    */
    public function tagName($tag)
    {
        $str = '';
        if ($tag) {
            foreach ($tag as $row) {
                $str .= $row['name'] . ',';
            }
            $str = trim($str, ',');
        }
        return $str;
    }

    public function getGuruRatings($guru_id)
    {
        $guruRatings = GuruRating::selectRaw('AVG(ratings) as guru_ratings')->where('guru_id', $guru_id)->groupBy('guru_id')->first();


        // $guruRatings = DB::table('guru_ratings')->where(array('guru_id' => $guru_id))->first();
        // dd($guruRatings);
        return $guruRatings;
    }
    public function calculate_discount($curr_user_details,$total_amt,$guru_id)
    {
       // pr_arr($curr_user_details);
        $guru_details = User::find($guru_id);
        $admin_commision_for_guru = $guru_details->guru_commission;
        $guru_commision_per = 100 - $admin_commision_for_guru;
        $discount_percentage=0;
        $discount_amount=0;
        if($curr_user_details->is_subscribe == 'true')
        {
            $discount_percentage=$this->getSettingValue(3);
            $guru_commision_amt=intval(($total_amt*$guru_commision_per)/100);
            $discount_amount=intval(($total_amt*$discount_percentage)/100);
        }
        else
        {
            $guru_commision_amt=intval(($total_amt*$guru_commision_per)/100);
        }
        $admin_amount=$total_amt-$guru_commision_amt-$discount_amount;

        $ret['total_amt']=$total_amt;
        $ret['admin_amount']=$admin_amount;

        $ret['discount_amount']=$discount_amount;
        $ret['discount_percentage']=$discount_percentage;

        $ret['guru_commision_per']=$guru_commision_per;
        $ret['guru_commision_amt']=$guru_commision_amt;

        $ret['pay_by_customer']=$total_amt-$discount_amount;
        $ret['admin_commision_for_guru'] = $admin_commision_for_guru;
        return $ret;
    }
    public function add_transaction($curr_user_details,$total_amt,$service_id,$guru_id)
    {
        $calculation=$this->calculate_discount($curr_user_details,$total_amt,$guru_id);
       // pr($calculation,1);

        // $wallet_amount=$curr_user_details->wallet_amount-$total_amt;
        // DB::table('users')->where('id',$curr_user_details->id)->update(array("wallet_amount"=>$wallet_amount));
        $payment = new Payment();
        $payment->service_id=$service_id;
        $payment->guru_id=$guru_id;
        $payment->user_id= $curr_user_details->id;
        $payment->amount=$total_amt;
        $payment->pay_by_customer = $calculation['pay_by_customer'];
        $payment->guru_percentage=$calculation['guru_commision_per'];
        $payment->guru_amount=$calculation['guru_commision_amt'];
        $payment->admin_percentage=$calculation['admin_commision_for_guru'];
        $payment->admin_amount=$calculation['admin_amount'];
        $payment->discount_percentage=$calculation['discount_percentage'];
        $payment->discount_amount	=$calculation['discount_amount'];
        $payment->transaction_type = 'booking';
        $payment->payment_status="success";
        $payment->date=date("Y-m-d H:i:s");
        if($payment->save())
        {
            //adding amount in guru's wallet and diduct amount from user

            $guru_data = User::find($guru_id);

            $guru_data->wallet_amount += $payment->guru_amount;
            $guru_data->save();

            $wallet_amount= $curr_user_details->wallet_amount-$payment->pay_by_customer;
            DB::table('users')->where('id',$curr_user_details->id)->update(array("wallet_amount"=>$wallet_amount));

            $calculation['payment_id']=$payment->id;
            return $calculation;
        }
        else
        {
            return 0;
        }
        // add_trasaction
    }
    public function pr_arr($arr)
    {
        echo "<pre>";
        print_R($arr->toArray());
        echo "</pre>";
    }

    public function getProfileImage($image)
    {
        if($image['status']== false)
        {
            $image['data'] = asset('/public/Images/avtar.png');

        }
        // dd($image['data']);
        return $image['data'];
    }

    public function sendNotification($title, $message, $type, $notification_payload, $icon_type, $send_by, $other_id, $user)
    {
        if ($user->id) {
            $notification = new Notification();
            $notification->user_id = $user->id;
            $notification->type = $type;
            $notification->message = $message;
            $notification->icon_type = $icon_type;
            $notification->send_by = $send_by;
            $notification->save();
        }
        Log::info('The device token is...'.$user->device_token);

        // echo "<hr>\ntitle~~>" . $title;
        // echo "<br>\nmessage~~>" . $message;
        // echo "<br>\ntype~~>" . $type;
        // echo "<br>\ndeviceIdsonly~~>" . $deviceIdsonly;
        // echo "<br>\ndeviceType~~>" . $deviceType;
        // echo "<br>\nnotification_payload~~>" . json_encode($notification_payload);
        // // return true;
        try {
            if (!empty($user->device_token) && !empty($user->device_type)) {
                $url = 'https://fcm.googleapis.com/fcm/send'; // put url link
                $server_key = 'AAAARQvlNa8:APA91bG8ENiVSlxL1mx8b3d2I9CZ0vg8vH-n1CKu1SNLlHLzy9RPDFHTjm0SOe1lh5TOA6FNwRZmIUIURWPgm6HTLiInfetDQIEk-7hU_s7AjVCmJt4oD-T_j2M__JxmoF8Mhj65ODvi'; // Server Key
                $fields = [];
                $fields['content_available'] = false;
                $fields['silent'] = true;
                if ('android' == $user->device_type) {
                    // Meaning Andorid
                    $fields['data'] = [];
                    $fields['data']['title'] = $title;
                    $fields['data']['body'] = $message;
                    $fields['data']['notification_data'] = $notification_payload;
                    $fields['data']['click_action'] = '.MainActivity';
                    $fields['data']['sound'] = 'default';
                    $fields['data']['type'] = $type;
                } elseif ('ios' == $user->device_type) {
                    // Meaning iOS
                    $fields['notification'] = [];
                    $fields['notification']['title'] = $message;
                    $fields['notification']['body'] = $title;
                    $fields['notification']['extra_support_message'] = $notification_payload;
                    $fields['notification']['click_action'] = '.MainActivity';
                    $fields['notification']['sound'] = 'default';
                    $fields['notification']['type'] = $type;
                }
                $fields['to'] = $user->device_token;
                $fields['priority'] = 'high';
                $headers = [
                    'Content-Type:application/json',
                    'Authorization:key='.$server_key,
                ];
                $fields = json_encode($fields);
                // print_r($fields);die;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                $result = curl_exec($ch);
                // echo "<br>Notification result";
                // print_R($result);
                Log::info('The output is...'.$result);
                if (false === $result) {
                    exit('Notification Send Error: '.curl_error($ch));
                }
                curl_close($ch);

                return $result;
            }

            return true;
        } catch (\Exception $e) {
            Log::info('The ERROR is...'.$e);

            return $this->responseHelper->error($e->getMessage());
        }
    }

    public function sendNotificationNew($title, $message, $type, $deviceIdsonly, $deviceType, $notification_payload, $image_url = "")
    {
        try {
            $url = "https://fcm.googleapis.com/fcm/send"; /* put url link */
            $server_key = "AAAARQvlNa8:APA91bG8ENiVSlxL1mx8b3d2I9CZ0vg8vH-n1CKu1SNLlHLzy9RPDFHTjm0SOe1lh5TOA6FNwRZmIUIURWPgm6HTLiInfetDQIEk-7hU_s7AjVCmJt4oD-T_j2M__JxmoF8Mhj65ODvi"; /* Server Key */
            $fields = array();
            $fields['content_available'] = false;
            $fields['silent'] = true;
            if ($deviceType == "android") {
                //Meaning Andorid
                $fields['data'] = array();
                $fields['data']['title'] = $title;
                if ($image_url != "") {
                    $fields['data']['image_url'] = $image_url;
                }
                $fields['data']['body'] = $message;
                $fields['data']['notification_data'] = $notification_payload;
                $fields['data']['click_action'] = '.MainActivity';
                $fields['data']['sound'] = 'default';
                $fields['data']['type'] = $type;
            } else if ($deviceType == "iOS") {
                //Meaning iOS
                $fields['notification'] = array();
                $fields['notification']['title'] = $message;
                $fields['notification']['body'] = $title;
                $fields['notification']['extra_support_message'] = $notification_payload;
                $fields['notification']['click_action'] = '.MainActivity';
                $fields['notification']['sound'] = 'default';
                $fields['notification']['type'] = $type;
            }
            $fields['registration_ids'] = $deviceIdsonly;
            $fields['priority'] = "high";
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key=' . $server_key,
            );
            $fields = json_encode($fields);
            // print_r($fields);die;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $result = curl_exec($ch);
            if ($result === false) {
                die('Notification Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
            return $result;
        } catch (Exception $e) {
            return $this->responseHelper->error($e->getMessage());
        }
    }
}
