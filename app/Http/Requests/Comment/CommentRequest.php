<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => ['sometimes', 'integer'],
            'type' => ['sometimes', 'string'],
        ];
    }
}
