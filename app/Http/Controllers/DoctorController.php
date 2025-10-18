<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    // Get all doctors
   public function index()
{
    $doctors = Doctor::orderBy('created_at', 'desc')->get();
    $doctors->transform(function ($d) {
        return [
            'id' => $d->id,
            'name' => $d->name,
            'specialist' => $d->specialist,
            'description' => $d->description,
            'image' => $d->image ? asset('storage/' . $d->image) : null,
        ];
    });
    return response()->json($doctors);
}

    // Add new doctor
    public function store(Request $request)
    {
        $data = $request->validate([
    'name' => 'required|string',
    'specialist' => 'nullable|string',
    'description' => 'nullable|string',
    'image' => 'nullable|image|max:5120'
]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('doctors', 'public');
        }

        $doctor = Doctor::create($data);
        $doctor->image = $doctor->image ? asset('storage/' . $doctor->image) : null;

        return response()->json($doctor, 201);
    }

    // Get a single doctor
    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->image = $doctor->image ? asset('storage/' . $doctor->image) : null;
        return response()->json($doctor);
    }

    // Update doctor
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

      $data = $request->validate([
    'name' => 'required|string',
    'specialist' => 'nullable|string',
    'description' => 'nullable|string',
    'image' => 'nullable|image|max:5120'
]);

        if ($request->hasFile('image')) {
            if ($doctor->image && Storage::disk('public')->exists($doctor->image)) {
                Storage::disk('public')->delete($doctor->image);
            }
            $data['image'] = $request->file('image')->store('doctors', 'public');
        }

        $doctor->update($data);
        $doctor->image = $doctor->image ? asset('storage/' . $doctor->image) : null;

        return response()->json($doctor);
    }

    // Delete doctor
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);

        if ($doctor->image && Storage::disk('public')->exists($doctor->image)) {
            Storage::disk('public')->delete($doctor->image);
        }

        $doctor->delete();

        return response()->json(null, 204);
    }
}
