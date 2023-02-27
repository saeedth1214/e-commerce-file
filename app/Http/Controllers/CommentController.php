<?php

namespace App\Http\Controllers;

use App\Traits\FilterQueryBuilder;
use App\Models\Comment;
use App\Transformers\CommentTransformer;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\FilterByDateTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CommentController extends Controller
{
    use FilterQueryBuilder;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * @get('/api/panel/comments')
         * @name('panel.comments.index')
         * @middlewares('api', 'auth:sanctum')
         */
        $per_page = request()->input('per_page', 15);
        $comments = $this->queryBuilder(Comment::class)
            ->allowedFilters([
                AllowedFilter::exact('parent_id'),
                AllowedFilter::exact('status'),
                AllowedFilter::custom('created_at', new FilterByDateTime()),
                AllowedFilter::scope('orderby'),
            ])
            ->paginate($per_page);

        $commentsCount = Cache::remember('allComments', 300, function () {
            return DB::table('comments')
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()->toArray();
        });
        return fractal()
            ->collection($comments)
            ->withResourceName('comments')
            ->transformWith(CommentTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($comments))
            ->addMeta(compact('commentsCount'))
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        /**
         * @get('/api/panel/comments/{comment}')
         * @name('panel.comments.show')
         * @middlewares('api', 'auth:sanctum')
         */
        return  fractal()
            ->item($comment)
            ->withResourceName('comments')
            ->transformWith(CommentTransformer::class)
            ->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        /**
         * @delete('/api/panel/comments/{comment}')
         * @name('panel.comments.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $comment->delete();
        return apiResponse()->empty();
    }
}
