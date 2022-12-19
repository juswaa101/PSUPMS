<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserManagementController extends Controller
{
    public function index()
    {
        try {
            $fetch = Project::orderByDesc('created_at')->limit(5)->get()->unique('project_title');
            $project = $fetch->unique('project_title');
            $user_profile = User::where('id', Auth::user()->id)->get();
            $notification = Notification::join('users', 'users.id', '=', 'notifications.user_id')
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('notifications.created_at')
                ->select(['notifications.id as notify_id', 'notifications.created_at as created', 'notifications.*', 'users.*'])
                ->get();
            $fetchLimitProject = Project::limit(5)->orderByDesc('created_at')->get()->unique('project_title');
            return view('admin.UserManagement', compact('project'), compact('notification'))
                ->with('user_profile', $user_profile)
                ->with('fetchLimitProject', $fetchLimitProject);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function fetchUsers()
    {
        try {
            return response()->json(['users' => User::whereNot('role', 'admin')->get()]);
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                $path = 'assets/users/' . $user->image;
                if (File::exists($path)) {
                    File::delete($path);
                }
                $user->delete();
                return response()->json([
                    'status' => 200,
                    'message' => 'User Deleted Successfully!',
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'User Not Found!',
                ]);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'full_name' => 'required',
                'username' => 'required',
                'email' => 'required|unique:users,email',
                'department' => 'required',
                'password' => 'required|min:6',
                'confirm_password' => 'required|same:password|min:6',
                'image' => 'required|image|mimes:jpg,png,jpeg|max:4096',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validate->getMessageBag(),
                ]);
            } else {
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;
                    $file->move('assets/users/', $filename);
                    $user = new User();
                    $user->image = $filename;
                    $user->username = $request->input('username');
                    $user->name = $request->input('full_name');
                    $user->email = $request->input('email');
                    $user->department = $request->input('department');
                    $user->password = Hash::make($request->input('password'));
                    $user->role = $request->input('role');
                    $user->save();
                    return response()->json([
                        'status' => 200,
                        'message' => 'User Data Added Successfully!',
                    ]);
                }
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function edit($id)
    {
        try {
            $user = User::find($id);
            if ($user) {
                return response()->json([
                    'status' => 200,
                    'users' => $user
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'User Not Found'
                ]);
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->all(), [
                'full_name' => 'required',
                'username' => 'required',
                'email' => 'required', Rule::unique('users')->ignore($id),
                'department' => 'required',
                'confirm_password' => 'same:password',
            ]);


            if ($validate->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validate->getMessageBag()
                ]);
            } else {
                if ($validate->fails()) {
                    return response()->json([
                        'status' => 400,
                        'errors' => $validate->getMessageBag(),
                    ]);
                } else {
                    if ($request->hasFile('image') && $request->input('password') != null && $request->input('confirm_password') != null) {
                        $user = User::find($id);
                        if ($user) {
                            $path = 'assets/users/' . $user->image;
                            if (File::exists($path)) {
                                File::delete($path);
                            }
                            $file = $request->file('image');
                            $extension = $file->getClientOriginalExtension();
                            $filename = time() . '.' . $extension;
                            $file->move('assets/users/', $filename);
                            $user->image = $filename;
                            $user->username = $request->input('username');
                            $user->name = $request->input('full_name');
                            $user->email = $request->input('email');
                            $user->department = $request->input('department');
                            $user->password = Hash::make($request->input('password'));
                            $user->role = $request->input('role');
                            $user->save();
                            return response()->json([
                                'status' => 200,
                                'message' => 'User Found!',
                            ]);
                        } else {
                            return response()->json([
                                'status' => 404,
                                'message' => 'User Not Found!',
                            ]);
                        }
                    } else if (!$request->hasFile('image') && ($request->input('password') != null && $request->input('confirm_password') != null)) {
                        $user = User::find($id);
                        if ($user) {
                            $user->username = $request->input('username');
                            $user->name = $request->input('full_name');
                            $user->email = $request->input('email');
                            $user->department = $request->input('department');
                            $user->password = Hash::make($request->input('password'));
                            $user->role = $request->input('role');
                            $user->save();
                            return response()->json([
                                'status' => 200,
                                'message' => 'User Found!',
                            ]);
                        } else {
                            return response()->json([
                                'status' => 404,
                                'message' => 'User Not Found!',
                            ]);
                        }
                    } else if (($request->input('password') == null && $request->input('confirm_password') == null) && $request->hasFile('image')) {
                        $user = User::find($id);
                        if ($user) {
                            $path = 'assets/users/' . $user->image;
                            if (File::exists($path)) {
                                File::delete($path);
                            }
                            $file = $request->file('image');
                            $extension = $file->getClientOriginalExtension();
                            $filename = time() . '.' . $extension;
                            $file->move('assets/users/', $filename);
                            $user->image = $filename;
                            $user->username = $request->input('username');
                            $user->name = $request->input('full_name');
                            $user->email = $request->input('email');
                            $user->department = $request->input('department');
                            $user->role = $request->input('role');
                            $user->save();
                            return response()->json([
                                'status' => 200,
                                'message' => 'User Found!',
                            ]);
                        } else {
                            return response()->json([
                                'status' => 404,
                                'message' => 'User Not Found!',
                            ]);
                        }
                    } else {
                        $user = User::find($id);
                        if ($user) {
                            $user->username = $request->input('username');
                            $user->name = $request->input('full_name');
                            $user->email = $request->input('email');
                            $user->department = $request->input('department');
                            $user->role = $request->input('role');
                            $user->save();
                            return response()->json([
                                'status' => 200,
                                'message' => 'User Found!',
                            ]);
                        } else {
                            return response()->json([
                                'status' => 404,
                                'message' => 'User Not Found!',
                            ]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            abort_if($e, 500);
        }
    }
}
