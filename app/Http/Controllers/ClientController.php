<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!auth()->user() || !auth()->user()->can('view-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $clients = Client::with('reservations')
            ->latest()
            ->get();

        return response()->json($clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return response()->json($client, 201);
    }

    public function show(Client $client): JsonResponse
    {
        if (!auth()->user() || !auth()->user()->can('view-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $client->load('reservations');

        return response()->json($client);
    }

    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $client->update($request->validated());

        return response()->json($client);
    }

    public function destroy(Client $client): JsonResponse
    {
        if (!auth()->user() || !auth()->user()->can('delete-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $client->delete();

        return response()->json(['message' => 'Client deleted successfully']);
    }

    // OCR Integration Method - This is where you'll integrate your OCR processing
    public function processNationalId(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
        ]);

        // TODO: Implement your OCR processing here
        // This method should:
        // 1. Store the uploaded image
        // 2. Call your OCR service/job to extract data from the Egyptian national ID
        // 3. Return the extracted data in the response

        // Example structure of what you should return:
        return response()->json([
            'national_id' => '29001011234567',
            'full_arabic_name' => 'أحمد محمد علي',
            'marital_status' => 'married',
            'job' => 'مهندس',
            'expiry_date' => '2030-12-31',
            'birthdate' => '1990-05-15',
        ]);
    }
}
