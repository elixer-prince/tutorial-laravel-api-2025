<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $posts = $user->posts()->get();
        
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['author_id'] = $request->user()->id;

        $post = Post::create($validatedData);

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if (Auth::user()->id !== $post->author_id) {
            abort(403, 'Access Forbidden!');
        }

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequest $request, Post $post)
    {
        if (Auth::user()->id !== $post->author_id) {
            abort(403, 'Access Forbidden!');
        }

        $validatedData = $request->validated();
        $validatedData['author_id'] = $request->user()->id;
        
        $post->update($validatedData);
        
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::user()->id !== $post->author_id) {
            abort(403, 'Access Forbidden!');
        }

        $post->delete();

        return response()->noContent();
    }
}
