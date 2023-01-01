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
use App\Models\Feed;
use App\Models\Advert;
use App\Models\FeedLike;
use App\Models\FeedComment;
use App\Models\Bookmark;
use App\Models\Following;
use App\Models\BookCategory;
use App\Models\User;
use App\Models\Service;


class FeedHelper
{
    private $responseHelper;
    public function __construct(ResponseHelper $responseHelper, CommonHelper $commonHelper)
    {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
        // $this->user  = Auth::user();
        // $this->user_id =  Auth::user()->id();
    }

 /*
        Function name : getFeedListing
        Description : to get feed
        Developed by : Shrusti Shah
        Date : 08/10/2022
*/

    public function getFeedListing($request)
    {
        try {
            $getAdvertList = Advert::where('status','active')->orderBy('id', 'DESC')->get()->toArray();
            $path = config('constant.user_feeddoc_path');
            $type = config('constant.type.feed_documents_type');
            $getFeedList=[];
            $getFeedList = Feed::with('guru','tags')->orderby('id','DESC');
            if (!empty($request->id)) {
                // echo "only id";
                $getFeedList = $getFeedList->where('id', $request->id);
            }
            if(Auth::user()->id == $request->guru_id && Auth::user()->type == "guru"){
                // echo "guru login id";
                $getFeedList = $getFeedList->where('guru_id',$request->guru_id);
            }
            if(!empty($request->type)) {
                // echo "type";
                $type = $request->type;
                if ($type == "free") {
                    $getFeedList = $getFeedList->where('feed_type','free');
                }else{
                    $getFeedList = $getFeedList->where('feed_type', 'premium');
                }
            }
            if(!empty($request->tagsearch)) {
                // echo "tagsearch only";
                $searchTag = $request->tagsearch;
                $searchTagArray = explode(',',$searchTag);
                $getFeedList = $getFeedList;
                $getFeedList->where(function($q) use($searchTagArray) {
                foreach ($searchTagArray as $txt) {
                    $q->orWhereHas('tags', function($query) use($txt) {
					   $query->where('name', 'like', '%'. $txt . '%');
				    });
                 }
                });
            }
            if(!empty($request->tagsearch) && !empty($request->type)) {
                // echo "tagsearch and type both";
                $searchTag = $request->tagsearch;
                $searchTagArray = explode(',',$searchTag);
                $getFeedList = $getFeedList;
                $getFeedList->where(function($q) use($searchTagArray) {
                foreach ($searchTagArray as $txt) {
                    $q->orWhereHas('tags', function($query) use($txt) {
					   $query->where('name', 'like', '%'. $txt . '%');
				    });
                 }
                })->where('feed_type', $request->type);
            }
            if(!empty($request->guru_name)) {
                // echo "only guru";
                $guruName = $request->guru_name;
                $guruArray = explode(',',$guruName);
                $getFeedList = $getFeedList;
                $getFeedList->where(function($q) use($guruArray) {
                foreach ($guruArray as $txt) {
                    $q->orWhereHas('guru', function($query) use($txt) {
                        $query->where('first_name', 'like', '%' . $txt . '%');
                        $query->orwhere('last_name', 'like', '%' . $txt . '%');
                        $query->orwhere('guru_name', 'like', '%' . $txt . '%');
				    });
                 }
                });
            }
            if(!empty($request->guru_name) && !empty($request->tagsearch) && !empty($request->type)) {
                // echo "guru search , tag , and type";
                $searchTag = $request->tagsearch;
                $searchTagArray = explode(',',$searchTag);
                $guruName = $request->guru_name;
                $guruArray = explode(',',$guruName);

                $getFeedList = $getFeedList;
                $getFeedList->where(function($q) use($searchTagArray) {
                foreach ($searchTagArray as $txt) {
                    $q->orWhereHas('tags', function($query) use($txt) {
					   $query->where('name', 'like', '%'. $txt . '%');
				    });
                 }
                });
            }
            if (!empty($request->feedContentType)) {
                $feedContentType = $request->feedContentType;
                if ($feedContentType == "blog") {
                    $getFeedList = $getFeedList->where('type','blog');
                }elseif ($feedContentType == "vlog"){
                    $getFeedList = $getFeedList->where('type', 'vlog');
                }else{
                    $getFeedList = $getFeedList->where('type', 'podcast');
                }
            }
            if(!empty($request->feedsearch) && !empty($request->guru_name)) {
                // echo "guru and feed search";
                $getCount =   Feed::query();
                if(!empty($request->feedsearch))
                {
                    $getCount=$getCount->where('name', 'like', '%'. $request->feedsearch .'%');
                    $srh_feed=$request->feedsearch;
                    $getCount->orWhere(function($q) use($srh_feed)
                    {
                        $q->orWhereHas('guru', function($query) use($srh_feed) {
                            $query->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', '%' . $srh_feed . '%');
                        });
                    });
                }
                //end added
                $getCount = $getCount->count();

                /**check if data not found then return back */
                if ($getCount < 0) {
                    $response['status'] = false;
                    $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
                } else {
                    $getFeedList = $getFeedList;
                        if(!empty($request->feedsearch))
                        {
                            $getFeedList=$getFeedList->where('name', 'like', '%'. $request->feedsearch .'%');
                            $srh_feed=$request->feedsearch;
                            $getFeedList->orWhere(function($q) use($srh_feed)
                            {
                                $q->orWhereHas('guru', function($query) use($srh_feed) {
                                    $query->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'LIKE', '%' . $srh_feed . '%');
                                    //$query->where('first_name', 'like', '%'. $srh_feed . '%');
                                });
                            });
                        }
                    if (Auth::user()->is_subscribe == "false") {
                        $getFeedList = $getFeedList->where('feed_type', config('constant.feedTypeFree'));
                    }
                }
            }
            //Final one call for pagination here.....
            if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                $getAllData = $this->commonHelper->getPagination($getFeedList, $this->commonHelper->getSettingValue(6), $request->page);
                $getFeedList = $getAllData['data'];
            } else {
                // die("FINA");
                $getFeedList = $getFeedList->orderby('id', 'DESC')
                    //->toSql();
                    ->get()
                    ->toArray();
                // dd($getFeedList);
            }

            $getAllFeed = [];

            if (isset($request->tagsearch) && empty($request->type)) {
                $result = array();
                foreach ($getFeedList as $key => $value) {
                    if (!empty($value['tags'])) {
                        $result[] = $value;
                    }
                }
            } elseif (isset($request->tagsearch) && isset($request->type)) {
                $result = array();
                foreach ($getFeedList as $key => $value) {
                    if (!empty($value['tags'])) {
                        $result[] = $value;
                    }
                }
            }else {
                $result = $getFeedList;
            }

            foreach ($result as $key => $getOneFeedList) {
                $userId = $getOneFeedList['guru_id'];
                $id = $getOneFeedList['id'];
                $subCategoryId = $getOneFeedList['sub_category_id'];
                // $type = $getOneFeedList['type'];

                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                /**get image url */
                $profileImage = $this->commonHelper->getImages($userId,$profileImageType,$pathProfile);
                $getOneFeedList['guru']['profile'] = $this->commonHelper->getProfileImage($profileImage);

                // $getOneFeedList['guru']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";

                // $getOneFeedList['profileImage'] = "";
                // if ($profileImage['status'] == 'true') {
                //     $getOneFeedList['profileImage'] = $profileImage['data'];
                // }

                $pathSubCategory = config('constant.book_subcategory_image_path');
                $subCategory = config('constant.type.book_subcategory_documents_type');
                /**get image url */
                $subCategoryImage = $this->commonHelper->getImages($subCategoryId,$subCategory,$pathSubCategory);
                $getOneFeedList['book_subcategeory_image'] = "";
                if ($subCategoryImage['status'] == 'true') {
                    $getOneFeedList['book_subcategeory_image'] = $subCategoryImage['data'];
                }

                $file =  $this->commonHelper->getImages($id, $type, $path);
                $getOneFeedList['file'] = "";
                if ($file['status'] == 'true') {
                    $getOneFeedList['file'] = $file['data'];
                }

                $pathFeedImageFile = config('constant.user_feedimgdoc_path');
                $feedImageDocumentsType = config('constant.type.feed_image_documents_type');

                $subCategoryImage = $this->commonHelper->getImages($id,$feedImageDocumentsType,$pathFeedImageFile);
                $getOneFeedList['feed_image'] = [];
                if ($subCategoryImage['status'] == 'true') {
                    $getOneFeedList['feed_image'] = $subCategoryImage['data'];
                }

                    $pathFeedBlogImageFile = config('constant.user_feedimgblogdoc_path');
                    $feedImageBlogDocumentsType = config('constant.type.feed_image_blog_documents_type');

                    $feedBlogImage = $this->commonHelper->getImages($id, $feedImageBlogDocumentsType, $pathFeedBlogImageFile);
                    $getOneFeedList['feed_blog_image'] = [];
                    if ($feedBlogImage['status'] == 'true') {
                        $getOneFeedList['feed_blog_image'] = $feedBlogImage['data'];
                    }

                $subcategoryName = BookCategory::where('id',$subCategoryId)->count();
                if($subcategoryName > 0){
                    $subcategoryName = BookCategory::where('id',$subCategoryId)->first()->toArray();
                    $getOneFeedList['subcategory'] = $subcategoryName;
                }

                /**get like listing */
                $getTotalLikes =  $this->getTotalLikes($id);
                $getOneFeedList['like_count'] = $getTotalLikes;
                /**get like listing end*/
                $isLikeByUser = FeedLike::where('feed_id', $id)->where('user_id', $this->commonHelper->userdetails()->id)->where('is_like', 1)->count();
                $isLikeByUser > 0 ? $getOneFeedList['is_like'] = "true" : $getOneFeedList['is_like'] = "false";
                /**get comment listing */

                $isBookmarkByUser = Bookmark::where('feed_id', $id)->where('user_id',$this->commonHelper->userdetails()->id)->where('bookmark_type','feed')->where('is_bookmark', 1)->count();
                $isBookmarkByUser > 0 ? $getOneFeedList['is_bookmark'] = "true" : $getOneFeedList['is_bookmark'] = "false";

                $isFollowingByUser = Following::where('guru_id',$userId)->where('customer_id',$this->commonHelper->userdetails()->id)->where('is_follow', 1)->count();
                $isFollowingByUser > 0 ? $getOneFeedList['is_follow'] = "true" : $getOneFeedList['is_follow'] = "false";

                // $getOneFeedList['comment_list'] = "";
                $getOneFeedList['comment_count'] = "";
                $getCommentListing  =  $this->commentListing($id);
                if ($getCommentListing['status'] == 'true') {
                    // $getOneFeedList['comment_list'] = $getCommentListing['data']['comment'];
                    $getOneFeedList['comment_count'] = $getCommentListing['data']['comment_count'];
                }
                /**get comment listing end*/

                $is_subscribe = User::where('id',$userId)->where('is_subscribe', 1)->count();
                $is_subscribe > 0 ? $getOneFeedList['is_subscribe'] = "true" : $getOneFeedList['is_subscribe'] = "false";
                $is_subscribe > 0 ? $getOneFeedList['is_membership'] = "premium" : $getOneFeedList['is_membership'] = "free";
                // $getOneFeedList['is_membership'] = "free";

                $serviceTotal = Service::where('user_id',$userId)->count();
                $serviceTotal > 0 ? $getOneFeedList['totalService'] = "true" : $getOneFeedList['totalService'] = "false";

                $getAllFeed['feed'][$key] = $getOneFeedList;
            }

            foreach ($getAdvertList as $key => $getOneAdvertList) {

                $pathAdvert = config('constant.advert_image_path');
                $advertDocumentsType = config('constant.type.advert_documents_type');
                /**get image url */
                $advertImage = $this->commonHelper->getImages($getOneAdvertList['id'],$advertDocumentsType,$pathAdvert);

                $getOneAdvertList['advert_image'] = isset($advertImage['data']) ? $advertImage['data'] : "";
                // dd($getOneAdvertList);
                $getAdvertList[$key] = $getOneAdvertList;

            }



            if (count($getAllFeed) > 0) {
                if (isset($request->pagination) && ($request->pagination == 'true')) {
                    $countadvert = Advert::where('status','active')->orderBy('id', 'DESC')->count();
                    if($countadvert > 0){
                        $getAllFeed['advert_count'] = $this->commonHelper->getSettingValue(1);
                    }
                    else{
                        $getAllFeed['advert_count'] = "0";
                    }
                    // $getAllFeed['advert_count'] = $this->commonHelper->getSettingValue(1);
                    $getAllFeed['total_counts'] = $getAllData['total_counts'];
                    $getAllFeed['total_pages'] = $getAllData['total_pages'];
                    $getAllFeed['current_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllFeed;
                    $response['data']['advert'] = $getAdvertList;
                }
                else{
                    //$getAllFeed['discussion'] = $getOneFeedList->toArray();
                    $response['status'] = 'true';
                    $response['data'] = $getAllFeed;
                    $response['data']['advert'] = $getAdvertList;
                }

            } else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
            }
            return $response;
        } catch(Exception $e) {
            $response['status'] = 'false';
            $response['message'] = $e->getMessage();
        }
    }

     /*
        Function name : getGuestFeedListing
        Description : to get feed
        Developed by : Shrusti Shah
        Date : 08/10/2022
*/

public function getGuestFeedListing($request)
{

    try {
        if ($request->type == "guest") {
            $getAdvertList = Advert::where('status','active')->orderBy('id', 'DESC')->get()->toArray();
            $path = config('constant.user_feeddoc_path');
            $type = config('constant.type.feed_documents_type');
            $getFeedList=[];
            if (isset($request->id)) {
                $getFeedList = Feed::with('guru', 'tags')->where('id', $request->id)->get()->toArray();
            } elseif (isset($request->user_id) && $request->type == "guest") {
                $getFeedList = Feed::with('guru', 'tags')->where('guru_id', $request->user_id)->orderby('id', 'DESC');

                if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                    $getAllData = $this->commonHelper->getPagination($getFeedList, $this->commonHelper->getSettingValue(6), $request->page);
                    $getFeedList = $getAllData['data'];
                } else {
                    $getFeedList = $getFeedList->get()->toArray();
                }
            } elseif (isset($request->tagsearch)) {
                $searchTag = $request->tagsearch;
                $getFeedList = Feed::with('guru')->has('tags')->with(['tags' => function ($query) use ($searchTag) {
                    $query->where('name', 'like', '%' . $searchTag . '%');
                }])->orderby('id', 'DESC');

                if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                    $getAllData = $this->commonHelper->getPagination($getFeedList, $this->commonHelper->getSettingValue(6), $request->page);

                    $getFeedList = $getAllData['data'];
                } else {
                    $getFeedList = $getFeedList->get()->toArray();
                }
            } elseif (empty($request->id) && empty($request->user_id) && empty($request->tagsearch)) {
                $getCount =   Feed::query();
                // if (Auth::user()->is_subscribe == false) {
                    //     $getCount = $getCount->where('feed_type', config('constant.feedTypeFree'));
                // }
                $getCount = $getCount->count();

                /**check if data not found then return back */
                if ($getCount < 0) {
                    $response['status'] = false;
                    $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
                } else {
                    $getFeedList = Feed::with('guru', 'tags');
                    $getFeedList = $getFeedList->orderBy('id', 'DESC');

                    // if (Auth::user()->is_subscribe == false) {
                        //     $getFeedList = $getFeedList->where('feed_type', config('constant.feedTypeFree'))->orderBy('id', 'DESC');
                    // }
                    /**get data with pagination start*/
                    if (isset($request->pagination) && isset($request->page) && ($request->pagination == 'true')) {
                        $getAllData = $this->commonHelper->getPagination($getFeedList, $this->commonHelper->getSettingValue(6), $request->page);
                        $getFeedList = $getAllData['data'];
                    } else {
                        $getFeedList = $getFeedList->get()->toArray();
                    }
                }
            }
            $getAllFeed = [];

            if (isset($request->tagsearch)) {
                $result = array();
                foreach ($getFeedList as $key => $value) {
                    if (!empty($value['tags'])) {
                        $result[] = $value;
                    }
                }
            } else {
                $result = $getFeedList;
            }
            foreach ($result as $key => $getOneFeedList) {
                $userId = $getOneFeedList['guru_id'];
                $id = $getOneFeedList['id'];
                $subCategoryId = $getOneFeedList['sub_category_id'];

                $pathProfile = config('constant.profile_image_path');
                $profileImageType = config('constant.type.profile_image_type');
                /**get image url */
                $profileImage = $this->commonHelper->getImages($userId, $profileImageType, $pathProfile);
                $getOneFeedList['guru']['profile'] = $this->commonHelper->getProfileImage($profileImage);

                // $getOneFeedList['guru']['profile'] = isset($profileImage['data']) ? $profileImage['data'] : "";

                // $getOneFeedList['profileImage'] = "";
                // if ($profileImage['status'] == 'true') {
                    //     $getOneFeedList['profileImage'] = $profileImage['data'];
                // }

                $pathSubCategory = config('constant.book_subcategory_image_path');
                $subCategory = config('constant.type.book_subcategory_documents_type');
                /**get image url */
                $subCategoryImage = $this->commonHelper->getImages($subCategoryId, $subCategory, $pathSubCategory);
                $getOneFeedList['book_subcategeory_image'] = "";
                if ($subCategoryImage['status'] == 'true') {
                    $getOneFeedList['book_subcategeory_image'] = $subCategoryImage['data'];
                }

                $file =  $this->commonHelper->getImages($id, $type, $path);
                $getOneFeedList['file'] = "";
                if ($file['status'] == 'true') {
                    $getOneFeedList['file'] = $file['data'];
                }

                $pathFeedImageFile = config('constant.user_feedimgdoc_path');
                $feedImageDocumentsType = config('constant.type.feed_image_documents_type');

                $subCategoryImage = $this->commonHelper->getImages($id, $feedImageDocumentsType, $pathFeedImageFile);
                $getOneFeedList['feed_image'] = [];
                if ($subCategoryImage['status'] == 'true') {
                    $getOneFeedList['feed_image'] = $subCategoryImage['data'];
                }

                $pathFeedBlogImageFile = config('constant.user_feedimgblogdoc_path');
                $feedImageBlogDocumentsType = config('constant.type.feed_image_blog_documents_type');

                $feedBlogImage = $this->commonHelper->getImages($id, $feedImageBlogDocumentsType, $pathFeedBlogImageFile);
                $getOneFeedList['feed_blog_image'] = [];
                if ($feedBlogImage['status'] == 'true') {
                    $getOneFeedList['feed_blog_image'] = $feedBlogImage['data'];
                }

                /**get like listing */
                $getTotalLikes =  $this->getTotalLikes($id);
                $getOneFeedList['like_count'] = $getTotalLikes;
                /**get like listing end*/
                $isLikeByUser = FeedLike::where('feed_id', $id)->where('user_id', $request->user_id)->where('is_like', 1)->count();
                $isLikeByUser > 0 ? $getOneFeedList['is_like'] = "true" : $getOneFeedList['is_like'] = "false";
                /**get comment listing */
                // $getOneFeedList['comment_list'] = "";
                $getOneFeedList['comment_count'] = "";
                $getCommentListing  =  $this->commentListing($id);
                if ($getCommentListing['status'] == 'true') {
                    // $getOneFeedList['comment_list'] = $getCommentListing['data']['comment'];
                    $getOneFeedList['comment_count'] = $getCommentListing['data']['comment_count'];
                }
                /**get comment listing end*/
                $getOneFeedList['advert_count'] = $this->commonHelper->getSettingValue(1);

                $getAllFeed['feed'][$key] = $getOneFeedList;
            }

            foreach ($getAdvertList as $key => $getOneAdvertList) {

                $pathAdvert = config('constant.advert_image_path');
                $advertDocumentsType = config('constant.type.advert_documents_type');
                /**get image url */
                $advertImage = $this->commonHelper->getImages($getOneAdvertList['id'],$advertDocumentsType,$pathAdvert);

                $getOneAdvertList['advert_image'] = isset($advertImage['data']) ? $advertImage['data'] : "";
                // dd($getOneAdvertList);
                $getAdvertList[$key] = $getOneAdvertList;

            }


            if (count($getAllFeed) > 0) {
                if (isset($request->pagination) && ($request->pagination == 'true')) {
                    $getAllFeed['total_counts'] = $getAllData['total_counts'];
                    $getAllFeed['total_pages'] = $getAllData['total_pages'];
                    $getAllFeed['current_page'] = $request->page;
                    $response['status'] = 'true';
                    $response['data'] = $getAllFeed;
                    $response['data']['advert'] = $getAdvertList;
                } else {
                    //$getAllFeed['discussion'] = $getOneFeedList->toArray();
                    $response['status'] = 'true';
                    $response['data'] = $getAllFeed;
                    $response['data']['advert'] = $getAdvertList;
                }
            } else {
                $response['status'] = 'false';
                $response['message'] = trans('api_messages.error.FEED_NOT_FOUND');
            }
            return $response;
        } else {
            $response['status'] = 'false';
            $response['message'] = trans('api_messages.error.NOT_GUEST');
        }
    } catch(Exception $e) {
        $response['status'] = 'false';
        $response['message'] = $e->getMessage();
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
        $count =  FeedLike::where('feed_id', $id)->where('is_like', 1)->count();
        if ($count) {
            return $count;
        } else {
            return "";
        }
    }

    /*
        Function name : commentListing
        Description : to get comments listing for feed
        Developed by : Pratik Prajapati
        Date : 08/10/2022
    */

    public function commentListing($feedId)
    {
        try {
            $query = FeedComment::with('commentReply')->where('feed_id', $feedId);
            $allFeedCommentCount = $query->count();
            if ($allFeedCommentCount > 0) {
                $getAllFeedComment  = $query->get();
                $response['status'] = true;
                $response['data']['comment']  = $getAllFeedComment->toArray();
                $response['data']['comment_count']  = $allFeedCommentCount;
            } else {
                $response['status']  = false;
                $response['message'] = trans('api_messages.error.NO_COMMENT_FOUND');
            }
            return $response;
        } catch(Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage();
        }
    }
}
















