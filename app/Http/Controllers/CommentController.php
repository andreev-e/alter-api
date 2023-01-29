<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $id = (integer) $request->input('id');
        $type = $request->input('type');

        $comments = Comment::where('approved', 1)->orderBy('time', 'DESC');
        if ($id && $type === 'poi') {
            $comments->where('backlink', $id);
        }

        return CommentResource::collection($comments->paginate());
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, Comment $comment)
    {
        //
    }

    public function destroy(Comment $comment)
    {
        //
    }
}
