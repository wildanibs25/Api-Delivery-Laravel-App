<?php

namespace App\Http\Controllers;

use App\Events\PemesananEvent;
use App\Models\Menu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        try {

            $data = Menu::all();

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
            'name' => 'required|string',
            'price' => 'required',
            'category' => 'required',
            'description' => 'required|string',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {

            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();

            if (strlen($fileName) > 40) {
                $fileName = substr($fileName, 0, 30) . "..." . substr($fileName, -10);
            }

            $finalName = date("YmdHis") . '-' . $fileName;

            $request->file('image')->storeAs('menus/', $finalName, 'public');

            $menu = Menu::create([
                'nama_menu' => $request->name,
                'harga_menu' => $request->price,
                'kategori_menu' => $request->category,
                'deskripsi_menu' => $request->description,
                'gambar_menu' => Storage::url('menus/' . $finalName),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu added successfully',
                'data' => $menu
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Menu $menu)
    {

        try {

            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sorry, Data menu not found.'
                ], 404);
            }

            return response()->json([
                'data' => $menu,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, Menu $menu)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required',
            'category' => 'required',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try {

            $dataMenu = [
                'nama_menu' => $request->name,
                'harga_menu' => $request->price,
                'kategori_menu' => $request->category,
                'deskripsi_menu' => $request->description,
            ];

            if ($request->hasFile('image')) {
                $exploded = explode('/', $menu->gambar_menu);
                $lastPathName = end($exploded);

                if (File::exists('storage/menus/' . $lastPathName)) {
                    unlink('storage/menus/' . $lastPathName);
                }

                $file = $request->file('image');
                $fileName = $file->getClientOriginalName();

                if (strlen($fileName) > 40) {
                    $fileName = substr($fileName, 0, 30) . "..." . substr($fileName, -10);
                }

                $finalName = date("YmdHis") . '-' . $fileName;

                $request->file('image')->storeAs('menus/', $finalName, 'public');

                $dataMenu += ['gambar_menu' => Storage::url('menus/' . $finalName),];
            }

            if ($request->status_menu) {
                $dataMenu += ['status_menu' => $request->status_menu];
            }

            $menu = $menu->update($dataMenu);

            return response()->json([
                'success' => true,
                'message' => 'Menu updated successfully',
                'data' => $menu
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function destroy(Menu $menu)
    {

        try {

            $exploded = explode('/', $menu->gambar_menu);
            $lastPathName = end($exploded);

            if (File::exists('storage/menus/' . $lastPathName)) {
                unlink('storage/menus/' . $lastPathName);
            }

            $menu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Menu deleted successfully'
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
