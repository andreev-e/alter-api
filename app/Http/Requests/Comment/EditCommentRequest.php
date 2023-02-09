<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class EditCommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'comment' => ['required', 'string'],
        ];
    }
}
