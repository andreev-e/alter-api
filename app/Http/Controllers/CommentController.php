<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\AddCommentRequest;
use App\Http\Requests\Comment\EditCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $id = (integer)$request->input('id');
        $type = $request->input('type');

        $comments = Comment::query()
            ->when(!$request->input('pending'), function($query) {
                $query->where('approved', 1);
            })->orderBy('time', 'DESC');
        if ($id && $type === 'poi') {
            $comments->where('backlink', $id);
        }
        $comments->with(['user', 'object']);

        return CommentResource::collection($comments->paginate());
    }

    public function store(AddCommentRequest $request): JsonResponse
    {
        if (Auth::user()) {
            $comment = new Comment([
                'name' => Auth::user()->username,
                'email' => Auth::user()->email,
                'approved' => 1,
            ]);
        } else {
            $comment = new Comment([
                'name' => '',
                'email' => $request->get('email'),
                'approved' => 0,
            ]);
        }
        $comment->fill([
            'backlink' => $request->get('id'),
            'comment' => $request->get('comment'),
            'time' => Carbon::now()->unix(),
        ])->save();
        return response()->json('Ok');
    }

    public function update(EditCommentRequest $request, Comment $comment)
    {
        if (Auth::user()) {
            if ($comment->name === Auth::user()->username || Auth::user()->username === 'andreev') {
                $comment->update($request->validated());
            }
        }
        return response()->json('Ok');
    }

    public function destroy(Comment $comment)
    {
        if (Auth::user()->username === 'andreev' || Auth::user()->username === $comment->name) {
            $comment->delete();
        }
    }

    public function approve(Comment $comment)
    {
        if (Auth::user()->username === 'andreev') {
            $comment->approved = true;
            $comment->save();
        }
    }
}
