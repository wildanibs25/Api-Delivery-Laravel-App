<?php

namespace App\Http\Controllers;

use App\JWTRS256\VerifyToken;
use App\Models\Item;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index(Item $item)
    {

        try {

            $where = [
                'id_user_item' => VerifyToken::AuthCheck()->sub,
                'nota_item' => 'Belum Ada'
            ];

            $data = $item->where($where)->with('menu')->get();

            return response()->json([
                'data' => $data,
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function indexAdmin(Item $item)
    {
        try {

            $data = $item
            ->select('id_menu_item', DB::raw('sum(qty) as qty'))
            ->whereNotIn('nota_item', ['Belum Ada'])
            ->groupBy('id_menu_item')
            ->get();

            foreach($data as $d){
                $d->menu = $d->menu;
            }

            return response()->json([
                'data' => $data,
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_menu_item' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {

            $id_user = VerifyToken::AuthCheck()->sub;
            $id_menu = $request->id_menu_item;

            $where = ['id_user_item' => $id_user, 'id_menu_item' => $id_menu, 'nota_item' => 'Belum Ada'];

            $cek = item::where($where)->first();

            if ($cek) {

                $item = Item::find($cek->id_item)->update(['qty' => $cek->qty + 1,]);
            } else {
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

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, Item $item)
    {

        $validator = Validator::make($request->all(), [
            'id_item' => 'required',
            'qty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {

            $item = $item->find($request->id_item)->update(['qty' => $request->qty,]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                // 'data' => ItemController::index()
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy(Request $request, Item $item)
    {

        $validator = Validator::make($request->all(), [
            'id_item' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {

            $item->find($request->id_item)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ], 200);

        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
