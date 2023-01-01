<?php

namespace App\Helpers\Web;

use Response;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use Illuminate\Support\Facades\Log;
use Exception;

class NotificationHelper
{
    private $responseHelper;
    public function __construct(ResponseHelper $responseHelper, CommonHelper $commonHelper)
    {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }
    /*
        Function name : Send Notification
        Description :send notification
        Developed by : Rajesh Koladiya
        Date : 01/11/2022
    */
    // public  function sendFCM($title, $message, $target = 0, $deviceIdsonly, $notification_payload)
    // {
    //     try{
    //         $url = 'https://fcm.googleapis.com/fcm/send';
    //         $server_key = $this->commonHelper->getSettingValue(5);
    //         $fields = array();
    //         $fields['content_available'] = true;
    //         $fields['data'] = array();
    //         $fields['data']['body'] = $message;
    //         $fields['data']['title'] = $title;
    //         $fields['data']['notification_payload'] = $notification_payload;
    //         $fields['data']['click_action'] = '.MainActivity';
    //         $fields['data']['sound'] = 'default';
    //         $fields['data']['notification_id'] = $target;
    //         $fields['to'] = $deviceIdsonly;
    //         $fields['priority'] = "high";
    //         $headers = array(
    //             'Content-Type:application/json',
    //             'Authorization:key=' . $server_key,
    //         );
    //         $fields = json_encode($fields);
    //         // print_r($fields);die;

    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, $url);
    //         curl_setopt($ch, CURLOPT_POST, true);
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    //         $result = curl_exec($ch);
    //         if ($result === false) {
    //             die('FCM Send Error: ' . curl_error($ch));
    //         }
    //         print_r($result);
    //         exit;
    //         curl_close($ch);
    //         return $result;
    //     } catch (Exception $e) {
    //         dd($e);
    //         return $this->responseHelper->error($e->getMessage());
    //     }
    // }

    public function sendFCM($title, $message, $type, $deviceIdsonly, $deviceType, $notification_payload)
    {
        /*
            Helper Function name : sendFCM
            Description : for sending the notification
            Developed by : srushti shah
            Date : 03/08/2022
        */
    try {
        // dd($notification_payload);
        $url = "https://fcm.googleapis.com/fcm/send";
        $server_key = $this->commonHelper->getSettingValue(5);
        $fields = array();
        $fields['content_available'] = false;
        $fields['silent']= false;
        if ($deviceType == "android") {
            //Meaning Andorid
            $fields['data'] = array();
            $fields['data']['title'] = $title;
            $fields['data']['body'] = $message;
            $fields['data']['notification_data'] = $notification_payload;
            $fields['data']['click_action'] = '.MainActivity';
            $fields['data']['sound'] = 'default';
            $fields['data']['notification_type'] = $type;

        } else if ($deviceType == "ios") {
            //Meaning iOS
            $fields['notification'] = array();
            $fields['notification']['title'] = $message;
            $fields['notification']['body'] = $title;
            $fields['notification']['notification_data'] = $notification_payload;
            // $fields['notification'] = $notification_payload;
            //$fields['notification'] = $notification_payload;
            $fields['notification']['click_action'] = '.MainActivity';
            $fields['notification']['sound'] = 'default';
            $fields['notification']['notification_type'] = $type;
            // dd($fields);
        }
        $fields['to'] = $deviceIdsonly;
        $fields['priority'] = "high";
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key,
        );
        $fields = json_encode($fields);

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
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        Log::info("========Start===========");
        Log::info($result);
        Log::info("======= END===========");
        //  echo $result;
        return $result;
        }catch (\Exception $e) {
        //dd($e);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
