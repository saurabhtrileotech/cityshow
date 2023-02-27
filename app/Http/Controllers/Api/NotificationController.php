<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Models\Notification;



class NotificationController extends Controller
{
    private $responseHelper;
    private $commonHelper;

    public function __construct(
        ResponseHelper $responseHelper,
        CommonHelper $commonHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }


    public function notificationList(Request $request){
        try{

             $notification = Notification::select('*');
             $totalCount = $notification->count();
         
          if($totalCount > 0){  
 
             if(isset($request->pagination) &&  $request->pagination != 'false'){
                 $limit = isset($request->limit) ? $request->limit : 10;
                 $page = ($request->page > 0) ? $request->page : 1; 
                 $notification = $notification->limit($limit)->offset(($page - 1) * $limit)->orderBy('id','desc')->get()->toArray();
 
                 $response['notification'] = $notification;
                 $response['total_counts'] = $totalCount;
                 $response['total_pages'] = $totalCount != 0 ? ceil($totalCount / $limit) : 0;
 
                 return $this->responseHelper->success('Notification gets successfully', $response);
             }else{
                 $notification = $notification->orderBy('id','desc')->get()->toArray();
                 $response['notification'] = $notification;
 
                 return $this->responseHelper->success('Notification gets successfully', $response);
             }
 
         }else{
             $response['notification'] = [];
             return $this->responseHelper->success('No Notification found', $response);
         }
 
         }
         catch(Exception $e){
             return $this->responseHelper->error($e->getMessage());
         }
 
     }
}
