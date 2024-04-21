<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller
{
    public function show(){
        $user = getUser(auth()->user()->id);

        return response()->json($user);
    }

    public function getUserByUsername(Request $request, $username){
        $user = User::select(
            'id', 'name', 'username', 'phone_number',
            'verified', 'profile_picture')
            ->where('username', 'LIKE', '%'.$username.'%')
            ->where('id', '<>', auth()->user()->id)
            ->get();

        $user->map(function($item){
            $item->profile_picture = $item->profile_picture ? 
                url('storage/'.$item->profile_picture) : '';

            return $item;
        });

        return response()->json($user);
    }

    public function update(Request $request){
        try {
            $user = User::find(auth()->user()->id);

            $data = $request->only('name', 'email', 'username', 'password', 'phone_number', 'profile_picture');

            if($request->username != $user->username){
                $isExistUsername = User::where('username', $request->username)->exists();
                if($isExistUsername){
                    return response(['message' => 'Username telah digunakan'], 409);
                }
            }

            if($request->email!= $user->email){
                $isExistEmail = User::where('email', $request->email)->exists();
                if($isExistEmail){
                    return response(['message' => 'Email telah digunakan'], 409);
                }
            }

            if($request->password){
                $data['password'] = bycrypt($request->password);
            }

            if($request->profile_picture){
                $profilePicture = uploadBase64Image($request->profile_picture);
                $data['profile_picture'] = $profilePicture;
                if($user->profile_picture){
                    Storage::delete('public/'.$user->profile_picture);
                }
            }

            $user->update($data);

            return response()->json(['message' => 'User telah terupdate']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function isEmailExist(Request $request){
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $isExist = User::where('email', $request->email)->exists();

        return response()->json(['Email sudah ada' => $isExist]);
    }
}

