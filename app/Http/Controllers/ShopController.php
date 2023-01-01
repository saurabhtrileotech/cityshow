<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use App\Models\ShopImage;
use Exception;
use App\Models\Category;

class ShopController extends Controller
{
    
    public function index()
    {
        return view('shop.index');
    }

    public function getList(Request $request)
    {
        
        $page_length = $request->length;
        $search_text = $request->search['value'];
        $offset = $request->start;

        $data = [];
        $order_column = $request->order[0]['column'];
        $order_by = $request->order[0]['dir'];

        $sql_query = Shop::select('shops.*');

        if (!empty($search_text)) {
            $split_array = explode(' ', $search_text);
            $sql_query->where(function ($q) use ($split_array) {
                foreach ($split_array as $txt) {
                    $q->orWhere('shops.shop_name', 'like', '%' . $txt . '%');
                    $q->orWhereHas('shopkeeper', function ($query) use ($txt) {
                        $query->where('username', 'like', '%' . $txt . '%');
                    });
                }
            });
        }
        if ($order_column == 0) {
            $sql_query->orderBy('shops.shop_name', $order_by);
        }
        else if ($order_column == 1) {
            $sql_query->WhereHas('shopkeeper', function ($query) use ($order_by) {
                $query->orderBy('username', $order_by);
            });
        }
        else {
            $sql_query->orderBy('users.create_at', "desc");
        }
        $query = clone $sql_query;
        $query->offset($offset)->limit($page_length);
        $list_data = $query->get();
        $list_data_count = $sql_query->get()->count();
        foreach ($list_data as $key => $val) {
            $action = '';
            $action .= '<a href="' . route('shop.edit', ['id' => $val->id]) . '" title="edit" class="btn btn-warning btn-icon btn-sm edit" style="margin-right:5px;"><i class="fa fa-edit"></i></a>
            <a href="' . route('shop.view', ['id' => $val->id]) . '" title="view" class="btn btn-primary btn-icon btn-sm view" style="margin-right:5px;"><i class="fa fa-eye"></i></a>
            <a   title="delete" class="btn btn-danger btn-icon btn-sm remove" data-id="'.$val->id.'" id="js-shop-delete"><i class="fa fa-trash"></i></a>';
            //$action .= '<a onclick="return deleteconfirm()" href="' . route('student.delete', array('id' => $val->id)) . '" title="delete" class="btn btn-danger btn-icon btn-sm remove"><i class="fa fa-trash"></i></a>';
            $nestedData['shop_name'] = $val->shop_name;
            $nestedData['shopkeeper'] = $val->shopkeeper->username;
            $nestedData['status'] = ($val->status == 0) ? 'In active' : 'Active';
            $nestedData['action'] = $action;
            $data[] = $nestedData;
        }
        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($list_data_count),
            "recordsFiltered" => intval($list_data_count),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    public function create()
    {
        $shop_keepers = User::select('id','username','email')->whereHas('roles', function ($query) {
            $query->whereIn('name', ['shop_keeper']);
            })->get();
        $categories = Category::select('id','name')->get();
        return view('shop.create',compact('shop_keepers','categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_keeper_id' => 'required',
            'shop_name' => 'required',
            'banner_image' => 'required|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
        ]);
        try {
            $shop = new Shop(); 
            $shop->user_id  = $request->shop_keeper_id;
            $shop->shop_name = $request->shop_name;
            $shop->category_id = ($request->category_id) ? $request->category_id : null;
            $shop->notes = ($request->notes) ? $request->notes : null;
            $shop->address = ($request->address) ? $request->address : null;
            $banner_image = $request->file('banner_image');
            if ($banner_image) {
                $ext = $banner_image->getClientOriginalExtension();
                $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/banner_image/' . $request->shop_keeper_id;
                /**create folder  **/
                $destinationPath = base_path() . '/public/banner_image/' . $request->shop_keeper_id . "/";
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $banner_image->move($destinationPath, $newFileName);
                $shop->banner = $newFileName;
            }
            $banner_video = $request->file('banner_video');
            if ($banner_video) {
                $ext = $banner_video->getClientOriginalExtension();
                $newVideo = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/banner_video/' . $request->shop_keeper_id;
                /**create folder  **/
                $destinationPath = base_path() . '/public/banner_video/' . $request->shop_keeper_id . "/";
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $banner_video->move($destinationPath, $newVideo);
                $shop->video = $newVideo;
            }

            $shop->save();

            // save Multiple images
            $images = $request->file('images');
            if ($images) {
                foreach($images as $image){
                    $shop_image = new ShopImage();
                    $shop_image->shop_id = $shop->id;
                    $ext = $image->getClientOriginalExtension();
                    $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                    $destinationPath = '/images/' . $shop->id;
                    /**create folder  **/
                    $destinationPath = base_path() . '/public/images/' . $shop->id . "/";
                    if (!file_exists($destinationPath)) {
                        \File::makeDirectory($destinationPath, 0777, true);
                        chmod($destinationPath, 0777);
                    }
                    $image->move($destinationPath, $newFileName);
                    $shop_image->image = $newFileName;
                    $shop_image->save();
                }
                
            }
            return redirect()->route('shops')->with('success', 'Shop added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $shop_keepers = User::select('id','username','email')->whereHas('roles', function ($query) {
                $query->whereIn('name', ['shop_keeper']);
                })->get();
            $categories = Category::select('id','name')->get();
            $shop = Shop::find($id);
            if ($shop) {
                // dd($shop);
                return view('shop.edit', compact('shop','shop_keepers','categories'));
            } else {
                return redirect()->back()->with('error', 'Shopkeeper not found');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request ,$id)
    {
        $validated = $request->validate([
            'banner_image' => 'mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:1024',
        ]);
        try {
            $shop = Shop::find($id);
            $shop->user_id  = $request->shop_keeper_id?$request->shop_keeper_id:$shop->shop_keeper_id;
            $shop->shop_name = $request->shop_name?$request->shop_name:$shop->shop_name;
            $shop->category_id = ($request->category_id) ? $request->category_id : $shop->category_id;
            $shop->notes = ($request->notes) ? $request->notes : $shop->notes;
            $shop->address = ($request->address) ? $request->address : $shop->address;
            $banner_image = $request->file('banner_image');
            if ($banner_image) {
                if($shop->banner){
                    \File::delete('/public/banner_image/' . $request->shop_keeper_id . "/".$shop->benner);
                }
                
                $ext = $banner_image->getClientOriginalExtension();
                $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/banner_image/' . $request->shop_keeper_id;
                /**create folder  **/
                $destinationPath = base_path() . '/public/banner_image/' . $request->shop_keeper_id . "/";
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $banner_image->move($destinationPath, $newFileName);
                $shop->banner = $newFileName;
            }
            $banner_video = $request->file('banner_video');
            if ($banner_video) {
                if($shop->video){
                    \File::delete('/public/banner_video/' . $request->shop_keeper_id . "/".$shop->video);
                }
                $ext = $banner_video->getClientOriginalExtension();
                $newVideo = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/banner_video/' . $request->shop_keeper_id;
                /**create folder  **/
                $destinationPath = base_path() . '/public/banner_video/' . $request->shop_keeper_id . "/";
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $banner_video->move($destinationPath, $newVideo);
                $shop->video = $newVideo;
            }

            $shop->update();

            // save Multiple images
            $images = $request->file('images');
            if ($images) {
                $oldImages = ShopImage::where('shop_id',$shop->id)->get();
                if(!empty($oldImages)){
                    foreach($oldImages as  $oldImage){
                        \File::delete('/public/images/' . $shop->id . "/".$oldImage->image);
                    }
                }
                ShopImage::where('shop_id',$shop->id)->delete();
                foreach($images as $image){
                    $shop_image = new ShopImage();
                    $shop_image->shop_id = $shop->id;
                    $ext = $image->getClientOriginalExtension();
                    $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                    $destinationPath = '/images/' . $shop->id;
                    /**create folder  **/
                    $destinationPath = base_path() . '/public/images/' . $shop->id . "/";
                    if (!file_exists($destinationPath)) {
                        \File::makeDirectory($destinationPath, 0777, true);
                        chmod($destinationPath, 0777);
                    }
                    $image->move($destinationPath, $newFileName);
                    $shop_image->image = $newFileName;
                    $shop_image->save();
                }
                
            }
            return redirect()->route('shops')->with('success', 'Shop update successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $shop = Shop::with('shopImages')->find($id);
            if($shop){
                if($shop->banner){
                    \File::delete('/public/banner_image/' . $request->shop_keeper_id . "/".$shop->benner);

                }
                if($shop->video){
                    \File::delete('/public/banner_video/' . $request->shop_keeper_id . "/".$shop->video);

                }
                $shop->delete();
                $oldImages = ShopImage::where('shop_id',$id)->get();
                if(!empty($oldImages)){
                    foreach($oldImages as  $oldImage){
                        \File::delete('/public/images/' . $shop->id . "/".$oldImage->image);
                    }
                }
                ShopImage::where('shop_id',$shop->id)->delete();
                $response['status'] = true;
                $response['message'] = "Shop Deleted successfully!";
                return $response;

            }
            $response['status'] = false;
            $response['message'] = "Shop Not Found!";
            return $response;
        } catch (Exception $e) {
            $response['status'] = false;
            $response['message'] = "Something went wrong!";
        }
    }

    public function view($id)
    {
        try {
            $shop = Shop::with('shopImages')->find($id);
            if ($shop) {
                $shop->banner = $shop->banner?URL('banner_image/' . $shop->user_id . "/".$shop->banner):'';
                $shop->video = $shop->video?URL('banner_video/' . $shop->user_id . "/".$shop->video):'';
                if($shop->shopImages){
                    $shopImage = [];
                    foreach($shop->shopImages as $key =>$image){
                        $image = URL('images/' . $shop->id . "/".$image['image']);
                        $shopImage[] = $image;
                    }
                    $shop->images =$shopImage; 
                    unset($shop->shopImages);
                }
                return view('shop.view', compact('shop'));
            } else {
                return redirect()->back()->with('error', 'Shop not  found');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }
}
