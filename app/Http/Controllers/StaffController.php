<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }
    public function index(Request $request)
    {
        try
        {
            if($request->ajax()){

                $data = User::where('user_type_id', 3)
                    ->orWhere('role', 'service_provider')
                    ->select('*')->latest();

                return Datatables::of($data)
                    ->addIndexColumn()

                    ->addColumn('name', function($row){
                        return $row->name ?? '';
                    })

                    ->addColumn('image', function($row){
                        $url = asset($row->image);
                        return '<img src="' . $url . '" alt="Profile Image" loading="lazy" style="height:60px;">';
                    })

                    ->addColumn('email', function($row){
                        return $row->email ?? '';
                    })

                    ->addColumn('phone', function($row){
                        return $row->phone ?? '';
                    })

                    ->addColumn('status', function($row){
                        return '<label class="switch"><input class="' . ($row->status === 'Active' ? 'active-data' : 'decline-data') . '" id="status-update"  type="checkbox" ' . ($row->status === 'Active' ? 'checked' : '') . ' data-id="'.$row->id.'"><span class="slider round"></span></label>';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('staffs.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-data" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';

                        $btn .= ' <a href="#" class="btn btn-info btn-sm add-service action-button" data-id="'.$row->id.'">Add Service</a>';

                        return $btn;
                    })
                    // search customization
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $searchValue = $request->search['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('name', 'like', "%{$searchValue}%")
                                    ->orWhere('slug', 'like', "%{$searchValue}%")
                                    ->orWhere('status', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['name','image', 'email', 'phone', 'status', 'action'])
                    ->make(true);
            }

            return view('admin.staffs.index');
        } catch(Exception $e) {
            // Log the error
            Log::error('Error in fetching data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!'
            ],500);
        }
    }
    public function create()
    {
        return view('admin.staffs.create');
    }
    public function store(StaffRequest $request)
    {
        DB::beginTransaction();
        try
        {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('image')) {
                $filePath = storeFile($request->file('image'), 'profile_images', 'profileImage_');
            }

            $data = new User();
            $data->name = $request->name;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->user_type_id = 3;
            $data->role = 'service_provider';
            $data->image = $filePath;
            $data->password = $request->password;
            $data->status = $request->status;
            $data->save();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );
            DB::commit();

            return redirect()->route('staffs.index')->with($notification);

        } catch(Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Error in storing data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.staffs.edit', compact('user'));
    }
    public function edit(Staff $staff)
    {
        //
    }
    public function update(StaffRequest $request, $id)
    {
        try
        {
            $user = User::findOrFail($id);
            if (!$user) {
                $notification = array(
                    'message' => 'User not found',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
            // Check if email or phone is being updated to a value that already exists
            $existingEmailUser = User::where('email', $request->email)
                ->where('id', '!=', $id)
                ->first();
            if ($existingEmailUser) {
                $notification = array(
                    'message' => 'The email has already been taken.',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }

            $existingPhoneUser = User::where('phone', $request->phone)
                ->where('id', '!=', $id)
                ->first();
            if ($existingPhoneUser) {
                $notification = array(
                    'message' => 'The phone has already been taken.',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }

            // Handle file upload
            $filePath = $user->image;
            if ($request->hasFile('image')) {
                $filePath = updateFile($request->file('image'), 'profile_images', 'profileImage_', $user->image);
            }

            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            $user->phone = $request->phone ?? $user->phone;
            $user->image = $filePath;
            $user->status = $request->status ?? $user->status;
            $user->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('staffs.index')->with($notification);

        } catch(Exception $e) {
            // Log the error
            Log::error('Error in updating data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
    public function destroy(Staff $staff)
    {
        try
        {
            $speciality->delete();
            return response()->json([
                'status'=>true,
                'message'=>'Successfully the data has been deleted'
            ]);
        } catch(Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Error in deleting data: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!!!'
            ]);
        }
    }
    public function staffStatusUpdate(StaffRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $data = User::findorfail($request->id);
            $data->status = $request->status;
            $data->update();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "User status updated successfully."
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in updating status: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => "Something went wrong!!!"
            ]);
        }
    }
}
