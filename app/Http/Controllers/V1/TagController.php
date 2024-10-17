<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return new TagCollection($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:tags,name'],
            [
                'name.unique' => 'This Tag Aleardy Exists',
            ]
        ]);
        $tag = new Tag();
        $tag->name = $request->name;
        $tag->save();
        return response(['status', 'successfully', 'message' => 'Tag Created Successfully', 'tag' => $tag], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'unique:tags,name'],
            [
                'name.unique' => 'This Tag Aleardy Exists',
            ]
        ]);
        $tag = Tag::findOrFail($id);
        $tag->name = $request->name;
        $tag->save();
        return response(['status', 'successfully', 'message' => 'Tag Updated Successfully', 'tag' => [$tag->id,$tag->name,]], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      $tag = Tag::findOrFail($id);
      $tag->delete();
      return response(['status', 'successfully', 'message' => 'Tag Deleted Successfully'], 200);
    }
}
