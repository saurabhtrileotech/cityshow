<?php

namespace App\helpers;

use Illuminate\Support\Facades\File;
use Twilio\Rest\Client;

require "phpmailer/PHPMailer.php";
require "phpmailer/SMTP.php";
require "phpmailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailHelper
{


    function sendEmail($emailData)
    {
        
        try {
            $mail = new PHPMailer();
            /**Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output**/

            $mail->isSMTP();
            /**Send using SMTP**/
            $mail->Host       = config('constant.MAIL_HOST');

            //$mail->SMTPDebug = 2;
            /**Set the SMTP server to send through**/
            $mail->SMTPAuth   = true;
            /**Enable SMTP authentication**/
            $mail->Username   = config('constant.MAIL_USERNAME');
            /**SMTP username**/
            $mail->Password   = config('constant.MAIL_PASSWORD');
            /**SMTP password**/
            $mail->SMTPSecure = config('constant.MAIL_ENCRYPTION');
            /**Enable implicit TLS encryption**/
            $mail->Port       = config('constant.MAIL_PORT');
            /**TCP port to connect to; use 587 if you have**/
            /**Recipients**/
            $mail->SetFrom(config('constant.MAIL_FROM_ADDRESS'));
            /**Project Name**/
            $mail->addAddress($emailData['email']);
            /** Add a recipient**/
            $mail->isHTML(true);
            /**Set email format to HTML**/
            $mail->Subject = $emailData['subject'];
            $body = $emailData['email_format'];
            $body = str_replace("{{WELLNESSGURU_LOGO}}", asset('public/Images/Logo/logo.png'), $body);

            if (isset($emailData['name'])) {
                $body = str_replace("{{NAME}}", $emailData['name'], $body);
            }
            if (isset($emailData['date'])) {
                $body = str_replace("{{DATE}}", $emailData['date'], $body);
            }
            if (isset($emailData['otp'])) {
                $body = str_replace("{{OTP}}", $emailData['otp'], $body);
            }
            if (isset($emailData['token'])) {
                $body = str_replace("{{TOKEN}}", $emailData['token'], $body);
            }
            if (isset($emailData['reason_rejected'])) {
                $body = str_replace("{{REASON_REJECTED}}", $emailData['reason_rejected'], $body);
            }
            
            if (isset($emailData['customer_name'])) {
                $body = str_replace("{{CUSTOMER_NAME}}", $emailData['customer_name'], $body);
            }
            if (isset($emailData['user_name'])) {
                $body = str_replace("{{USER_NAME}}", $emailData['user_name'], $body);
            }
            $mail->Body = $body;
            
            if ($mail->send()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
           
            return false;
        }
    }

    /*
    function mail_sent($data)
    {
        try {
            $mail = new PHPMailer();
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = getenv('MAIL_HOST');                    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('MAIL_USERNAME');                //SMTP username
            $mail->Password   = getenv('MAIL_PASSWORD');                //SMTP password
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');              //Enable implicit TLS encryption
            $mail->Port       = getenv('MAIL_PORT');                    //TCP port to connect to; use 587 if you have
            //Recipients
            $mail->SetFrom(getenv('MAIL_FROM_ADDRESS'), "Teachus Summer camps");                 //Project Name
            $mail->addAddress($data['email']);             // Add a recipient
            //Content
            $mail->isHTML(true);                                          //Set email format to HTML
            $mail->Subject = $data['subject'];
            $body = File::get($data['email_format']);
            // $body = str_replace("{{image_url}}", asset('/dist/img/Logo-03.png'), $body);
            $body = str_replace("{{NAME}}", $data['name'], $body);
            $body = str_replace("{{OTP}}", $data['otp'], $body);
            $body = str_replace("{{IMAGE_URL}}", asset('dist/img/transperent.png'), $body);
            $body = str_replace("{{IOS_IMAGE}}", asset('dist/img/apple.png'), $body);
            $body = str_replace("{{ANDROID_IMAGE}}", asset('dist/img/android.png'), $body);
            $body = str_replace("{{YEAR}}", date("Y"), $body);
            $mail->Body = $body;
            if (!$mail->send()) {

            }
        } catch (Exception $e) {
            //dd($e->getMessage());
        }
    }

    //Function for sending OTP
    function sendSMS($data)
    {
        try {
            $token = getenv('TWILIO_TOKEN');
            $twilio_sid = getenv('TWILIO_SID');
            $client = new Client($twilio_sid, $token);
            $phonenumber = $data['phonenumber'];
            $client->messages->create(
                $phonenumber,
                [
                    'from' => getenv('TWILIO_PHONE_NUMBER'),
                    'body' => 'Your Teachus verification code is ' . $data['OTP']
                ]
            );
            $data['message'] = "success";
            $data['status'] = 200;
        } catch (\Twilio\Exceptions\RestException $e) {
            $data['message'] = $e->getMessage();
            $data['status'] = 400;
        }
        return $data;
    }

    //Function for sending OTP
    function sendCustomSMS($data)
    {
        try {
            $token = getenv('TWILIO_TOKEN');
            $twilio_sid = getenv('TWILIO_SID');
            $client = new Client($twilio_sid, $token);
            $phonenumber = $data['phonenumber'];
            $result = $client->messages->create(
                $phonenumber,
                [
                    'from' => getenv('TWILIO_PHONE_NUMBER'),
                    'body' => $data['message']
                ]
            );
            $data['message'] = "success";
            $data['status'] = 200;
        } catch (\Twilio\Exceptions\RestException $e) {
            $data['message'] = $e->getMessage();
            $data['status'] = 400;
        }
        return $data;
    }
     function send_subscription_activation_link($data)
    {
        try
        {
            $mail = new PHPMailer();
            //Server settings
             $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = env('MAIL_HOST');                    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = env('MAIL_USERNAME');                //SMTP username
            $mail->Password   = env('MAIL_PASSWORD');                //SMTP password
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');              //Enable implicit TLS encryption
            $mail->Port       = env('MAIL_PORT');                    //TCP port to connect to; use 587 if you have
            //Recipients
            $mail->SetFrom(env('MAIL_FROM_ADDRESS'));                 //Project Name
            $mail->addAddress($data['email']);             // Add a recipient
            //Content
            $mail->isHTML(true);                                          //Set email format to HTML
            $mail->Subject = $data['subject'];
            $body = File::get($data['email_format']);
            $body = str_replace("{{IMAGE_URL}}", asset('public/Images/Logo/logo.png'), $body);
            $body = str_replace("{{NAME}}", $data['name'], $body);
            $body = str_replace("{{ACTIVATION_URL}}", $data['ACTIVATION_URL'], $body);
            $body = str_replace("{{TOPIC}}", $data['TOPIC'], $body);
            $body = str_replace("{{IOS_IMAGE}}", asset('public/Images/apple.png'), $body);
            $body = str_replace("{{ANDROID_IMAGE}}", asset('public/Images/android.png'), $body);
            $body = str_replace("{{YEAR}}", date("Y"), $body);

           // echo $body;exit;
           //pr($mail);
           //return true;
            $mail->Body = $body;

            if (!$mail->send())
            {
                echo "Mail send error";
            }
            else
            {
                echo "Mail send success";
            }
        } catch (Exception $e) {
            //dd($e->getMessage());
        }
    }
    function send_panic_mail_to_contact($data)
    {
        try
        {
            $mail = new PHPMailer();
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = env('MAIL_HOST');                    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = env('MAIL_USERNAME');                //SMTP username
            $mail->Password   = env('MAIL_PASSWORD');                //SMTP password
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');              //Enable implicit TLS encryption
            $mail->Port       = env('MAIL_PORT');                    //TCP port to connect to; use 587 if you have
            //Recipients
            $mail->SetFrom(env('MAIL_FROM_ADDRESS'));                 //Project Name
            $mail->addAddress($data['email']);             // Add a recipient
            //Content
            $mail->isHTML(true);                                          //Set email format to HTML
            $mail->Subject = $data['subject'];
            $body = File::get($data['email_format']);
            $body = str_replace("{{IMAGE_URL}}", asset('public/Images/Logo/logo.png'), $body);
            $body = str_replace("{{NAME}}", $data['name'], $body);
            $body = str_replace("{{DATE_TIME}}", date("Y-m-d H:i:s"), $body);
            $body = str_replace("{{IMAGE_TEXT_ENG}}", $data['image_text_eng'], $body);
            $body = str_replace("{{IMAGE_TEXT_SPA}}", $data['image_text_spa'], $body);
            $body = str_replace("{{USER_NAME}}", $data['user_name'], $body);
            $body = str_replace("{{USER_NUMBER}}", $data['user_number'], $body);
            $body = str_replace("{{USER_ADDRESS}}", $data['user_address'], $body);
            $body = str_replace("{{IMAGE_URL}}", asset('public/Images/Logo/logo.png'), $body);
            $body = str_replace("{{IOS_IMAGE}}", asset('public/Images/apple.png'), $body);
            $body = str_replace("{{ANDROID_IMAGE}}", asset('public/Images/android.png'), $body);
            $body = str_replace("{{YEAR}}", date("Y"), $body);

            //echo $body;exit;
            //pr($mail);
            //return true;
            $mail->Body = $body;

            if (!$mail->send()) {
                // echo "++++++++++++++NOT SEND";exit;
            }
            else
            {
                //echo "++++++++++++Send";exit;
            }
        } catch (Exception $e) {
            //dd($e->getMessage());
        }
    }
 /*   //Function for contract email sending
    function contract_email_send($data)
    {
        try {
            $mail = new PHPMailer();
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = getenv('MAIL_HOST');                    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('MAIL_USERNAME');                //SMTP username
            $mail->Password   = getenv('MAIL_PASSWORD');                //SMTP password
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');              //Enable implicit TLS encryption
            $mail->Port       = getenv('MAIL_PORT');                    //TCP port to connect to; use 587 if you have
            //Recipients
            $mail->SetFrom(getenv('MAIL_FROM_ADDRESS'), "Teachus Summer camps");                 //Project Name
            $mail->addAddress($data['email']);             // Add a recipient
            //Content
            $mail->isHTML(true);                                          //Set email format to HTML
            $mail->Subject = $data['subject'];
            $body = File::get($data['email_format']);
            // $body = str_replace("{{image_url}}", asset('/dist/img/Logo-03.png'), $body);
            $body = str_replace("{{NAME}}", $data['name'], $body);
            $body = str_replace("{{LINK}}", $data['link'], $body);
            $body = str_replace("{{IMAGE_URL}}", asset('dist/img/transperent.png'), $body);
            $body = str_replace("{{IOS_IMAGE}}", asset('dist/img/apple.png'), $body);
            $body = str_replace("{{ANDROID_IMAGE}}", asset('dist/img/android.png'), $body);
            $body = str_replace("{{YEAR}}", date("Y"), $body);
            $mail->Body = $body;
            if (!$mail->send()) {
                // echo "NOT SEND";
            }
        } catch (Exception $e) {
            //dd($e->getMessage());
        }
    }

    public static function sendFCM($title, $message, $type, $deviceIdsonly, $deviceType, $notification_payload)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $server_key = "AAAA-ZkcMAo:APA91bFuw_ifc8K-ePg_5Wze9WEwbVwqDIbAKqvRmgXEqJInwffrPFWwPRcQBqpiLVj8hEC4aGhIUs3TCFgSv32-TfWm3XNyuTAW8UYhi5b3J5YSu1QxMQD_jjzXXx-75KEP-I4ewZ6X";
        $fields = array();
        $fields['content_available'] = true;
        if ($deviceType == 0) {
            //Meaning Andorid
            $fields['data'] = array();
            $fields['data']['title'] = $title;
            $fields['data']['body'] = $message;
            $fields['data']['extra_support_message'] = $notification_payload;
            $fields['data']['click_action'] = '.MainActivity';
            $fields['data']['sound'] = 'default';
            $fields['data']['type'] = $type;
        } else if ($deviceType == 1) {
            //Meaning iOS
            $fields['notification'] = array();
            $fields['notification']['title'] = $message;
            $fields['notification']['body'] = $title;
            $fields['notification']['extra_support_message'] = $notification_payload;
            $fields['notification']['click_action'] = '.MainActivity';
            $fields['notification']['sound'] = 'default';
            $fields['notification']['type'] = $type;
        }
        $fields['to'] = $deviceIdsonly;
        $fields['priority'] = "high";
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === false) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    function notification_mail_sent($data)
    {
        try {
            $mail = new PHPMailer();
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = getenv('MAIL_HOST');                    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('MAIL_USERNAME');                //SMTP username
            $mail->Password   = getenv('MAIL_PASSWORD');                //SMTP password
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');              //Enable implicit TLS encryption
            $mail->Port       = getenv('MAIL_PORT');                    //TCP port to connect to; use 587 if you have
            //Recipients
            $mail->SetFrom(getenv('MAIL_FROM_ADDRESS'), "Teachus Summer camps");                 //Project Name
            $mail->addAddress($data['email']);             // Add a recipient
            //Content
            $mail->isHTML(true);                                          //Set email format to HTML
            $mail->Subject = $data['subject'];
            $body = File::get($data['email_format']);
            // $body = str_replace("{{image_url}}", asset('/dist/img/Logo-03.png'), $body);
            $body = str_replace("{{NAME}}", $data['name'], $body);
            $body = str_replace("{{BODY}}", $data['body'], $body);
            $body = str_replace("{{IMAGE_URL}}", asset('dist/img/transperent.png'), $body);
            $body = str_replace("{{IOS_IMAGE}}", asset('dist/img/apple.png'), $body);
            $body = str_replace("{{ANDROID_IMAGE}}", asset('dist/img/android.png'), $body);
            $body = str_replace("{{YEAR}}", date("Y"), $body);
            $mail->Body = $body;
            if (!$mail->send()) {
                // echo "NOT SEND";
            }
        } catch (Exception $e) {
            //dd($e->getMessage());
        }
    }

    function parent_reregister($data)
    {
        try {
            $mail = new PHPMailer();
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = getenv('MAIL_HOST');                    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('MAIL_USERNAME');                //SMTP username
            $mail->Password   = getenv('MAIL_PASSWORD');                //SMTP password
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');              //Enable implicit TLS encryption
            $mail->Port       = getenv('MAIL_PORT');                    //TCP port to connect to; use 587 if you have
            //Recipients
            $mail->SetFrom(getenv('MAIL_FROM_ADDRESS'), "Teachus Summer camps");                 //Project Name
            $mail->addAddress($data['email']);             // Add a recipient
            //Content
            $mail->isHTML(true);                                          //Set email format to HTML
            $mail->Subject = $data['subject'];
            $body = File::get($data['email_format']);
            // $body = str_replace("{{image_url}}", asset('/dist/img/Logo-03.png'), $body);
            $body = str_replace("{{NAME}}", $data['name'], $body);
            $body = str_replace("{{EMAIL}}", $data['email'], $body);
            $body = str_replace("{{CAMPNAME}}", $data['camp_name'], $body);
            $body = str_replace("{{IMAGE_URL}}", asset('dist/img/transperent.png'), $body);
            $body = str_replace("{{IOS_IMAGE}}", asset('dist/img/apple.png'), $body);
            $body = str_replace("{{ANDROID_IMAGE}}", asset('dist/img/android.png'), $body);
            $body = str_replace("{{YEAR}}", date("Y"), $body);
            $mail->Body = $body;
            if (!$mail->send()) {
                // echo "NOT SEND";
            }
        } catch (Exception $e) {
            //dd($e->getMessage());
        }
    }

    function assigned_email($data)
    {
        try {
            $mail = new PHPMailer();
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = getenv('MAIL_HOST');                    //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('MAIL_USERNAME');                //SMTP username
            $mail->Password   = getenv('MAIL_PASSWORD');                //SMTP password
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');              //Enable implicit TLS encryption
            $mail->Port       = getenv('MAIL_PORT');                    //TCP port to connect to; use 587 if you have
            //Recipients
            $mail->SetFrom(getenv('MAIL_FROM_ADDRESS'), "Teachus Summer camps");                 //Project Name
            $mail->addAddress($data['email']);             // Add a recipient
            //Content
            $mail->isHTML(true);                                          //Set email format to HTML
            $mail->Subject = $data['subject'];
            $body = File::get($data['email_format']);
            // $body = str_replace("{{image_url}}", asset('/dist/img/Logo-03.png'), $body);
            $body = str_replace("{{NAME}}", $data['name'], $body);
            $body = str_replace("{{EMAIL}}", $data['email'], $body);
            $body = str_replace("{{CAMPNAME}}", $data['camp_name'], $body);
            $body = str_replace("{{IMAGE_URL}}", asset('dist/img/transperent.png'), $body);
            $body = str_replace("{{IOS_IMAGE}}", asset('dist/img/apple.png'), $body);
            $body = str_replace("{{ANDROID_IMAGE}}", asset('dist/img/android.png'), $body);
            $body = str_replace("{{YEAR}}", date("Y"), $body);
            $mail->Body = $body;
            if (!$mail->send()) {
                // echo "NOT SEND";
            }
        } catch (Exception $e) {
            //dd($e->getMessage());
        }
    }
*/
}
