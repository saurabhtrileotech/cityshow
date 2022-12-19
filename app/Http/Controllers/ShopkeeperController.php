<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Hash;

class ShopkeeperController extends Controller
{
    public function index()
    {
        return view('shopkeeper.index');
    }

    public function getList(Request $request)
    {
        
        $page_length = $request->length;
        $search_text = $request->search['value'];
        $offset = $request->start;

        $data = [];
        $order_column = $request->order[0]['column'];
        $order_by = $request->order[0]['dir'];

        $sql_query = User::select('users.*');

        $sql_query =  $sql_query->whereHas('roles', function ($query) {
            $query->whereIn('name', ['shop_keeper']);
        });

        if (!empty($search_text)) {
            $split_array = explode(' ', $search_text);
            $sql_query->where(function ($q) use ($split_array) {
                foreach ($split_array as $txt) {
                    $q->orWhere('users.username', 'like', '%' . $txt . '%');
                    //$q->orWhere('users.lastname', 'like',  '%' . $txt . '%');
                   // $q->orWhere('users.phone_number', 'like', '%' . $txt . '%');
                    $q->orWhere('users.email', 'like', '%' . $txt . '%');
                }
            });
        }
        if ($order_column == 0) {
            $sql_query->orderBy('users.username', $order_by);
        }
        // else if ($order_column == 1) {
        //     $sql_query->orderBy('users.lastname', $order_by);
        // } else if ($order_column == 2) {
        //     $sql_query->orderBy('users.phone_number', $order_by);
        // }
        else if ($order_column == 2) {
            $sql_query->orderBy('users.email', $order_by);
        } else if ($order_column == 3) {
            $sql_query->orderBy('users.status', $order_by);
        } else {
            $sql_query->orderBy('users.create_at', "desc");
        }
        $query = clone $sql_query;
        $query->offset($offset)->limit($page_length);
        $list_data = $query->get();
        $list_data_count = $sql_query->get()->count();
        foreach ($list_data as $key => $val) {
            $action = '';
            $action .= '<a href="' . route('shopkeeper.edit', ['id' => $val->id]) . '" title="edit" class="btn btn-warning btn-icon btn-sm edit" style="margin-right:5px;"><i class="fa fa-edit"></i></a><a href="' . route('shopkeeper.view', ['id' => $val->id]) . '" title="view" class="btn btn-primary btn-icon btn-sm view" style="margin-right:5px;"><i class="fa fa-eye"></i></a>';
            //$action .= '<a href="' . route('shopkeeper.edit', ['id' => $val->id]) . '" title="Shops" class="btn btn-primary edit" style="margin-right:5px;">Shops</a>';
            //$action .= '<a onclick="return deleteconfirm()" href="' . route('student.delete', array('id' => $val->id)) . '" title="delete" class="btn btn-danger btn-icon btn-sm remove"><i class="fa fa-trash"></i></a>';
            $nestedData['username'] = $val->username;
            $nestedData['email'] = $val->email;
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
        return view('shopkeeper.create');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'username' => 'required',   
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);


        try {
            $user = new User();
            $user->username =  $request->username;
            $user->email =  $request->email;
            $user->password = Hash::make($request->password);
            $user->status = 1;
            $profile_pic = $request->file('profile_picture');
            if ($profile_pic) {
                $ext = $profile_pic->getClientOriginalExtension();
                $newFileName = time() . '_' . rand(0, 1000) . '.' . $ext;
                $destinationPath = '/profile_pic/';
                /**create folder  **/
                $destinationPath = base_path() . '/public/profile_pic/';
                if (!file_exists($destinationPath)) {
                    \File::makeDirectory($destinationPath, 0777, true);
                    chmod($destinationPath, 0777);
                }
                $profile_pic->move($destinationPath, $newFileName);
                $user->profile_pic = $newFileName;
            }
            if($user->save()){
               $user->assignRole('shop_keeper');
            }
            return redirect()->route('shopkeepers')->with('success', 'Shopkeeper added successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $shopkeeper = User::find($id);
            if ($shopkeeper) {
                return view('shopkeeper.edit', compact('shopkeeper'));
            } else {
                return redirect()->back()->with('error', 'Shopkeeper not found');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username'   => 'required',
            'email'      => 'required|email|unique:users,email,' . $id,
        ]);

        try {
            $shopkeeper = User::find($id);
            $shopkeeper->username = $request->username;
            $shopkeeper->email = $request->email;
            $shopkeeper->status = $request->status;
            $shopkeeper->save();
            return redirect()->route('shopkeepers')
                ->with('success', 'Shopkeeper updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            return redirect()->route('camp_manager')
                ->with('success', 'Camp manager deleted successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }

    public function view($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                return view('shopkeeper.view', compact('user'));
            } else {
                return redirect()->back()->with('error', 'User not  found');
            }
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong');
        }
    }
}
