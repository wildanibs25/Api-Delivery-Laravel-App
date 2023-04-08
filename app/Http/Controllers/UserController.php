<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\JWTRS256\GetToken;
use App\JWTRS256\VerifyToken;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function index(User $user)
    {

        try{
            $user = $user->all();

            foreach($user as $u){
                $u->pesanan = $u->pesanan;
            }

            return response()->json([
                'data' => $user,
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:60|confirmed',
            'date' => 'required',
            'gender' => 'required',
            'number_phone' => 'required|string',
            'accept' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $user = User::create([
                'nama' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tgl_lahir' => $request->date,
                'jk' => $request->gender,
                'no_hp' => $request->number_phone,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],400);

        }

    }

    public function authenticate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:60'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $token = GetToken::Auth($request);

            if(!file_exists("../app/jwtrs256/logout/ListLogout.json")){
                file_put_contents('../app/jwtrs256/logout/ListLogout.json', []);
            }

            return response()->json([
                'Success' => true,
                'token'=> $token,
            ],200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],401);

        }

    }

    public function addToList(Request $request)
    {

        $list = array();
        $token = $request->bearerToken();
        $date = strval(date("F d, Y H:i:s"));

        if($my_file = file_get_contents('../app/jwtrs256/logout/ListLogout.json')){
            $file_decode = json_decode($my_file);

            foreach($file_decode as $value){
                $list[] = $value;
            }

            if(count((array)$list) >= 100){
                $list = [];
            }

            array_push($list, ["date" => $date, "token" => $token]);

        }else{

            array_push($list, ["date" => $date, "token" => $token]);

        }

        $list_file = file_put_contents('../app/jwtrs256/logout/ListLogout.json', json_encode($list, JSON_PRETTY_PRINT));

        if ($list_file){
            return response()->json([
               'success' => true,
            ], 200);

        }else{
            return response()->json([
                'error' => 'Something went wrong'
            ], 400);
        }

    }

    public function me(User $user)
    {

        try{

            $id = VerifyToken::AuthCheck()->sub;

            $user = $user->find($id);

            return response()->json([
                'Success' => true,
                'user' => $user,
            ],200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],400);

        }

    }

    public function detailAdmin(User $user)
    {

        try{

            if(!$user){

                return response()->json([
                    'success' => false,
                    'error' => 'Sorry, Data user not found.'
                ], 404);

            }

            return response()->json([
                'Success' => true,
                'User' => $user,
            ],200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],400);

        }

    }

    public function update(Request $request, User $user)
    {

        $id = VerifyToken::AuthCheck()->sub;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'old_password' => 'string|min:6|max:50',
            'new_password' => 'string|min:6|max:50|confirmed',
            'date' => 'required',
            'gender' => 'required',
            'number_phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $dataUser = [
                'nama' => $request->name,
                'email' => $request->email,
                'tgl_lahir' => $request->date,
                'jk' => $request->gender,
                'no_hp' => $request->number_phone,
            ];

            if(!empty($request->new_password)){

                if(!Hash::check($request->old_password, $user->find($id)->password)){

                    return response()->json([
                        'success' => false,
                        'error' => 'Old Password is Invalid',
                    ], 404);

                }else{

                    $dataUser += [
                        'password' => Hash::make($request->new_password),
                    ];

                }
            }

            if($request->hasFile('photo')){

                $exploded = explode('/', $user->find($id)->foto);
                $lastPathName = end($exploded);

                if(File::exists('storage/fotos/'.$lastPathName)){
                    unlink('storage/fotos/'.$lastPathName);
                }

                $file = $request->file('photo');
                $fileName = $file->getClientOriginalName();

                if(strlen($fileName) > 40){
                    $fileName = substr($fileName, 0, 30) . "..." . substr($fileName, -10);
                }

                $finalName = date("YmdHis") .'-'. $fileName;

                $request->file('photo')->storeAs('fotos/',$finalName, 'public');

                $dataUser += [ 'foto' => Storage::url('fotos/'.$finalName), ];

            }

            $user = $user->find($id)->update($dataUser);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

    public function updateAdmin(Request $request, User $user)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'string|min:6|max:50',
            'date' => 'required',
            'gender' => 'required',
            'number_phone' => 'required|string',
            'admin' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 422);
        }

        try{

            $dataUser = [
                'nama' => $request->name,
                'email' => $request->email,
                'tgl_lahir' => $request->date,
                'jk' => $request->gender,
                'no_hp' => $request->number_phone,
                'is_admin' => $request->admin,
            ];

            if($request->password){
                $dataUser += ['password' => Hash::make($request->password)];
            }

            if($request->hasFile('photo')){

                $exploded = explode('/', $user->foto);
                $lastPathName = end($exploded);

                if(File::exists('storage/fotos/'.$lastPathName)){
                    unlink('storage/fotos/'.$lastPathName);
                }

                $file = $request->file('photo');
                $fileName = $file->getClientOriginalName();
                $finalName = date("YmdHis") .'-'. $fileName;

                $request->file('photo')->storeAs('fotos/',$finalName, 'public');

                $dataUser += [ 'foto' => Storage::url('fotos/'.$finalName), ];
            }

            $user = $user->update($dataUser);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }


    }

    public function destroy(User $user)
    {

        try{

            $payload = VerifyToken::AuthCheck();

            if($payload->admin === 1){
                return response()->json([
                    'success' => false,
                    'message' => 'Your is Admin'
                ], 400);
            }

            $exploded = explode('/', $user->find($payload->sub)->foto);
            $lastPathName = end($exploded);

            if(File::exists('storage/fotos/'.$lastPathName)){
                unlink('storage/fotos/'.$lastPathName);
            }

            $user->find($payload->sub)->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

    public function destroyAdmin(User $user)
    {
        try{

            if(VerifyToken::AuthCheck()->sub === $user->id_user){
                return response()->json([
                    'success' => false,
                    'message' => 'This is you, Your is Admin'
                ], 400);
            }

            if($user->id_user === 1){
                return response()->json([
                    'success' => false,
                    'message' => "You cannot delete this Account"
                ], 400);
            }


            $exploded = explode('/', $user->foto);
            $lastPathName = end($exploded);

            if(File::exists('storage/fotos/'.$lastPathName)){
                unlink('storage/fotos/'.$lastPathName);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ], 200);

        }catch(Exception $e){

            return response()->json([
                'error' => $e->getMessage()
            ],404);

        }

    }

}
