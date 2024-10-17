<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = auth()->user()->posts()->with('tags')->orderByDesc('pinned')->get();
        return new PostCollection($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image',
            'pinned' => 'required|boolean',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $imagePath = $request->file('cover_image')->store('covers', 'public');

        $post = auth()->user()->posts()->create([
            'title' => $request->title,
            'body' => $request->body,
            'cover_image' => $imagePath,
            'pinned' => $request->pinned,
        ]);

        $post->tags()->attach($request->tags);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        if (auth()->user()->id == $post->user_id) {
            return new PostResource($post);
        } else {
            return response()->json(['status' => 'error', 'message' => 'This Post Does not Belong To You'], 401);
        }
    }


    public function update(Request $request, Post $post)
    {
        if (auth()->user()->id == $post->user_id) {
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'body' => 'sometimes|required|string',
                'cover_image' => 'sometimes|image',
                'pinned' => 'required|boolean',
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id',
            ]);

            if ($request->hasFile('cover_image')) {
                $imagePath = $request->file('cover_image')->store('covers', 'public');
                $post->cover_image = $imagePath;
            }

            $post->update($request->only('title', 'body', 'pinned'));

            $post->tags()->sync($request->tags);

            return response()->json($post);
        } else {
            return response()->json(['status' => 'error', 'message' => 'This Post Does not Belong To You'], 401);
        }
    }


    public function destroy(Post $post)
    {
        if (auth()->user()->id == $post->user_id) {
            $post->delete();
            return response()->json(['status' => 'success', 'message' => 'Post Has Been Deleted Successfully'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'This Post Does not Belong To You'], 401);
        }
    }

    public function deleted()
    {
        // $user = Auth::user();
        // $posts = Post::where('user_id',$user->id)->onlyTrashed()->get();
        $posts = auth()->user()->posts()->onlyTrashed()->get();
        return response()->json($posts);
    }

    public function restore($id)
    {
        $post = auth()->user()->posts()->onlyTrashed()->findOrFail($id);
        $post->restore();
        return response()->json($post);
    }
}
