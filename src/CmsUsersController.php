<?php

namespace Xtnd\Cms;

use Xtnd\Cms\CmsUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;


class CmsUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show()
    {   
        
        return view('cms::login');
    }

    public function authenticate(Request $request)
    {   
        
        $validator = $request->validate([
            'email'     => 'required',
            'password'  => 'required|min:6'
        ]);
        
       
        if (Auth::guard('cms_user')->attempt($validator)) {
            return redirect('/cms/dashboard');
        }

        return back();
    }

    public function register()
    {

       $user = array(
           'name' => 'admin',
           'email' => 'admin1@admin.com',
            'password' => Hash::make('password')
       );
           
        CmsUser::create($user);

        return redirect('/cms/login');
     }

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect('/cms/login');
    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CmsUser  $cmsUser
     * @return \Illuminate\Http\Response
     */
    // public function show(CmsUser $cmsUser)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CmsUser  $cmsUser
     * @return \Illuminate\Http\Response
     */
    public function edit(CmsUser $cmsUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CmsUser  $cmsUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CmsUser $cmsUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CmsUser  $cmsUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(CmsUser $cmsUser)
    {
        //
    }
}
