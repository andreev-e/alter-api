<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\AddCommentRequest;
use App\Http\Requests\Comment\EditCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\RouteComment;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $type = $request->input('type');

        if ($type === 'poi') {
            $comments = Comment::query()
                ->when(!$request->input('pending'), function($query) {
                    $query->where('approved', 1);
                })->orderBy('time', 'DESC');
            if ($request->input('id')) {
                $comments->where('backlink', (integer)$request->input('id'));
            }
        }

        if ($type === 'route') {
            $comments = RouteComment::query()
                ->orderBy('date', 'DESC');
            if ($request->input('id')) {
                $comments->where('backlink', (integer)$request->input('id'));
            }
        }

        $comments->with(['user', 'object']);

        return CommentResource::collection($comments->paginate());
    }

    public function store(AddCommentRequest $request): CommentResource
    {
        $comment = Comment::create([
            'name' => Auth::user()->username,
            'email' => Auth::user()->email,
            'approved' => 1,
            'backlink' => $request->get('id'),
            'comment' => $request->get('comment'),
            'time' => Carbon::now()->unix(),
        ]);
        return new CommentResource($comment);
    }

    public function update(EditCommentRequest $request, Comment $comment): CommentResource
    {
        if ($comment->name === Auth::user()->username || Auth::user()->username === 'andreev') {
            $comment->update($request->validated());
        }
        return new CommentResource($comment);
    }

    public function destroy(Comment $comment): \Illuminate\Http\Response|JsonResponse
    {
        if (Auth::user()->username === 'andreev' || Auth::user()->username === $comment->name) {
            $comment->delete();
        }
        return response()->noContent();
    }

    public function approve(Comment $comment)
    {
        if (Auth::user()->username === 'andreev') {
            $comment->approved = true;
            $comment->save();
        }
        return new CommentResource($comment);
    }
}
