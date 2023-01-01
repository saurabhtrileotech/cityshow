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
use App\Models\Discussion;
use App\Models\DiscussionTag;
use App\Models\DiscussionLike;
use App\Models\DiscussionReply;
use Carbon\Carbon;


class DiscussionHelper
{
    private $responseHelper;
    public function __construct(ResponseHelper $responseHelper, CommonHelper $commonHelper)
    {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }

    /*
        Function name : getDiscussionListing
        Description : to get Discussion
        Developed by : Srushti Shah
        Date : 11/10/2022
    */

    public function getDiscussionListing($request)
    {
        try {
            if (isset($request->id) && !isset($request->pagination) && !isset($request->page)) {
                $getDiscussionList = DiscussionReply::with('user')->where('discussion_id', $request->id)->where('discussion_reply_id',null)->orderby('id', 'DESC')->get()->toArray();
            } else {
                $getCount =   DiscussionReply::query();

                $getCount = $getCount->count();

                if ($getCount < 0) {
                    $response['status'] = 'false';
                    $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                }
                else {
                    /**get data with pagination start*/
                    if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')  && !isset($request->id)) {
                        $getDiscussionList = DiscussionReply::with('user')->where('discussion_reply_id',null)->orderby('id', 'DESC');
                        $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                        $getDiscussionList = $getAllData['data'];
                    }
                    elseif (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true') && isset($request->id)) {
                        $getDiscussionList = DiscussionReply::where('discussion_id', $request->id)->where('discussion_reply_id',null)->count();
                        if ($getDiscussionList > 0) {
                            $getDiscussionList = DiscussionReply::with('user')->where('discussion_id', $request->id)->where('discussion_reply_id',null)->orderby('id', 'DESC');
                            $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                            $getDiscussionList = $getAllData['data'];
                        }
                        else{
                            $response['status'] = 'false';
                            $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                        }
                    }
                    else {
                        $getDiscussionList = DiscussionReply::with('user')->where('discussion_reply_id',null)->orderby('id', 'DESC')->get();
                    }
                }
            }
            $getAllDiscussion = [];
            foreach ($getDiscussionList as $key => $getDiscussionList) {
                $id = $getDiscussionList['id'];
                $userId = $getDiscussionList['user_id'];
                $query = DiscussionTag::where('discussion_id', $id);
                $tagCount = $query->count();
                if ($tagCount > 0) {
                    $getTag = $query->get()->toArray();
                    $getDiscussionList['discussTag'] = $getTag;
                }

                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                /**get image url */
                $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);

                $getDiscussionList['user']['profile'] = $this->commonHelper->getProfileImage($profileImage);

                // $getDiscussionList['user']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";
                // $getDiscussionList['profile'] = "";
                // if ($profileImage['status'] == 'true') {
                //     $getDiscussionList['profile'] = $profileImage['data'];
                // }


                /**get like listing */
                $getTotalLikes =  $this->getReplyTotalLikes($id);
                $getDiscussionList['like_count'] = $getTotalLikes;
                /**get like listing end*/

                $isLikeByUser = DiscussionLike::where('discussion_reply_id', $id)->where('from_user_id', $this->commonHelper->userdetails()->id)->where('is_like', 1)->count();
                $isLikeByUser > 0 ? $getDiscussionList['is_like'] = "true" : $getDiscussionList['is_like'] = "false";

                // $getDiscussionList['comment_list'] = "";
                $getDiscussionList['comment_count'] = "";
                $getCommentListing  =  $this->discussionReplyToReplyListing($id);
                if ($getCommentListing['status'] == 'true') {
                    // $getDiscussionList['comment_list'] = $getCommentListing['data']['discussion'];
                    $getDiscussionList['comment_count'] = $getCommentListing['data']['discussion_reply_count'];
                }

                $getAllDiscussion['comment_list'][$key] = $getDiscussionList;
            }
            if (count($getAllDiscussion) > 0) {
                if (isset($request->pagination) && ($request->pagination == 'true')) {
                    $getAllDiscussion['total_counts'] = $getAllData['total_counts'];
                    $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                    $getAllDiscussion['curent_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllDiscussion;
                }elseif(isset($request->pagination) && ($request->pagination == 'true') && ($request->id)) {
                    $getAllDiscussion['total_counts'] = $getAllData['total_counts'];
                    $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                    $getAllDiscussion['curent_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllDiscussion;
                }
                else {
                    $response['status'] = 'true';
                    $response['data'] = $getAllDiscussion;
                }
            } else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
            }
            return $response;
        } catch (Exception $e) {
            $response['status'] = 'false';
            $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
            return $response;
        }
    }

    /*
        Function name : getReplyDiscussionListing
        Description : to get Discussion
        Developed by : Srushti Shah
        Date : 11/10/2022
    */

    public function getReplyDiscussionListing($request)
    {
        try {
            if (isset($request->id) && !isset($request->pagination) && !isset($request->page)) {
                $getDiscussionList = DiscussionReply::with('user')->where('discussion_reply_id', $request->id)->whereNotNull('discussion_reply_id')->orderby('id', 'DESC')->get()->toArray();
            } else {
                $getCount =   DiscussionReply::query();

                $getCount = $getCount->count();

                if ($getCount < 0) {
                    $response['status'] = 'false';
                    $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                }
                else {
                    /**get data with pagination start*/
                    if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')  && !isset($request->id)) {
                        $getDiscussionList = DiscussionReply::with('user')->whereNotNull('discussion_reply_id')->orderby('id', 'DESC');
                        $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                        $getDiscussionList = $getAllData['data'];
                    }
                    elseif (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true') && isset($request->id)) {
                        $getDiscussionList = DiscussionReply::where('discussion_reply_id', $request->id)->whereNotNull('discussion_reply_id')->count();
                        if ($getDiscussionList > 0) {
                            $getDiscussionList = DiscussionReply::with('user')->where('discussion_reply_id', $request->id)->whereNotNull('discussion_reply_id')->orderby('id', 'DESC');
                            $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                            $getDiscussionList = $getAllData['data'];
                        }
                        else{
                            $response['status'] = 'false';
                            $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                        }
                    }
                    else {
                        $getDiscussionList = DiscussionReply::with('user')->whereNotNull('discussion_reply_id')->orderby('id', 'DESC')->get();
                    }
                }
            }
            $getAllDiscussion = [];
            foreach ($getDiscussionList as $key => $getDiscussionList) {
                $id = $getDiscussionList['id'];
                $userId = $getDiscussionList['user_id'];
                // $query = DiscussionTag::where('discussion_id', $id);
                // $tagCount = $query->count();
                // if ($tagCount > 0) {
                //     $getTag = $query->get()->toArray();
                //     $getDiscussionList['discussTag'] = $getTag;
                // }

                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                /**get image url */
                $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);
                $getDiscussionList['user']['profile'] = $this->commonHelper->getProfileImage($profileImage);

                // $getDiscussionList['user']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";
                // $getDiscussionList['profile'] = "";
                // if ($profileImage['status'] == 'true') {
                //     $getDiscussionList['profile'] = $profileImage['data'];
                // }


                /**get like listing */
                // $getTotalLikes =  $this->getTotalLikes($id);
                // $getDiscussionList['like_count'] = $getTotalLikes;
                /**get like listing end*/

                // $isLikeByUser = DiscussionLike::where('discussion_reply_id', $id)->where('from_user_id', $this->commonHelper->userdetails()->id)->where('is_like', 1)->count();
                // $isLikeByUser > 0 ? $getDiscussionList['is_like'] = "true" : $getDiscussionList['is_like'] = "false";

                // $getDiscussionList['comment_list'] = "";
                // $getDiscussionList['comment_count'] = "";
                // $getCommentListing  =  $this->discussionReplyToReplyListing($id);
                // if ($getCommentListing['status'] == 'true') {
                //     // $getDiscussionList['comment_list'] = $getCommentListing['data']['discussion'];
                //     $getDiscussionList['comment_count'] = $getCommentListing['data']['discussion_reply_count'];
                // }

                $getAllDiscussion['comment_list'][$key] = $getDiscussionList;
            }
            if (count($getAllDiscussion) > 0) {
                if (isset($request->pagination) && ($request->pagination == 'true')) {
                    $getAllDiscussion['total_counts'] = $getAllData['total_counts'];
                    $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                    $getAllDiscussion['curent_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllDiscussion;
                }elseif(isset($request->pagination) && ($request->pagination == 'true') && ($request->id)) {
                    $getAllDiscussion['total_counts'] = $getAllData['total_counts'];
                    $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                    $getAllDiscussion['curent_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllDiscussion;
                }
                else {
                    $response['status'] = 'true';
                    $response['data'] = $getAllDiscussion;
                }
            } else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
            }
            return $response;
        } catch (Exception $e) {
            $response['status'] = 'false';
            $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
            return $response;
        }
    }

    /*
        Function name : getGuestReplyDiscussionListing
        Description : to get Discussion
        Developed by : Srushti Shah
        Date : 11/10/2022
    */

    public function getGuestReplyDiscussionListing($request)
    {
        try {
            if (($request->type) == "guest") {
                if (isset($request->id) && !isset($request->pagination) && !isset($request->page)) {
                    $getDiscussionList = DiscussionReply::with('user')->where('discussion_reply_id', $request->id)->whereNotNull('discussion_reply_id')->orderby('id', 'DESC')->get()->toArray();
                } else {
                    $getCount =   DiscussionReply::query();

                    $getCount = $getCount->count();

                    if ($getCount < 0) {
                        $response['status'] = 'false';
                        $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                    } else {
                        /**get data with pagination start*/
                        if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')  && !isset($request->id)) {
                            $getDiscussionList = DiscussionReply::with('user')->whereNotNull('discussion_reply_id')->orderby('id', 'DESC');
                            $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                            $getDiscussionList = $getAllData['data'];
                        } elseif (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true') && isset($request->id)) {
                            $getDiscussionList = DiscussionReply::where('discussion_reply_id', $request->id)->whereNotNull('discussion_reply_id')->count();
                            if ($getDiscussionList > 0) {
                                $getDiscussionList = DiscussionReply::with('user')->where('discussion_reply_id', $request->id)->whereNotNull('discussion_reply_id')->orderby('id', 'DESC');
                                $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                                $getDiscussionList = $getAllData['data'];
                            } else {
                                $response['status'] = 'false';
                                $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                            }
                        } else {
                            $getDiscussionList = DiscussionReply::with('user')->whereNotNull('discussion_reply_id')->orderby('id', 'DESC')->get();
                        }
                    }
                }
                $getAllDiscussion = [];
                foreach ($getDiscussionList as $key => $getDiscussionList) {
                    $id = $getDiscussionList['id'];
                    $userId = $getDiscussionList['user_id'];
                    // $query = DiscussionTag::where('discussion_id', $id);
                    // $tagCount = $query->count();
                    // if ($tagCount > 0) {
                            //     $getTag = $query->get()->toArray();
                            //     $getDiscussionList['discussTag'] = $getTag;
                    // }

                    $pathProfile = config('constant.profile_image_path');
                    $profileImageType = config('constant.type.profile_image_type');
                    /**get image url */
                    $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);
                    $getDiscussionList['user']['profile'] = $this->commonHelper->getProfileImage($profileImage);

                    // $getDiscussionList['user']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";
                    // $getDiscussionList['profile'] = "";
                    // if ($profileImage['status'] == 'true') {
                            //     $getDiscussionList['profile'] = $profileImage['data'];
                    // }


                    /**get like listing */
                    // $getTotalLikes =  $this->getTotalLikes($id);
                    // $getDiscussionList['like_count'] = $getTotalLikes;
                    /**get like listing end*/

                    // $isLikeByUser = DiscussionLike::where('discussion_reply_id', $id)->where('from_user_id', $this->commonHelper->userdetails()->id)->where('is_like', 1)->count();
                    // $isLikeByUser > 0 ? $getDiscussionList['is_like'] = "true" : $getDiscussionList['is_like'] = "false";

                    // $getDiscussionList['comment_list'] = "";
                    // $getDiscussionList['comment_count'] = "";
                    // $getCommentListing  =  $this->discussionReplyToReplyListing($id);
                    // if ($getCommentListing['status'] == 'true') {
                            //     // $getDiscussionList['comment_list'] = $getCommentListing['data']['discussion'];
                            //     $getDiscussionList['comment_count'] = $getCommentListing['data']['discussion_reply_count'];
                    // }

                    $getAllDiscussion['comment_list'][$key] = $getDiscussionList;
                }
                if (count($getAllDiscussion) > 0) {
                    if (isset($request->pagination) && ($request->pagination == 'true')) {
                        $getAllDiscussion['total_counts'] = $getAllData['total_counts'];
                        $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                        $getAllDiscussion['curent_page'] = $request->page;
                        $response['status'] = 'true';
                        $response['data'] = $getAllDiscussion;
                    } elseif (isset($request->pagination) && ($request->pagination == 'true') && ($request->id)) {
                        $getAllDiscussion['total_counts'] = $getAllData['total_counts'];
                        $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                        $getAllDiscussion['curent_page'] = $request->page;
                        $response['status'] = 'true';
                        $response['data'] = $getAllDiscussion;
                    } else {
                        $response['status'] = 'true';
                        $response['data'] = $getAllDiscussion;
                    }
                } else {
                    $response['status'] = 'false';
                    $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                }
                return $response;
            }
            else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.NOT_GUEST');
            }
        } catch (Exception $e) {
            $response['status'] = 'false';
            $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
            return $response;
        }
    }



    /*
        Function name : getDiscussionGuestListing
        Description : to get Discussion for guest
        Developed by : Srushti Shah
        Date : 12/11/2022
    */

    public function getDiscussionGuestListing($request)
    {
        try {
            if ($request->type == "guest") {
                if (isset($request->id) && !isset($request->pagination) && !isset($request->page) && isset($request->type) == "guest") {
                    $getDiscussionList = DiscussionReply::with('user')->where('discussion_id', $request->id)->where('discussion_reply_id', null)->orderby('id', 'DESC')->get()->toArray();
                } else {
                    $getCount =   DiscussionReply::query();

                    $getCount = $getCount->count();

                    if ($getCount < 0) {
                        $response['status'] = 'false';
                        $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                    } else {
                        /**get data with pagination start*/
                        if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')  && !isset($request->id) && isset($request->type) == "guest") {
                            $getDiscussionList = DiscussionReply::with('user')->where('discussion_reply_id', null)->orderby('id', 'DESC');
                            $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                            $getDiscussionList = $getAllData['data'];
                        } elseif (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true') && isset($request->id) && isset($request->type) == "guest") {
                            $getDiscussionList = DiscussionReply::where('discussion_id', $request->id)->where('discussion_reply_id', null)->count();
                            if ($getDiscussionList > 0) {
                                $getDiscussionList = DiscussionReply::with('user')->where('discussion_id', $request->id)->where('discussion_reply_id', null)->orderby('id', 'DESC');
                                $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                                $getDiscussionList = $getAllData['data'];
                            } else {
                                $response['status'] = 'false';
                                $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                            }
                        } else {
                            $getDiscussionList = DiscussionReply::with('user')->where('discussion_reply_id', null)->orderby('id', 'DESC')->get()->toArray();
                        }
                    }
                }
                $getAllDiscussion = [];
                foreach ($getDiscussionList as $key => $getDiscussionList) {
                    $id = $getDiscussionList['id'];
                    $userId = $getDiscussionList['user_id'];
                    $query = DiscussionTag::where('discussion_id', $id);
                    $tagCount = $query->count();
                    if ($tagCount > 0) {
                        $getTag = $query->get()->toArray();
                        $getDiscussionList['discussTag'] = $getTag;
                    }

                    $pathProfile = config('constant.profile_image_path');
                    $profileImageType = config('constant.type.profile_image_type');
                    /**get image url */
                    $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);
                    $getDiscussionList['user']['profile'] = $this->commonHelper->getProfileImage($profileImage);

                    // $getDiscussionList['user']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";
                    // $getDiscussionList['profile'] = "";
                    // if ($profileImage['status'] == 'true') {
                            //     $getDiscussionList['profile'] = $profileImage['data'];
                    // }


                    /**get like listing */
                    $getTotalLikes =  $this->getTotalLikes($id);
                    $getDiscussionList['like_count'] = $getTotalLikes;
                    /**get like listing end*/

                    $isLikeByUser = DiscussionLike::where('discussion_id', $id)->where('from_user_id', $request->user_id)->where('is_like', 1)->count();
                    $isLikeByUser > 0 ? $getDiscussionList['is_like'] = "true" : $getDiscussionList['is_like'] = "false";

                    // $getDiscussionList['comment_list'] = "";
                    $getDiscussionList['comment_count'] = "";
                    $getCommentListing  =  $this->discussionReplyToReplyListing($id);
                    if ($getCommentListing['status'] == 'true') {
                        // $getDiscussionList['comment_list'] = $getCommentListing['data']['discussion'];
                        $getDiscussionList['comment_count'] = $getCommentListing['data']['discussion_reply_count'];
                    }

                    $getAllDiscussion['comment_list'][$key] = $getDiscussionList;
                }
                if (count($getAllDiscussion) > 0) {
                    if (isset($request->pagination) && ($request->pagination == 'true')) {
                        $getAllDiscussion['total_counts'] = $getAllData['total_counts'];
                        $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                        $getAllDiscussion['curent_page'] = $request->page;
                        $response['status'] = 'true';
                        $response['data'] = $getAllDiscussion;
                    } elseif (isset($request->pagination) && ($request->pagination == 'true') && ($request->id)) {
                        $getAllDiscussion['total_counts'] = $getAllData['total_counts'];
                        $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                        $getAllDiscussion['curent_page'] = $request->page;
                        $response['status'] = 'true';
                        $response['data'] = $getAllDiscussion;
                    } else {
                        $response['status'] = 'true';
                        $response['data'] = $getAllDiscussion;
                    }
                } else {
                    $response['status'] = 'false';
                    $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
                }
                return $response;
            } else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.NOT_GUEST');
            }
        } catch (Exception $e) {
            $response['status'] = 'false';
            $response['message'] = trans('api_messages.error.DISCUSSION_NOT_FOUND');
            return $response;
        }
    }



    public function getDiscussionGuruListing($request)
    {
        try {
            // if (isset($request->tagsearch)) {
            //     $searchTag = $request->tagsearch;
            //     $getDiscussionList = Discussion::with('user')->with(['tags' => function ($query) use ($searchTag) {
            //         $query->where('name', 'like', '%' . $searchTag . '%');
            //     }])->orderby('id', 'DESC');

            //     if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
            //         $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
            //         $getDiscussionList = $getAllData['data'];
            //     } else {
            //         $getDiscussionList = $getDiscussionList->get();
            //     }
                //print_r($getDiscussionList);exit;
           // } else {

                $getDiscussionList = Discussion::with('user','tags');

                if (isset($request->tagsearch)) {
                    $searchTag = $request->tagsearch;
                    $getDiscussionList =  $getDiscussionList->whereHas('tags',function ($query) use ($searchTag) {
                        $query->where('name', 'like', '%' . $searchTag . '%');
                    });
                }

                if ($request->filtertype == 'lastweek'){
                    $date = Carbon::today()->subDays(7);
                    $getDiscussionList =  $getDiscussionList->where('created_at','>=',$date);
                }
                elseif ($request->filtertype == 'thismonth'){
                    $date = Carbon::now()->month;
                    $getDiscussionList =  $getDiscussionList->whereMonth('created_at', $date);
                }
                elseif ($request->filtertype == 'today'){
                    $date = Carbon::today();
                    $getDiscussionList =  $getDiscussionList->whereDate('created_at', $date);
                }

                $getDiscussionList =  $getDiscussionList->orderby('id', 'DESC');



                 /**get data with pagination start*/
                 if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                    $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                    $getDiscussionList = $getAllData['data'];
                } else {
                    $getDiscussionList = $getDiscussionList->get();
                }



                // $getCount =   Discussion::query();

                // $getCount = $getCount->count();

                // if ($getCount < 0) {
                //     $response['status'] = 'false';
                //     $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
                // } else {
                //     if ($request->filtertype == 'latest') {
                //         $getDiscussionList = Discussion::with('user', 'tags')->orderby('id', 'DESC');
                //     }
                //     elseif ($request->filtertype == 'lastweek'){
                //         $date = Carbon::today()->subDays(7);
                //         $getDiscussionList = Discussion::where('created_at','>=',$date)->with('user','tags')->orderby('id', 'DESC');
                //     }
                //     elseif ($request->filtertype == 'thismonth'){
                //         $date = Carbon::now()->month;
                //         $getDiscussionList = Discussion::with('user','tags')->whereMonth('created_at', $date)->orderby('id', 'DESC');
                //     }
                //     elseif ($request->filtertype == 'today'){
                //         $date = Carbon::today();
                //         $getDiscussionList = Discussion::with('user','tags')->whereDate('created_at', $date)->orderby('id', 'DESC');
                //     }

                //     /**get data with pagination start*/
                //     if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                //         $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                //         $getDiscussionList = $getAllData['data'];
                //     } else {
                //         $getDiscussionList = $getDiscussionList->get();
                //     }
                // }
           // }

            $totalCounts = "";

            if (isset($request->tagsearch)) {
                $result = array();
                foreach ($getDiscussionList as $key => $value) {
                    if (!empty($value['tags'])) {
                        $result[] = $value;
                    }
                }
            } else {
                $result = $getDiscussionList;
            }
            // dd($getDiscussionList->toArray());
            $totalCounts = count($result);
            $getAllDiscussion = [];
            foreach ($result as $key => $getDiscussionList) {
                $id = $getDiscussionList['id'];
                $userId = $getDiscussionList['user_id'];


                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                /**get image url */
                $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);
                $getDiscussionList['user']['profile'] = $this->commonHelper->getProfileImage($profileImage);

                // $getDiscussionList['user']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";



                // $getDiscussionList['profile'] = "";
                // if ($profileImage['status'] == 'true') {
                //     $getDiscussionList['profile'] = $profileImage['data'];
                // }


                /**get like listing */
                $getTotalLikes =  $this->getTotalLikes($id);
                $getDiscussionList['like_count'] = $getTotalLikes;
                /**get like listing end*/

                $isLikeByUser = DiscussionLike::where('discussion_id', $id)->where('from_user_id', $this->commonHelper->userdetails()->id)->where('is_like', 1)->count();
                $isLikeByUser > 0 ? $getDiscussionList['is_like'] = "true" : $getDiscussionList['is_like'] = "false";

                // $getDiscussionList['comment_list'] = "";
                $getDiscussionList['comment_count'] = "";
                $getCommentListing  =  $this->discussionReplyListing($id);
                // dd($getCommentListing);

                if (!empty($getCommentListing['status']) && $getCommentListing['status'] == 'true') {
                    // $getDiscussionList['comment_list'] = $getCommentListing['data']['discussion'];
                    $getDiscussionList['comment_count'] = $getCommentListing['data']['discussion_reply_count'];
                }
                else{
                    $getDiscussionList['comment_count'] = "";
                }


                $getAllDiscussion['discussion'][$key] = $getDiscussionList;
            }

            // dd($getDiscussionList->toArray());
            if (count($getAllDiscussion) > 0) {
                if (isset($request->pagination) && ($request->pagination == 'true')) {
                    $getAllDiscussion['total_counts'] = $totalCounts;
                    $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                    $getAllDiscussion['curent_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllDiscussion;
                } else {
                    $getDiscussion['discussion'] = $result->toArray();
                    $response['status'] = 'true';
                    $response['data'] = $getDiscussion;
                }
            } else {
                $getDiscussion['discussion'] = [];
                $response['data'] = $getDiscussion;
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
            }
            return $response;
        } catch (Exception $e) {
            //dd($e);
            $response['status'] = 'false';
            $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
            return $response;
        }
    }

    public function getDiscussionGuestGuruListing($request)
    {
    try {
        if ($request->type == "guest") {
            if (!empty($request->tagsearch) && isset($request->type) == "guest") {
                $searchTag = $request->tagsearch;
                $getDiscussionList = Discussion::with('user')->with(['tags' => function ($query) use ($searchTag) {
                    $query->where('name', 'like', '%' . $searchTag . '%');
                }])->orderby('id', 'DESC');

                if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                    $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                    $getDiscussionList = $getAllData['data'];
                } else {
                    $getDiscussionList = $getDiscussionList->get();
                }
            //print_r($getDiscussionList);exit;
            } else {
                $getCount =   Discussion::query();

                $getCount = $getCount->count();

                if ($getCount < 0) {
                    $response['status'] = 'false';
                    $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
                } else {
                    if ($request->filtertype == 'latest') {
                        $getDiscussionList = Discussion::with('user', 'tags')->orderby('id', 'DESC');
                    }
                    elseif ($request->filtertype == 'lastweek'){
                        $date = Carbon::today()->subDays(7);
                        $getDiscussionList = Discussion::where('created_at','>=',$date)->with('user','tags')->orderby('id', 'DESC');
                    }
                    elseif ($request->filtertype == 'thismonth'){
                        $date = Carbon::now()->month;
                        $getDiscussionList = Discussion::with('user','tags')->whereMonth('created_at', $date)->orderby('id', 'DESC');
                        // dd($getDiscussionList);
                    }
                    elseif ($request->filtertype == 'today'){
                        $date = Carbon::today();
                        $getDiscussionList = Discussion::with('user','tags')->whereDate('created_at', $date)->orderby('id', 'DESC');
                    }
                    /**get data with pagination start*/
                    if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                        $getAllData = $this->commonHelper->getPagination($getDiscussionList, $this->commonHelper->getSettingValue(6), $request->page);
                        $getDiscussionList = $getAllData['data'];
                    } else {
                        $getDiscussionList = $getDiscussionList->get();
                    }
                }
            }

            $totalCounts = "";

            if (isset($request->tagsearch)) {
                $result = array();
                foreach ($getDiscussionList as $key => $value) {
                    if (!empty($value['tags'])) {
                        $result[] = $value;
                    }
                }
            } else {
                $result = $getDiscussionList;
            }
            $totalCounts = count($result);
            $getAllDiscussion = [];
            // echo "<pre>";
            // print_r($result);exit;
            foreach ($result as $key => $getDiscussionList) {
                $id = $getDiscussionList['id'];
                $userId = $getDiscussionList['user_id'];

                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                /**get image url */
                $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);
                $getDiscussionList['user']['profile'] = $this->commonHelper->getProfileImage($profileImage);

                /**get like listing */
                $getTotalLikes =  $this->getTotalLikes($id);
                $getDiscussionList['like_count'] = $getTotalLikes;
                /**get like listing end*/

                $isLikeByUser = DiscussionLike::where('discussion_id', $id)->where('from_user_id', $request->user_id)->where('is_like', 1)->count();
                $isLikeByUser > 0 ? $getDiscussionList['is_like'] = "true" : $getDiscussionList['is_like'] = "false";

                $getDiscussionList['comment_count'] = "";
                $getCommentListing  =  $this->discussionReplyListing($id);

                if(!empty($getCommentListing['status']) && $getCommentListing['status'] == "true") {
                    $getDiscussionList['comment_count'] = $getCommentListing['data']['discussion_reply_count'];
                } else{

                    $getDiscussionList['comment_count'] = "";
                }

                $getAllDiscussion['discussion'][$key] = $getDiscussionList;
            }
            if (count($getAllDiscussion) > 0) {
                if (isset($request->pagination) && ($request->pagination == 'true')) {
                    $getAllDiscussion['total_counts'] = $totalCounts;
                    $getAllDiscussion['total_pages'] = $getAllData['total_pages'];
                    $getAllDiscussion['curent_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllDiscussion;
                } else {
                    $getDiscussion['discussion'] = $result->toArray();
                    $response['status'] = 'true';
                    $response['data'] = $getDiscussion;
                }
            } else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
            }
            return $response;
        }
            else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.NOT_GUEST');
            }
        } catch (Exception $e) {
            //dd($e);
            $response['status'] = 'false';
            $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
            return $response;
        }
    }

    /*
        Function name : getTotalLikes
        Description : to get like count for feed
        Developed by : Pratik Prajapati
        Date : 08/10/2022
    */
    public function getTotalLikes($id)
    {
        $count =  DiscussionLike::where('discussion_id', $id)->where('is_like', 1)->count();
        if ($count) {
            return $count;
        } else {
            return "";
        }
    }

    /*
        Function name : getTotalLikes
        Description : to get like count for feed
        Developed by : Pratik Prajapati
        Date : 08/10/2022
    */
    public function getReplyTotalLikes($id)
    {
        $count =  DiscussionLike::where('discussion_reply_id', $id)->where('is_like', 1)->count();
        if ($count) {
            return $count;
        } else {
            return "";
        }
    }

    /*
        Function name : discussionReplyListing
        Description : to get reply listing of discussion and count
        Developed by : Srushti Shah
        Date : 11/10/2022
    */

    public function discussionReplyListing($discussionId)
    {
        try {
            $query = DiscussionReply::with('user')->where('discussion_id', $discussionId)->where('discussion_reply_id', null)->orderby('id', 'DESC');
            $allFeedCommentCount = $query->count();
            if ($allFeedCommentCount > 0) {
                $getAllFeedComment  = $query->get();
                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                foreach ($getAllFeedComment as $key => $singleComment) {
                    $profileImage = $this->commonHelper->getImages($singleComment->user->id, $profileImageType, $pathProfile);
                    $singleComment['user']['profile'] = $this->commonHelper->getProfileImage($profileImage);
                    // $singleComment['user']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";
                }
                $response['status'] = true;
                $response['data']['discussion']  = $getAllFeedComment->toArray();
                $response['data']['discussion_reply_count']  = $allFeedCommentCount;
            } else {
                $response['status']  = false;
                $response['message'] = trans('api_messages.error.NO_COMMENT_FOUND');
            }
            return $response;
        } catch (Exception $e) {
            $response['status'] = false;
            $response['message'] = $e;
        }
    }

    /*
        Function name : discussionReplyToReplyListing
        Description : to get reply listing of discussion and count of reply
        Developed by : Srushti Shah
        Date : 11/10/2022
    */
    public function discussionReplyToReplyListing($discussionId)
    {
        try {
            $query = DiscussionReply::where("discussion_reply_id", $discussionId)->orderby('id', 'DESC');
            $allFeedCommentCount = $query->count();
            if ($allFeedCommentCount > 0) {
                $getAllFeedComment  = $query->get();
                $response['status'] = true;
                $response['data']['discussion']  = $getAllFeedComment->toArray();
                $response['data']['discussion_reply_count']  = $allFeedCommentCount;
            } else {
                $response['status']  = false;
                $response['message'] = trans('api_messages.error.NO_COMMENT_FOUND');
            }
            return $response;
        } catch (Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
        }
    }
}
