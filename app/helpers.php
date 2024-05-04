<?php

use App\Models\User;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

function getUser($param){
    $user = User::where('id', $param)
                ->orWhere('email', $param)
                ->orWhere('username', $param)
                ->first();

    $user->pin = $user->pin;
    $user->username = $user->username;
    $user->profile_picture = $user->profile_picture ? url('storage/'.$user->profile_picture) : "";

    return $user;
};

function pinChecker($pin){
    $userId = auth()->user()->id;

    $user = User::where('id', $userId)->first();

    if($user->pin == $pin){
        return true;
    }

    return false;
}

function uploadBase64Image($base64Image){
    $decoder = new Base64ImageDecoder($base64Image, $allowedFormats = ['jpeg', 'png', 'jpg']);

    $decodedContent = $decoder->getDecodedContent();
    $format = $decoder->getFormat();
    $image = Str::random(10).'.'.$format;

    Storage::disk('public')->put($image, $decodedContent);

    return $image;
}