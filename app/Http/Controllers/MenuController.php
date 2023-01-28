<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{

            $data = Menu::all();

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
            'name' => 'required|string',
            'price' => 'required',
            'category' => 'required',
            'description' => 'required|string',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $file = request()->file('image');
            $fileName = $file->getClientOriginalName();
            $finalName = date("YmdHis") .'-'. $fileName;

            request()->file('image')->storeAs('menus/',$finalName, 'public');

            $menu = Menu::create([
                'nama_menu' => $request->name,
                'harga_menu' => $request->price,
                'kategori_menu' => $request->category,
                'deskripsi_menu' => $request->description,
                'gambar_menu' => 'http://192.168.43.222:8000'.Storage::url('menus/'.$finalName),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu added successfully',
                'data' => $menu
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
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {

        try{

            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sorry, Data menu not found.'
                ], 404);
            }

            return response()->json([
                'data' => $menu,
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],400);

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Menu $menu, $id)
    {

        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'price' => 'required',
            'category' => 'required',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $dataMenu = [
                'nama_menu' => request()->name,
                'harga_menu' => request()->price,
                'kategori_menu' => request()->category,
                'deskripsi_menu' => request()->description,
            ];

            if(request()->hasFile('image')){

                if(File::exists('storage/menus/'.$menu->find($id)->gambar_menu)){
                    unlink('storage/menus/'.$menu->find($id)->gambar_menu);
                }

                $file = request()->file('image');
                $fileName = $file->getClientOriginalName();
                $finalName = date("YmdHis") .'-'. $fileName;

                request()->file('image')->storeAs('menus/',$finalName, 'public');

                $dataMenu += [ 'gambar_menu' => 'http://192.168.43.222:8000'.Storage::url('menus/'.$finalName), ];
            }

            if(request()->status_menu){

                $dataMenu += [ 'status_menu' => request()->status_menu ];

            }

            $menu = $menu->find($id)->update($dataMenu);

            if($menu){

                 return response()->json([
                    'success' => true,
                    'message' => 'Menu updated successfully',
                    'data' => $menu
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
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu, $id)
    {

        try{

            $menu->find($id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Menu deleted successfully'
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

}
