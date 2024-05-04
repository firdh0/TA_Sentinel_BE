<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email',
            'username' => 'required|string',
            'password' => 'required|string|min:6',
            'pin' => 'required|digits:6',
            'phone_number' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->messages()], 400);
        }

        if (User::where('email', $request->email)->exists() || User::where('username', $request->username)->exists()) {
            return response()->json(['message' => 'Email atau Username telah digunakan'], 409);
        }

        try {
            $profilePicture = null;

            if($request->profile_picture){
                $profilePicture = $this->uploadBase64Image($request->profile_picture);
            }

            $phoneNumber = $request->phone_number;

            // $phoneNumber = '1234567890';

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'pin' => $request->pin,
                'phone_number' => $phoneNumber,
                'verified' => ($phoneNumber) ? true : false,
                'profile_picture' => $profilePicture,
            ]);
            
            $token = JWTAuth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ]);

            $userResponse = getUser($request->email);
            $userResponse->token = $token;
            $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            $userResponse->token_type = 'bearer';

            return response()->json($userResponse);
        } catch (\Throwable $th) {
            // echo $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->message()], 400);
        }

        try {
            $token = JWTAuth::attempt($credentials);

            if(!$token){
                return response()->json(['message' => 'Kredensial login tidak valid']);
            }

            $userResponse = getUser($request->email);
            $userResponse->token = $token;
            $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            $userResponse->token_type = 'bearer';

            return response()->json($userResponse);
        } catch (\JWTException $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    private function uploadBase64Image($base64Image){
        $decoder = new Base64ImageDecoder($base64Image, $allowedFormats = ['jpeg', 'png', 'jpg']);

        $decodedContent = $decoder->getDecodedContent();
        $format = $decoder->getFormat();
        $image = Str::random(10).'.'.$format;

        Storage::disk('public')->put($image, $decodedContent);

        return $image;
    }
    
    public function logout(){
        auth()->logout();

        return response()->json(['message' => 'Keluar sukses']);
    }
}
