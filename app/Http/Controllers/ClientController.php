<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        if (!auth()->user() || !auth()->user()->can('view-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $clients = Client::with('reservations')
            ->latest()
            ->paginate();

        return ClientResource::collection($clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = Client::create($request->validated());

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Client $client): ClientResource
    {
        if (!auth()->user() || !auth()->user()->can('view-clients')) {
            abort(403, 'Unauthorized action.');
        }

        $client->load('reservations');

        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, Client $client): ClientResource
    {
        $client->update($request->validated());

        return new ClientResource($client);
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
