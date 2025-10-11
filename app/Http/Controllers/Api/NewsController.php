<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $items = News::orderBy('created_at','desc')->get();
        // make image full URL
        $items->transform(function($n){
            $n->image = $n->image ? asset('storage/'.$n->image) : null;
            return $n;
        });
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'date'  => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120' // max 5MB
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('news', 'public'); // saved to storage/app/public/news
            $data['image'] = $path;
        }

        $news = News::create($data);
        $news->image = $news->image ? asset('storage/'.$news->image) : null;
        return response()->json($news, 201);
    }

    public function show($id)
    {
        $n = News::findOrFail($id);
        $n->image = $n->image ? asset('storage/'.$n->image) : null;
        return response()->json($n);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string',
            'date'  => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120'
        ]);

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            $path = $request->file('image')->store('news','public');
            $data['image'] = $path;
        }

        $news->update($data);
        $news->image = $news->image ? asset('storage/'.$news->image) : null;
        return response()->json($news);
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();
        return response()->json(null, 204);
    }
}
