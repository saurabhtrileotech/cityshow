<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        return view('city.index');
    }

    public function getList(Request $request)
    {
        
        $page_length = $request->length;
        $search_text = $request->search['value'];
        $offset = $request->start;

        $data = [];
        $order_column = $request->order[0]['column'];
        $order_by = $request->order[0]['dir'];

        $sql_query = City::select('cities.*');

        if (!empty($search_text)) {
            $split_array = explode(' ', $search_text);
            $sql_query->where(function ($q) use ($split_array) {
                foreach ($split_array as $txt) {
                    $q->orWhere('cities.name', 'like', '%' . $txt . '%');
                    $q->orWhere('cities.postcode', 'like', '%' . $txt . '%');
                }
            });
        }
        if ($order_column == 0) {
            $sql_query->orderBy('cities.name', $order_by);
        }
        else if($order_column == 1) {
            $sql_query->orderBy('cities.postcode', $order_by);
        }
        else {
            $sql_query->orderBy('cities.create_at', "desc");
        }
        $query = clone $sql_query;
        $query->offset($offset)->limit($page_length);
        $list_data = $query->get();
        $list_data_count = $sql_query->get()->count();
        foreach ($list_data as $key => $val) {
            $action = '';
            $action .= '<a href="' . route('city.edit', ['id' => $val->id]) . '" title="edit" class="btn btn-warning btn-icon btn-sm edit" style="margin-right:5px;"><i class="fa fa-edit"></i></a>
            <a   title="delete" class="btn btn-danger btn-icon btn-sm js-city-delete" data-id="'.$val->id.'"><i class="fa fa-trash"></i></a>';
            //  $action .= '<a onclick="return deleteconfirm()" href="' . route('student.delete', array('id' => $val->id)) . '" title="delete" class="btn btn-danger btn-icon btn-sm remove"><i class="fa fa-trash"></i></a>';
            $nestedData['name'] = $val->name;
            $nestedData['postcode'] = $val->postcode;
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
        return view('city.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:cities',
        ]);
        try {
            $category = new City();
            $category->name  = $request->name;
            $category->postcode = !empty($request->postcode) ? $request->postcode : null;
            $category->save();
            return redirect()->route('cities')->with('success', 'city added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $city = City::find($id);
            if ($city) {
                return view('city.edit', compact('city'));
            } else {
                return redirect()->back()->with('error', 'city not found found');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|unique:cities,name,' . $id
        ]);

        try {
            $city = City::find($id);
            $city->name = $request->name;
            $city->postcode = !empty($request->postcode) ? $request->postcode : null;
            $city->save();
            return redirect()->route('cities')
                ->with('success', 'City updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $city = City::find($id);
            if(!empty($city)){
                $city->delete();
                $response['status'] = true;
                $response['message'] = "city deleted successfully!";
                return $response;
            }
            $response['status'] = false;
            $response['message'] = "city not found!";
            return $response;
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = "Something went wrong!";
        }
    }
}
