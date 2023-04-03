<?php

namespace App\Http\Controllers;

use App\Events\PemesananEvent;
use App\JWTRS256\VerifyToken;
use App\Models\Item;
use App\Models\Pesanan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PesananController extends Controller
{
    public function index()
    {
        try {

            $id = VerifyToken::AuthCheck()->sub;

            $data = Pesanan::where('id_user_pesanan', $id)->with('user', 'alamat')->orderBy('created_at', 'desc')->get();

            foreach ($data as $dt) {
                $dt->item = Item::where('nota_item', $dt->nota)->with('menu')->get();
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

    public function indexAdmin(Pesanan $pesanan)
    {

        try {

            $data = $pesanan->all();

            foreach ($data as $dt) {
                $dt->user = $dt->user;
                $dt->alamat = $dt->alamat;
                $dt->item = Item::where('nota_item', $dt->nota)->with('menu')->get();
            }

            $static = [
                'today' => $pesanan->whereRaw('DAY(updated_at) = ' . date('d'))->where('status_pesanan', 'Finished')->sum('total_harga'),
                'yesterday' => $pesanan->whereRaw('DAY(updated_at) = ' . date('d') - 1)->where('status_pesanan', 'Finished')->sum('total_harga'),
                'month' => $pesanan->whereRaw('MONTH(updated_at) = ' . date('m'))->where('status_pesanan', 'Finished')->sum('total_harga'),
                'lastMonth' => $pesanan->whereRaw('MONTH(updated_at) = ' . date('m') - 1)->where('status_pesanan', 'Finished')->sum('total_harga'),
                'year' => $pesanan->whereRaw('YEAR(updated_at) = ' . date('Y'))->where('status_pesanan', 'Finished')->sum('total_harga'),
                'lastYear' => $pesanan->whereRaw('YEAR(updated_at) = ' . date('Y') - 1)->where('status_pesanan', 'Finished')->sum('total_harga'),
            ];

            return response()->json([
                'data' => $data,
                'static' => $static,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request)
    {

        $id = VerifyToken::AuthCheck()->sub;

        $validator = Validator::make($request->all(), [
            'id_alamat_pesanan' => 'required',
            'total_harga' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        if ($request->total_harga === 0) {
            return response()->json(['error' => 'Sorry, an error occurred!'], 421);
        }

        try {

            $finalNota = 'INV-' . date('Y.m.d') . '-01-' . date('His');

            $where = ['id_user_item' => $id, 'nota_item' => 'Belum Ada'];

            $pesanan = Pesanan::create([
                'nota' => $finalNota,
                'id_user_pesanan' => $id,
                'id_alamat_pesanan' => $request->id_alamat_pesanan,
                'total_harga' => $request->total_harga,
            ]);

            if ($pesanan) {

                Item::where($where)->update(['nota_item' => $finalNota]);

                PemesananEvent::dispatch(['success' => true], 200);

                return response()->json([
                    'success' => true,
                    'message' => 'Order added successfully',
                    'data' => $pesanan,
                ], 200);
            }
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Pesanan $pesanan)
    {

        try {

            if (!$pesanan) {

                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, Data order not found.'
                ], 404);
            } else {

                $pesanan->user = $pesanan->user;
                $pesanan->item = Item::where('nota_item', $pesanan->nota)->with('menu')->get();
                $pesanan->alamat = $pesanan->alamat;

                return response()->json([
                    'data' => $pesanan,
                ], 200);
            }
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, Pesanan $pesanan)
    {
        $validator = Validator::make($request->all(), [
            'status_pesanan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        if ($pesanan->id_user_pesanan !== VerifyToken::AuthCheck()->sub) {
            return response()->json(['message' => "You didn't Order"], 400);
        }

        try {

            $pesanan = $pesanan->update(['status_pesanan' => $request->status_pesanan]);

            if ($pesanan) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order updated successfully',
                ], 200);
            }
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
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

        try {

            $pesanan = $pesanan->update(['status_pesanan' => $request->status_pesanan]);

            if ($pesanan) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order updated successfully',
                ], 200);
            }
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function countOrder()
    {
        try {

            $pesanan = Pesanan::where('status_pesanan', 'Order')->get();

            return response()->json([
                'data' => $pesanan,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy(Pesanan $pesanan)
    {
        //
    }
}
