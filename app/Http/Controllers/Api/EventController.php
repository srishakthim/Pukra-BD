<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        return response()->json(Event::orderBy('created_at','desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'date'  => 'nullable|string',
            'month' => 'nullable|string',
            'type'  => 'nullable|string',
        ]);
        $event = Event::create($data);
        return response()->json($event, 201);
    }

    public function show($id)
    {
        return response()->json(Event::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'date'  => 'nullable|string',
            'month' => 'nullable|string',
            'type'  => 'nullable|string',
        ]);
        $event = Event::findOrFail($id);
        $event->update($data);
        return response()->json($event);
    }

    public function destroy($id)
    {
        Event::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
