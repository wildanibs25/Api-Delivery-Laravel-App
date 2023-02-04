<?php

namespace App\Http\Controllers;

use App\JWTRS256\VerifyToken;
use App\Models\Item;
use App\Models\Pesanan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PesananController extends Controller
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

            $data = Pesanan::where('id_user_pesanan', $id)->with('user','alamat')->get();

            foreach( $data as $dt){
                $dt->item = Item::where('nota_item',$dt->nota)->with('menu')->get();
            }

            return response()->json([
                'data' => $data,
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

    public function indexAdmin(Pesanan $pesanan)
    {

        try{

            $data = $pesanan->all();

            foreach( $data as $dt){
                $dt->user = $dt->user;
                $dt->alamat = $dt->alamat;
                $dt->item = Item::where('nota_item',$dt->nota)->with('menu')->get();
            }

            return response()->json([
                'data' => $data,
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
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
            'id_alamat_pesanan'=> 'required',
            'total_harga'=> 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        if($request->total_harga === 0){
            return response()->json(['error' => 'Sorry, an error occurred!'], 421);
        }

        try{

            $finalNota = 'INV-'.date('Y.m.d').'-01-'.date('His');

            $where = ['id_user_item' => $id, 'nota_item' => 'Belum Ada'];

            $pesanan = Pesanan::create([
                'nota' => $finalNota,
                'id_user_pesanan' => $id,
                'id_alamat_pesanan' => $request->id_alamat_pesanan,
                'total_harga' => $request->total_harga,
            ]);

            if($pesanan){

                Item::where($where)->update(['nota_item' => $finalNota]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order added successfully',
                    'data' => $pesanan,
                ], 200);

            }

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],400);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function show(Pesanan $pesanan)
    {

        try{

            if (!$pesanan) {

                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, Data order not found.'
                ], 404);

            }else{

                $pesanan->user = $pesanan->user;
                $pesanan->item = Item::where('nota_item',$pesanan->nota)->with('menu')->get();
                $pesanan->alamat = $pesanan->alamat;

                return response()->json([
                    'data' => $pesanan,
                ], 200);

            }

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],400);

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function edit(Pesanan $pesanan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Pesanan $pesanan)
    {
        $validator = Validator::make($request->all(), [
            'status_pesanan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        if($pesanan->id_user_pesanan !== VerifyToken::AuthCheck()->sub){
             return response()->json(['message' => "You didn't Order"], 400);
        }

        try{

            $pesanan = $pesanan->update(['status_pesanan' => $request->status_pesanan]);

            if($pesanan){
                return response()->json([
                    'success' => true,
                    'message' => 'Order updated successfully',
                ], 200);
            }

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }
    }

    public function updateAdmin(Request $request, Pesanan $pesanan)
    {

        $validator = Validator::make($request->all(), [
            'status_pesanan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $pesanan = $pesanan->update(['status_pesanan' => $request->status_pesanan]);

            if($pesanan){
                return response()->json([
                    'success' => true,
                    'message' => 'Order updated successfully',
                ], 200);
            }

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pesanan $pesanan)
    {
        //
    }
}
