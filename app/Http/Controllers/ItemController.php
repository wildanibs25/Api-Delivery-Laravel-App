<?php

namespace App\Http\Controllers;

use App\JWTRS256\VerifyToken;
use App\Models\Item;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try{

            $where = ['id_user_item' => VerifyToken::AuthCheck()->sub, 'nota_item' => 'Belum Ada'];

            // $data = Item::where($where)->join("menu", "menu.id_menu", "item.id_menu_item")->get();
            $data = Item::where($where)->with('menu')->get();

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

        $data = $request->all();
        $validator = Validator::make($data, [
            'id_menu_item' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $id_user = VerifyToken::AuthCheck()->sub;
            $id_menu = $request->id_menu_item;

            $where = ['id_user_item' => $id_user, 'id_menu_item' => $id_menu, 'nota_item' => 'Belum Ada'];

            $cek = item::where($where)->first();

            if($cek){

                $item = Item::find($cek->id_item)->update(['qty' => $cek->qty + 1,]);

            }else{
                $item = Item::create([
                    'id_menu_item' => $id_menu,
                    'id_user_item' => $id_user,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item
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
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'id_item' => 'required',
            'qty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $item = $item->find($request->id_item)->update(['qty' => $request->qty,]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                // 'data' => ItemController::index()
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
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Item $item)
    {

        $data = $request->all();
        $validator = Validator::make($data, [
            'id_item' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $item->find($request->id_item)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

}
