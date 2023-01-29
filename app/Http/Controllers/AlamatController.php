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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Alamat  $alamat
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Alamat  $alamat
     * @return \Illuminate\Http\Response
     */
    public function edit(Alamat $alamat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alamat  $alamat
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alamat  $alamat
     * @return \Illuminate\Http\Response
     */
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
