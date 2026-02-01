<?php

namespace App\Http\Controllers;

use App\Http\Requests\DurationRequest;
use App\Models\Duration;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth_check');
    }
    public function index(Request $request)
    {
        try
        {
            if($request->ajax()) {

                $tasks = Duration::select('*')->latest();

                return Datatables::of($tasks)
                    ->addIndexColumn()

                    ->addColumn('time_duration', function($row){
                        return $row->time_duration ?? '';
                    })

                    ->addColumn('time_unit', function($row){
                        return $row->time_unit ?? '';
                    })

                    ->addColumn('status', function($row){
                        return $row->status ?? '';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('durations.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-product" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';

                        $btn .= ' <a href="#" class="btn btn-danger btn-sm delete-data action-button" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a>';

                        return $btn;
                    })
                    // search customization
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && $request->search['value'] != '') {
                            $searchValue = $request->search['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('time_duration', 'like', "%{$searchValue}%")
                                    ->orWhere('time_unit', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->rawColumns(['time_duration', 'time_unit', 'status', 'action'])
                    ->make(true);
            }

            return view('admin.durations.index');
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
        return view('admin.durations.create');
    }
    public function store(DurationRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $duration = new Duration();
            $duration->time_duration = $request->time_duration;
            $duration->time_unit = $request->time_unit;
            $duration->status = $request->status;
            $duration->save();

            DB::commit();

            $notification=array(
                'message' => 'Successfully a data has been added',
                'alert-type' => 'success',
            );

            return redirect()->route('durations.index')->with($notification);

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
    public function show(Duration $duration)
    {
        return view('admin.durations.edit', compact('duration'));
    }
    public function edit(Duration $duration)
    {
        //
    }
    public function update(DurationRequest $request, Duration $duration)
    {
        try
        {
            $duration->time_duration = $request->time_duration;
            $duration->time_unit = $request->time_unit;
            $duration->status = $request->status;
            $duration->save();

            $notification=array(
                'message' => 'Successfully the data has been updated',
                'alert-type' => 'success',
            );

            return redirect()->route('durations.index')->with($notification);

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
    public function destroy(Duration $duration)
    {
        try
        {
            $duration->delete();
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
}
