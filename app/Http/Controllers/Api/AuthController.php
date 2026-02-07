<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends AppBaseController
{
    public function register(StaffRequest $request)
    {
        DB::beginTransaction();
        try {
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
            $data->status = 'Active';
            $data->save();

            // Generate API token
            $token = $data->createToken('flutter')->plainTextToken;

            DB::commit();

            return $this->sendResponse([
                'success' => true,
                'token' => $token,
                'user' => $data,
            ], 'User created successfully.');

        } catch (Exception $e) {
            DB::rollBack();

            // Log the error
            Log::error('Error in updating Register: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!!!',
            ], 500);
        }
    }
}
