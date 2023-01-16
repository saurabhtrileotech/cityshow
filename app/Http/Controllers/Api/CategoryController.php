<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Api\ResponseHelper;
use App\Helpers\Api\CommonHelper;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\CategoryImages;
use Validator;
use Auth;
use Exception;
class CategoryController extends Controller
{
    private $responseHelper;

    public function __construct(
        ResponseHelper $responseHelper,
        CommonHelper $commonHelper
    ) {
        $this->responseHelper = $responseHelper;
        $this->commonHelper = $commonHelper;
    }

    public function list(Request $request){
        try{
            $categories =  Category::with('categoryImage','subCategory','shops');
            $categoryCount =  $categories->count();
            if($categoryCount <= 0){
                $response['categories'] = [];
                return $this->responseHelper->success('Category not found', $response);   
            }

                if(isset($request->page) && isset($request->pagination)){
                    $limit = isset($request->limit) ? $request->limit : 10;
                    $page = ($request->page > 0) ? $request->page : 1; 
                    $categories = $categories->limit($limit)->offset(($page - 1) * $limit)->get()->toArray();
    
                    $response['total_counts'] = $categoryCount;
                    $response['total_pages'] = $categoryCount != 0 ? ceil($categoryCount / $limit) : 0;
    
                }else{
                    $categories = $categories->get()->toArray();
                }
                $response['categories'] = $categories;

                return $this->responseHelper->success('Category gets successfully', $response);                
            
        }
        catch(Exception $e){
            dd($e);
            return $this->responseHelper->error($e);
        }
    }
}
