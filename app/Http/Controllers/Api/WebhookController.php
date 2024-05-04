<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller{

    public function getStatus(Request $request)
    {
        // Get the request body (assuming JSON data)
        $data = $request->all();
        // if (empty($data)) {
        //     error_log("Error: No data received in the request body");
        // } else {
        //     error_log("Status message: ", $data);
        // }
        // \Log::info("Status message: " . json_encode($data));
        // dd("Status message: ", $data);

        // Check if 'payload' key exists
        if (array_key_exists('payload', $data)) {
            $payload = $data['payload'];
            // Check if 'from' and 'body' keys exist
            if (array_key_exists('from', $payload) || array_key_exists('body', $payload)) {
                $sender = $payload['from'];
                $name = $payload['_data']['notifyName'];
                $messageBody = $payload['body'];
                $links = $payload['_data']['links'];
                \Log::info("Sender: $sender");
                \Log::info("Name: $name");
                \Log::info("Message Body: $messageBody");
                \Log::info("Links: " . json_encode($links));
            } else {
                \Log::error("Error: 'from' or 'body' key not found in payload");
            }
        } else {
            \Log::error("Error: 'payload' key not found in data");
        }

        // You can return a response if needed
        return response()->json(['message' => 'Data processed successfully']);
    }
}
