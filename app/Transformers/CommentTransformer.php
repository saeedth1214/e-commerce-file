<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use App\Enums\CommentStatusEnum;
use App\Models\Comment;
use App\Models\Plan;
use App\Models\User;
use App\Traits\ConvertDateTime;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    use ConvertDateTime;
    protected  array $availableIncludes = [
        'model',
        'replies',
        'acceptedReplies',
        'user',
    ];

    public function transform(Comment $comment)
    {
        return [
            'id' => $comment->id,
            'user_id' => $comment->user_id,
            'parent_id' => $comment->parent_id,
            'content' => $comment->content,
            'status' => $comment->status,
            'status_dec' => CommentStatusEnum::getDescription(CommentStatusEnum::getKey($comment->status)),
            'created_at' => $this->formatDiffrence($comment->created_at),
        ];
    }

    public function IncludeModel(Comment $comment)
    {
        $model = $comment->model;
        if ($model instanceof Plan) {
            return $this->item($model, fn ($model) => ['id' => $model->id, 'title' => $model->title, 'type' => 'plans']);
        }
        return $this->item($model, fn ($model) => ['id' => $model->id, 'title' => $model->title, 'type' => 'files']);
    }

    public function IncludeReplies(Comment $comment)
    {
        $replies = $comment->replies;

        return $this->collection($replies, new self, 'replies');
    }


    public function IncludeAcceptedReplies(Comment $comment)
    {
        $replies = $comment->acceptedReplies;

        return $this->collection($replies, new self, 'replies');
    }

    public function IncludeUser(Comment $comment)
    {
        return $this->item($comment->user, fn ($user) => ['id' => $user->id, 'fullname' => $user->first_name . ' ' . $user->last_name, 'media' => $this->getMediaUrl($user)]);
    }
    private function getMediaUrl(User $user)
    {
        return $user->getFirstMediaUrl('avatar-image');
    }
}
