<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
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

                $tasks = Branch::select('*')->latest();

                return Datatables::of($tasks)
                    ->addIndexColumn()

                    ->addColumn('name', function($row){
                        return $row->name ?? '';
                    })

                    ->addColumn('address', function($row){
                        return $row->address ?? '';
                    })

                    ->addColumn('email', function($row){
                        return $row->email ?? '';
                    })

                    ->addColumn('phone', function($row){
                        return $row->phone ?? '';
                    })

                    ->addColumn('latitude', function($row){
                        return $row->latitude ?? '';
                    })

                    ->addColumn('longitude', function($row){
                        return $row->longitude ?? '';
                    })

                    ->addColumn('status', function($row){
                        return '<label class="switch"><input class="' . ($row->status === 'Active' ? 'active-data' : 'decline-data') . '" id="status-update"  type="checkbox" ' . ($row->status === 'Active' ? 'checked' : '') . ' data-id="'.$row->id.'"><span class="slider round"></span></label>';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('services.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-product" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';


                        $btn .= ' <a href="#" class="btn btn-danger btn-sm delete-data action-button" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a>';

                        return $btn;
                    })
                    // search customization
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $searchValue = $request->search['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('name', 'like', "%{$searchValue}%")
                                    ->orWhere('status', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['name','address','email','phone','latitude','longitude', 'status', 'action'])
                    ->make(true);
            }

            return view('admin.branches.index');
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
        return view('admin.services.create');
    }
    public function store(ServiceRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $task = new Branch();
            $task->name = $request->name;
            $task->status = $request->status;
            $task->save();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );
            DB::commit();

            return redirect()->route('services.index')->with($notification);

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
    public function show(Branch $branch)
    {
        return view('admin.services.edit', compact('service'));
    }
    public function edit(Branch $branch)
    {
        //
    }
    public function update(ServiceRequest $request, Branch $branch)
    {
        try
        {
            $service->name = $request->name;
            $service->status = $request->status;
            $service->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('services.index')->with($notification);

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
    public function destroy(Branch $branch)
    {
        try
        {
            $service->delete();
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
    public function branchStatusUpdate()
    {
        DB::beginTransaction();
        try
        {
            $user = Branch::findorfail($request->user_id);
            $user->status = $request->status;
            $user->update();

            $existingAssignedTask = AssignedTrialTask::where('user_id', $user->id)->first();
            if (!$existingAssignedTask) {
                $trialTaskInfo = TrialTask::first();

                $assignLevel = new AssignedTrialTask();
                $assignLevel->user_id = $user->id;
                $assignLevel->trial_task_id = $request->trial_task_id;
                $assignLevel->num_of_tasks = ($trialTaskInfo && $trialTaskInfo->num_of_task) ? $trialTaskInfo->num_of_task : 0;
                $assignLevel->status = 'pending';
                $assignLevel->save();

                $user->balance = $trialTaskInfo->trial_balance;
                $user->update();
            }

            DB::commit();

            return response()->json([
                'status'=>true,
                'message'=>"User status updated successfully."
            ]);
        } catch(Exception $e) {
            DB::rollBack();
            // Log the error
            Log::error('Error in updating user: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status'=>false,
                'message'=>"Something went wrong!!!"
            ]);
        }
    }
}
