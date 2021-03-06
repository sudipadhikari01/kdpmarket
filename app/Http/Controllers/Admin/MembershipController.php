<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Member;
use App\SponsorRecruiter;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = Member::all();
        return view('backend.membership.index')->with('members', $members);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.membership.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'userID' => 'unique:members,userID'
        ]);

        Member::create($request->all());
        return redirect()->route('membership.index')->with('success', 'Member created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $member = Member::find($id);
        return view('backend.membership.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $member = Member::find($id);
        return view('backend.membership.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
     $request->validate([
            'name' => 'required',
        ]);
        $member = Member::find($id);

        $member->update($request->all());

        return redirect()->route('membership.index')
            ->with('success', 'Member updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = Member::find($id);
        $member->delete();

        return redirect()->route('membership.index')
            ->with('success', 'Member deleted successfully');
    }

    public function changeInfo()
    {
        $userIds = Member::select('userID', 'id')->get();
        // echo "<pre>";
        // print_r($userIds);
        // die;
        return view('backend.membership.changeInfo')->with('userIds', $userIds);
    }
    public function checkRecruiterInfo(Request $request)
    {
        $info = $request->all();
        $member = Member::select('name')->where('userID', $info['id'])->get();
        $checkrepeatation = Member::where('sponsor_id', '=', $info['id'])->get();
        $row_count = $checkrepeatation->count();
        return response()->json(['success' => 'Got the Request', 'data' => $member, 'count' => $row_count]);

        // return response()->json(['success' => 'Got the Request', 'data' => $member]);
    }

    public function checkUserID(Request $request)
    {
        dd($request);
    }

    public function getAllUserID(Request $request)
    {

    }

    public function checkPassword(Request $request)
    {
        //"curpwd":"adsf","pwd":"asdf","conpwd":"asdf"
        // $currentpassword = User::find(Auth::id());
        // return strcmp(bcrypt($request->curpwd), $currentpassword->password);
        return Auth::id();
        $user = User::find(Auth::id());
        $user->password = bcrypt($request->pwd);
        $response = $user->save();
        if ($response > 0) {
            return view('cauth.changepassword', ['msg' => 'Password Change Successfully']);
        }
    }
}
