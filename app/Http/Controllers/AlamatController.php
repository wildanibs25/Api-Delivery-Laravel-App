<?php

namespace App\Http\Controllers;

use App\JWTRS256\VerifyToken;
use App\Models\Alamat;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlamatController extends Controller
{
    public function index()
    {

        try{

            $id = VerifyToken::AuthCheck()->sub;

            $data = Alamat::where('id_user_alamat', $id)->get();

            return response()->json([
                'data' => $data,
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

    public function indexAdmin(User $user, Alamat $alamat)
    {
        try{

            $data = $alamat->where("id_user_alamat", $user->id_user)->get();

            return response()->json([
                'data' => $data,
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage(),
            ],404);

        }
    }

    public function store(Request $request)
    {

        $id = VerifyToken::AuthCheck()->sub;

        $validator = Validator::make($request->all(), [
            'alamat_lengkap' => 'required',
            'sebagai' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $alamat = Alamat::create([
                'id_user_alamat' => $id,
                'alamat_lengkap' => $request->alamat_lengkap,
                'sebagai' => $request->sebagai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Addres added successfully',
                'data' => $alamat
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],400);

        }

    }

    public function show(Alamat $alamat)
    {

        try{

            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sorry, Data addres not found.'
                ], 404);
            }

            return response()->json([
                'data' => $alamat,
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

    public function update(Request $request, Alamat $alamat)
    {
        $validator = Validator::make($request->all(), [
            'alamat_lengkap',
            'sebagai',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $alamat = $alamat->update([
                'alamat_lengkap' => request()->alamat_lengkap,
                'sebagai' => request()->sebagai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alamat updated successfully',
                'data' => $alamat
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

    public function destroy(Alamat $alamat)
    {

        try{

            $alamat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Addres deleted successfully'
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

}
