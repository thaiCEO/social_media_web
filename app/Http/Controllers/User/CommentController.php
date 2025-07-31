<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created comment or reply.
     */
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'text' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id, // null for main comment, else reply
            'text' => $request->text,
        ]);

        // Optional: return the new comment as JSON for frontend update
         // Redirect back with success message
        return redirect()->back()->with('success', 'Comment created successfully');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(int $id)
    {
        $comment = Comment::findOrFail($id);

        // Only owner can delete
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

          // Redirect back with success message
        return redirect()->back()->with('success', 'Comment deleted successfully');
    }

    /**
     * Like the specified comment.
     */
    public function like(Comment $comment)
    {
        $comment->likes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

         // Redirect back with success message
        return redirect()->back()->with('success', 'Comment liked.');
    }

    /**
     * Unlike the specified comment.
     */
    public function unlike(Comment $comment)
    {
        $comment->likes()->where('user_id', auth()->id())->delete();

           // Redirect back with success message
        return redirect()->back()->with('success', 'Comment unliked.');
    }
}
