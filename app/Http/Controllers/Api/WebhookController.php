<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function getStatus(Request $request)
    {
        // Get the request body (assuming JSON data)
        $data = $request->json()->all();
        \Log::info("Status message: " . json_encode($data));

        // Check if 'payload' key exists
        if (isset($data['payload'])) {
            $payload = $data['payload'];
            // Check if 'from' and 'body' keys exist
            if (isset($payload['from']) && isset($payload['body'])) {
                $sender = $payload['from'];
                $messageBody = $payload['body'];
                $links = $payload['_data']['links'];
                \Log::info("Sender: " . $sender);
                \Log::info("Message Body: " . $messageBody);
                \Log::info("Links: " . json_encode($links));
            } else {
                \Log::error("Error: 'from' or 'body' key not found in payload");
            }
        } else {
            \Log::error("Error: 'payload' key not found in data");
        }

        // You can return a response if needed
        return response()->json(["message" => "Data processed successfully"]);
    }
}
