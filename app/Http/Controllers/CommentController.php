<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Exception;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Comment::with('post')->get();
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
        //Create new comment
        try {
            $request->validate([
                'content' => 'required|string',
                'post_id' => 'required'
            ]);

            $comment = Comment::create([
                'content' => $request->content,
                'post_id' => $request->post_id,
                'user_id' => $request->user()->id
            ]); 

            return response()->json([
                'message' => 'Comment created successfully',
                'comment' => $comment
            ], 201);    


        } catch(Exception $error) {
            return response()->json([
                'message' => 'Error: '.$error->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $comment = Comment::findOrFail($id);

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update($request->all());

        return response()->json([
            'message' => 'Comment Updated Succesfully!',
            'data' => $comment
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Comment Deleted Succesfully!'
        ], 200);
    }
}
