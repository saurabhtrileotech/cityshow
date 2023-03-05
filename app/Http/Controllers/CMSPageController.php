<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaticPage;
use Yajra\DataTables\Facades\DataTables;
use Validator;

class CMSPageController extends Controller
{
    public function index()
    {
        return view('cms_pages.index');
    }

    public function getCMSPageList(Request $request)
    {


        $data = StaticPage::orderBy('id', 'DESC')->get();

        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                return '<a href="' . route('cmsPages.edit', ['id' => $data->id]) . '" class="btn btn-primary btn-sm" title="Edit"><i class="fa fa-edit"></i></a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function create()
    {

        $static_keys = ['privacy_policy', 'terms_conditions', 'faq','about'];

        // foreach ($static_keys as $key => $static_key) {
        //     // Check the record exists
        //     $check_page = StaticPages::where('type', $static_key)->first();
        //     if($check_page) {
        //         unset($static_keys[$key]);
        //     }
        // }
        // $static_keys[] = 'others';
        return view('cms_pages.create', compact('static_keys'));
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'required',
                'type' => 'required|unique:static_pages',

            ], [
                'description.required' => 'description is required',
                'type.required' => 'Page type is required',
                'type.unique' => 'The page type has already been taken'
            ]);

            if ($validator->fails()) {
                return  redirect()->route('cmsPages.create')->with('error', $validator->messages()->first());
            }

            $page =  new StaticPage();
            $page->type = $request->type;
            $page->description = $request->description;
            $page->save();
            return  redirect()->route('cmsPages.index')->with('success', 'Page Successfully Added');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function edit($id)
    {
        try {
            $data = StaticPage::find($id);
            if (!$data) {
                return redirect()->route('cmsPages.index')->with('error', 'Page not found');
            }
            $static_keys = ['privacy_policy', 'terms_conditions', 'faq','about'];
            return view('cms_pages.edit', compact('static_keys', 'data'));
        } catch (\Exception $e) {
            return redirect()->route('cmsPages.index')->with('error', $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'description' => 'required',
                'type' => 'required|unique:static_pages,type,' . $id,
            ], [
                'description.required' => 'description is required',
                'type.required' => 'Page type is required',
                'type.unique' => 'The page type has already been taken'
            ]);

            if ($validator->fails()) {
                return  redirect()->route('cmsPages.edit', $request->id)->with('error', $validator->messages()->first());
            }
            $data = StaticPage::find($id);
            if (!$data) {
                return redirect()->route('cmsPages.index')->with('error', 'Page not found');
            }

            $data->type = $request->type;
            $data->description = $request->description;
            $data->update();

            return  redirect()->route('cmsPages.index')->with('success', 'Page Successfully Updated');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function delete($id)
    {
        $StaticPages   = StaticPages::find($id);
        if ($StaticPages) {
            $StaticPages->delete();
            return  redirect()->route('cmspage.index')->with('message', $StaticPages->type . " Page removed!");
        } else {
            return  redirect()->route('cmspage.index')->with('message', "Page  not found");
        }
    }
    public function getPrivacyPolicy()
    {
        try {
            $data = StaticPage::where('type', 'privacy_policy')->first();
            if (!$data) {
                return  abort(500);
            }
            return view('cms_pages.view', compact('data'));
        } catch (Exception $e) {
            return  abort(500);
        }
    }
    public function getTermsCondition()
    {
        try {
            $data = StaticPage::where('type', 'terms_conditions')->first();
            if (!$data) {
                return  abort(500);
            }
            return view('cms_pages.view', compact('data'));
        } catch (Exception $e) {
            return  abort(500);
        }
    }
    public function getFAQ()
    {
        try {
            $data = StaticPage::where('type', 'faq')->first();
            if (!$data) {
                return  abort(500);
            }
            return view('cms_pages.view', compact('data'));
        } catch (Exception $e) {
            return  abort(500);
        }
    }

    public function getAboutUs()
    {
        try {
            $data = StaticPage::where('type', 'about')->first();
            if (!$data) {
                return  abort(500);
            }
            return view('cms_pages.view', compact('data'));
        } catch (Exception $e) {
            return  abort(500);
        }
    }
}
