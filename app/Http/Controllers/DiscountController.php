<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Discount;
use App\Models\ShopDiscount;

class DiscountController extends Controller
{
    public function index()
    {
        return view('discount.index');
    }

    public function getList(Request $request)
    {
        $page_length = $request->length;
        $search_text = $request->search['value'];
        $offset = $request->start;

        $data = [];
        $order_column = $request->order[0]['column'];
        $order_by = $request->order[0]['dir'];

        $sql_query = Discount::select('discounts.*');

        if (!empty($search_text)) {
            $split_array = explode(' ', $search_text);
            $sql_query->where(function ($q) use ($split_array) {
                foreach ($split_array as $txt) {
                    $q->orWhere('discounts.coupon_name', 'like', '%' . $txt . '%');
                    $q->orWhere('discounts.coupon_code', 'like', '%' . $txt . '%');
                    $q->orWhere('discounts.price', 'like', '%' . $txt . '%');
                    $q->orWhere('discounts.percentage', 'like', '%' . $txt . '%');
                }
            });
        }
        if ($order_column == 0) {
            $sql_query->orderBy('discounts.coupon_name', $order_by);
        }
        else if ($order_column == 1) {
            $sql_query->orderBy('discounts.coupon_code', $order_by);
        }

        else if ($order_column == 2) {
            $sql_query->orderBy('discounts.price', $order_by);
        }

        else if ($order_column == 3) {
            $sql_query->orderBy('discounts.percentage', $order_by);
        }
        else {
            $sql_query->orderBy('discounts.create_at', "desc");
        }
        $query = clone $sql_query;
        $query->offset($offset)->limit($page_length);
        $list_data = $query->get();
        $list_data_count = $sql_query->get()->count();
        foreach ($list_data as $key => $val) {
            $action = '';
            $action .= '<a href="' . route('discount.edit', ['id' => $val->id]) . '" title="edit" class="btn btn-warning btn-icon btn-sm edit" style="margin-right:5px;"><i class="fa fa-edit"></i></a>';
            //$action .= '<a onclick="return deleteconfirm()" href="' . route('student.delete', array('id' => $val->id)) . '" title="delete" class="btn btn-danger btn-icon btn-sm remove"><i class="fa fa-trash"></i></a>';
            $nestedData['coupon_name'] = $val->coupon_name;
            $nestedData['coupon_code'] = $val->coupon_code;
            $nestedData['price'] =  ($val->price) ? $val->price : '-';
            $nestedData['percentage'] =  ($val->percentage) ? $val->percentage : '-';
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
        return view('discount.create',compact('shop_keepers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_keeper_id' => 'required',
            'shop_id' => 'required',
            'coupon_name' => 'required',
            'coupon_code' => 'required',
            'price' => 'sometimes',
            'percentage' => 'sometimes',
            'startdate'  => 'required',
            'enddate'    => 'required',
        ]);
        try {
            $discount = new Discount(); 
            $discount->coupon_name = $request->coupon_name;
            $discount->coupon_code = $request->coupon_code;
            $discount->is_price = $request->is_price;
            $discount->price = ($request->price) ? $request->price : null;
            $discount->percentage = ($request->percentage) ? $request->percentage : null;
            $discount->start_date = \Carbon\Carbon::parse($request->startdate)->format('Y-m-d H:i:s');
            $discount->end_date = \Carbon\Carbon::parse($request->enddate)->format('Y-m-d H:i:s');


           if($discount->save()){
                // Save Product to all shops
                if($request->shop_id){
                    foreach($request->shop_id as $shop_id){
                        $shop_discount = new ShopDiscount();
                        $shop_discount->shop_keeper_id = $request->shop_keeper_id;
                        $shop_discount->shop_id = $shop_id;
                        $shop_discount->discount_id = $discount->id;
                        $shop_discount->save();
                    }
                }
            }
            return redirect()->route('discounts')->with('success', 'Discount added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}