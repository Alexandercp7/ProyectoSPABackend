<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::with(['products','tags'])
            ->when($request->search, fn($q) => $q->where('nombre','like',"%{$request->search}%"))
            ->paginate(30);
        return ContactResource::collection($contacts);
    }

    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());
        return response()->json(['data' => new ContactResource($contact)], 201);
    }

    public function show(int $id)
    {
        return response()->json(['data' => new ContactResource(Contact::with(['products','tags','accountsPayable'])->findOrFail($id))]);
    }

    public function update(StoreContactRequest $request, int $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update($request->validated());
        return response()->json(['data' => new ContactResource($contact)]);
    }

    public function destroy(int $id)
    {
        Contact::findOrFail($id)->delete();
        return response()->json(['message' => 'Contacto eliminado.']);
    }
}
