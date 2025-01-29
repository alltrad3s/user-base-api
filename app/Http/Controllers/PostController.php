<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Solo obtiene los posts del usuario logueado
        //return Post::all();

        //Obtiene los posts y datos del usuario
        return Post::with('user')->get();
    }

    public function indexOne(string $id)
    {
        //Solo obtiene los posts del usuario logueado
        //return Post::all();

        //Obtiene los posts y datos del usuario
        $post = Post::findOrFail($id);

        return $post;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Proceso creacion de Post
        // Validamos la informacion recibida en el request
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'required|integer'
        ]);

        //Creamos el post
        $post = $request->user()->posts()->create($request->all());

        return response()->json([
            'message' => 'Post Created Succesfully!',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        /*$post = Post::FindOrFail('post_id');
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->save();

        return response()->json([
            'message' => 'Post Updated Succesfully!',
            'data' => $post
        ], 200);*/

        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($request->all());

        return response()->json([
            'message' => 'Post Updated Succesfully!',
            'data' => $post
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post Deleted Succesfully!'
        ], 200);
    }
}
