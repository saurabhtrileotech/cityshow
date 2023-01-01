<?php

namespace App\Helpers\Api;

use Response;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use Exception;
use URL;
use Artisan;
use DB;
use Mail;
use File;
use Auth;
use App\Models\Service;
use App\Models\BookCategory;
use App\Models\ServiceLike;
use Carbon\Carbon;
use App\Models\WellnessGuruSlot;

class ServiceHelper
{
    private $responseHelper;
    public function __construct(ResponseHelper $responseHelper, CommonHelper $commonHelper)
    {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }

    /*
        Function name : getServiceListing
        Description : to get service
        Developed by : Shrusti Shah
        Date : 07/11/2022
*/

    public function getServiceListing($request)
    {

        try {
            $path = config('constant.user_document_path');
            $type = config('constant.type.service_documents_type');

            $getServiceList = [];
            if (isset($request->id)) {

                $getServiceList = Service::with(['guru','tagsname','slots' => function ($query) {
                    $query->where('slot_date','>',date('Y-m-d'));
                    }])->where('id', $request->id)->get();

            } elseif (Auth::user()->id == $request->guru_id && Auth::user()->type == "guru") {

                $getServiceList = Service::with('guru', 'tagsname', 'duration', 'slots')->where('user_id', $request->guru_id)->orderby('id', 'DESC');

                if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                    $getAllData = $this->commonHelper->getPagination($getServiceList, $this->commonHelper->getSettingValue(6), $request->page);
                    $getServiceList = $getAllData['data'];
                } else {
                    $getServiceList = $getServiceList->get();
                }
            } elseif (!empty($request->guru_id)) {

                $getServiceList = Service::with('guru', 'tagsname', 'duration', 'slots')->where('user_id', $request->guru_id)->orderby('id', 'DESC');

                if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                    $getAllData = $this->commonHelper->getPagination($getServiceList, $this->commonHelper->getSettingValue(6), $request->page);
                    $getServiceList = $getAllData['data'];
                } else {
                    $getServiceList = $getServiceList->get();
                }
            }elseif (empty($request->id) && empty($request->guru_id)) {

                $getCount =   Service::query();
                $getCount = $getCount->count();

                /**check if data not found then return back */
                if ($getCount < 0) {
                    $response['status'] = false;
                    $response['message'] = trans('api_messages.error.SERVICE_NOT_FOUND');
                } else {
                    $getServiceList = Service::with('guru', 'tagsname', 'duration', 'slots')->orderby('id', 'DESC');
                    /**get data with pagination start*/
                    if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                        $getAllData = $this->commonHelper->getPagination($getServiceList, $this->commonHelper->getSettingValue(6), $request->page);
                        $getServiceList = $getAllData['data'];
                    } else {
                        $getServiceList = $getServiceList->orderby('id', 'DESC')->get();
                    }
                }
            }

            $getAllService = [];
            foreach ($getServiceList as $key => $getOneServiceList) {
                $userId = $getOneServiceList['user_id'];
                $id = $getOneServiceList['id'];
                $subCategoryId = $getOneServiceList['sub_category_id'];
                // $type = $getOneServiceList['type'];

                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                /**get image url */
                $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);

                //$getOneServiceList['guru']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";
                if (!empty($getOneServiceList['guru'])) {
                    $getOneServiceList['guru']['profile'] = $this->commonHelper->getProfileImage($profileImage);
                }

                // $getOneServiceList['profileImage'] = "";
                // if ($profileImage['status'] == 'true') {
                //     $getOneServiceList['profileImage'] = $profileImage['data'];
                // }

                $pathSubCategory = config('constant.book_subcategory_image_path');
                $subCategory = config('constant.type.book_subcategory_documents_type');
                /**get image url */
                $subCategoryImage = $this->commonHelper->getImages($subCategoryId, $subCategory, $pathSubCategory);
                $getOneServiceList['book_subcategeory_image'] = "";
                if ($subCategoryImage['status'] == 'true') {
                    $getOneServiceList['book_subcategeory_image'] = $subCategoryImage['data'];
                }

                $subcategoryName = BookCategory::where('id', $subCategoryId)->count();
                if ($subcategoryName > 0) {
                    $subcategoryName = BookCategory::where('id', $subCategoryId)->first()->toArray();
                    $getOneServiceList['subcategory'] = $subcategoryName;
                }

                $pathServiceVideoFile = config('constant.service_video_document_path');
                $serviceDocumentVideoType = config('constant.type.service_video_documents_type');
                /**get image url */
                $fileVideo = $this->commonHelper->getImages($id, $serviceDocumentVideoType, $pathServiceVideoFile);
                $getOneServiceList['service_video_upload'] = [];
                if ($fileVideo['status'] == 'true') {
                    $getOneServiceList['service_video_upload'] = $fileVideo['data'];
                }

                $file =  $this->commonHelper->getImages($id, $type, $path);
                $getOneServiceList['service_file_upload'] = [];
                if ($file['status'] == 'true') {
                    $getOneServiceList['service_file_upload'] = $file['data'];
                }

                /**get like listing */
                $getTotalLikes =  $this->getTotalLikes($id);
                $getOneServiceList['like_count'] = $getTotalLikes;
                /**get like listing end*/
                $isLikeByUser = ServiceLike::where('service_id', $id)->where('user_id', $this->commonHelper->userdetails()->id)->where('is_like', 1)->count();
                $isLikeByUser > 0 ? $getOneServiceList['is_like'] = "true" : $getOneServiceList['is_like'] = "false";

                $getOneServiceList['guru']['percentage'] = $this->commonHelper->getSettingValue(3);
                $getOneServiceList['guru']['offer_percentage'] = $this->commonHelper->getSettingValue(3)." % "."cashback for preminum members";

                $getAllService['service'][$key] = $getOneServiceList;
            }
            if (count($getAllService) > 0) {
                if (isset($request->pagination) && ($request->pagination == 'true')) {
                    $getAllService['total_counts'] = $getAllData['total_counts'];
                    $getAllService['total_pages'] = $getAllData['total_pages'];
                    $getAllService['current_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllService;
                } else {
                    $getAllService['discussion'] = $getOneServiceList->toArray();
                    $response['status'] = 'true';
                    $response['data'] = $getOneServiceList->toArray();
                }
            } else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.SERVICE_NOT_FOUND');
            }
            return $response;
        } catch (Exception $e) {
            $response['status'] = 'false';
            $response['message'] = $e->getMessage();
        }
    }

    public function getServiceScheduleListing($request)
    {

        try {
            $path = config('constant.user_document_path');
            $type = config('constant.type.service_documents_type');

            $getServiceList = [];
            $slots = [];
            if (isset($request->id)) {

                $getServiceList = Service::with('guru', 'tagsname', 'duration')
                    // ->whereHas('slots', function($query){
                    //     $query->whereBetween('slot_date', [date("Y-01-01"), date("Y-12-31")])
                    //     ->whereYear('slot_date', date('Y'))
                    //     ->orderBy('slot_date','ASC')
                    //     ->groupBy(function ($val) {
                    //         return Carbon::parse($val->slot_date)->format('m');
                    //     });
                    // })
                    ->where('id', $request->id)
                    //echo $getServiceList->toSql();
                    ->get();

                $slots =  WellnessGuruSlot::where('service_id', $request->id)
                    ->whereBetween('slot_date', [date("Y-01-01"), date("Y-12-31")])
                    ->whereYear('slot_date', date('Y'))
                    ->orderby('slot_date', 'ASC')
                    ->get()
                    ->groupBy(function ($val) {
                        return Carbon::parse($val->slot_date)->format('F');
                    });


                //print($getServiceList); exit;

            }

            //print_r($getServiceList->toArray());

            $getAllService = [];
            foreach ($getServiceList as $key => $getOneServiceList) {
                $userId = $getOneServiceList['user_id'];
                $id = $getOneServiceList['id'];
                $subCategoryId = $getOneServiceList['sub_category_id'];
                // $type = $getOneServiceList['type'];

                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                /**get image url */
                $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);
                $getOneServiceList['guru']['profile'] = $this->commonHelper->getProfileImage($profileImage);
                // $getOneServiceList['guru']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";

                // $getOneServiceList['profileImage'] = "";
                // if ($profileImage['status'] == 'true') {
                //     $getOneServiceList['profileImage'] = $profileImage['data'];
                // }

                $pathSubCategory = config('constant.book_subcategory_image_path');
                $subCategory = config('constant.type.book_subcategory_documents_type');
                /**get image url */
                $subCategoryImage = $this->commonHelper->getImages($subCategoryId, $subCategory, $pathSubCategory);
                $getOneServiceList['book_subcategeory_image'] = "";
                if ($subCategoryImage['status'] == 'true') {
                    $getOneServiceList['book_subcategeory_image'] = $subCategoryImage['data'];
                }

                $subcategoryName = BookCategory::where('id', $subCategoryId)->count();
                if ($subcategoryName > 0) {
                    $subcategoryName = BookCategory::where('id', $subCategoryId)->first()->toArray();
                    $getOneServiceList['subcategory'] = $subcategoryName;
                }

                $pathServiceVideoFile = config('constant.service_video_document_path');
                $serviceDocumentVideoType = config('constant.type.service_video_documents_type');
                /**get image url */
                $fileVideo = $this->commonHelper->getImages($id, $serviceDocumentVideoType, $pathServiceVideoFile);
                $getOneServiceList['service_video_upload'] = [];
                if ($fileVideo['status'] == 'true') {
                    $getOneServiceList['service_video_upload'] = $fileVideo['data'];
                }


                $file =  $this->commonHelper->getImages($id, $type, $path);
                $getOneServiceList['service_file_upload'] = [];
                if ($file['status'] == 'true') {
                    $getOneServiceList['service_file_upload'] = $file['data'];
                }

                /**get like listing */
                $getTotalLikes =  $this->getTotalLikes($id);
                $getOneServiceList['like_count'] = $getTotalLikes;
                /**get like listing end*/
                $isLikeByUser = ServiceLike::where('service_id', $id)->where('user_id', $this->commonHelper->userdetails()->id)->where('is_like', 1)->count();
                $isLikeByUser > 0 ? $getOneServiceList['is_like'] = "true" : $getOneServiceList['is_like'] = "false";

                $getAllService['service'][$key] = $getOneServiceList;
                $getOneServiceList->slots = (!empty($slots)) ? $slots->toArray() : $slots;
            }
            if (count($getAllService) > 0) {
                if (isset($request->pagination) && ($request->pagination == 'true')) {
                    $getAllService['total_counts'] = $getAllData['total_counts'];
                    $getAllService['total_pages'] = $getAllData['total_pages'];
                    $getAllService['current_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllService;
                } else {
                    $getAllService['discussion'] = $getOneServiceList->toArray();
                    $response['status'] = 'true';
                    $response['data'] = $getOneServiceList->toArray();
                }
            } else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.SERVICE_NOT_FOUND');
            }
            return $response;
        } catch (Exception $e) {
            //dd($e);
            $response['status'] = 'false';
            $response['message'] = $e->getMessage();
        }
    }



    /*
        Function name : getTotalLikes
        Description : to get like count for service
        Developed by : srushti shah
        Date : 08/10/2022
    */
    public function getTotalLikes($id)
    {
        $count =  ServiceLike::where('service_id', $id)->where('is_like', 1)->count();
        if ($count) {
            return $count;
        } else {
            return "";
        }
    }
}
