<?php

namespace App\Http\Controllers;

use App\Events\DailyFileDownloadEvent;
use App\Http\Requests\StoreFileRequest;
use App\Traits\FilterQueryBuilder;
use App\Models\File;
use App\Transformers\FileTransformer;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\FilterBySpecialValue;
use App\Filters\FilterUniqueValue;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Requests\StoreFileMediaRequest;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\QueryBuilder\AllowedSort;
use App\Filters\SortByPopular;
use App\Filters\SortBySelling;
use App\Http\Requests\EnsureUserHasFileRequest;
use App\Http\Requests\GenerateTemporaryUrlRequest;
use App\Http\Requests\StoreFileCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use FilterQueryBuilder;
    private $user = null;

    public function __construct()
    {
        $this->user = auth()->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        /**
         * @get('/api/panel/files')
         * @name('panel.files.index')
         * @middlewares('api', 'auth:sanctum')
         *
         * @get('/api/frontend/files')
         * @name('frontend..files')
         * @middlewares('api')
         */
        $per_page = request()->input('per_page', 15);
        $files = $this->queryBuilder(File::class)
            ->allowedFilters([
                'title',
                AllowedFilter::custom('unique', new FilterUniqueValue),
                AllowedFilter::exact('sale_as_single'),
                AllowedFilter::exact('percentage'),
                AllowedFilter::custom('amount', new FilterBySpecialValue),
                AllowedFilter::custom('rebate', new FilterBySpecialValue),
                AllowedFilter::scope('category', 'categoryId'),
                AllowedFilter::scope('category_name', 'categoryName'),
                AllowedFilter::scope('tag_id', 'tagId'),
                AllowedFilter::scope('user_id', 'userId'),
            ])->allowedSorts([
                AllowedSort::custom('bestSelling', new SortBySelling),
                AllowedSort::custom('mostPopular', new SortByPopular),
                'rebate',
                'created_at',
                'amount',
            ])->paginate($per_page);

        return fractal()
            ->collection($files)
            ->paginateWith(new IlluminatePaginatorAdapter($files))
            ->transformWith(FileTransformer::class)
            ->withResourceName('files')
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreFileRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreFileRequest $request): JsonResponse
    {
        /**
         * @post('/api/panel/files')
         * @name('panel.files.store')
         * @middlewares('api', 'auth:sanctum')
         */
        $fileData = $request->safe()->all();
        $file = File::query()->create($fileData);
        return fractal()
            ->item($file)
            ->withResourceName('files')
            ->transformWith(FileTransformer::class)
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(File $file): JsonResponse
    {
        /**
         * @get('/api/panel/files/{file}')
         * @name('panel.files.show')
         * @middlewares('api', 'auth:sanctum')
         *
         * @get('/api/frontend/files/{file}')
         * @name('frontend.show.files')
         * @middlewares('api')
         */
        return fractal()
            ->item($file)
            ->transformWith(FileTransformer::class)
            ->withResourceName('files')
            ->respond();
        // ->serializeWith(new CustomSerializer())
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateFileRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateFileRequest $request, File $file): JsonResponse
    {

        /**
         * @methods('PUT', PATCH')
         * @uri('/api/panel/files/{file}')
         * @name('panel.files.update')
         * @middlewares('api', 'auth:sanctum')
         */
        $fileData = $request->safe()->all();
        $file->update($fileData);
        //assign tags
        if ($request->has('tags')) {
            $tags = $request->input('tags');
            $file->tags()->sync($tags);
        }
        return apiResponse()->empty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(File $file): JsonResponse
    {
        /**
         * @delete('/api/panel/files/{file}')
         * @name('panel.files.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $file->delete();

        return apiResponse()->empty();
    }

    public function uploadFileMedia(File $file, StoreFileMediaRequest $request)
    {
        /**
         * @post('/api/panel/files/{file}/upload-media')
         * @name('panel..files.media')
         * @middlewares('api', 'auth:sanctum')
         */
        try {
            $file->addMediaFromRequest('file')->toMediaCollection('file-image');
        } catch (FileDoesNotExist $exception) {
            return apiResponse()
                ->status(400)
                ->message('File is missing.')
                ->fail();
        } catch (FileIsTooBig $exception) {
            return apiResponse()
                ->status(400)
                ->message('File is too big.')
                ->fail();
        }
        return apiResponse()->empty();
    }


    public function assignComment(StoreFileCommentRequest $request, File $file)
    {
        /**
         * @post('/api/panel/files/{file}/comments')
         * @name('panel.file.comment')
         * @middlewares('api', 'auth:sanctum')
         */
        $commentData = $request->safe()->all();
        $commentData['user_id'] = $this->user->id;
        $comment = $file->comments()->create($commentData);

        return fractal()
            ->item($comment)
            ->transformWith(CommentTransformer::class)
            ->withResourceName('comments');
    }

    public function updateComment(UpdateCommentRequest $request, File $file, int $comment)
    {
        /**
         * @put('/api/panel/files/{file}/comments/{comment}')
         * @name('panel.file.update.comment')
         * @middlewares('api', 'auth:sanctum')
         */
        $updateCommentData = $request->safe()->all();
        $file->comments()->where('id', $comment)->update($updateCommentData);
        return apiResponse()->empty();
    }


    public function CommentsOfFile(File $file)
    {
        /**
         * @get('/api/frontend/files/{file}/comments')
         * @name('frontend.file.comments')
         * @middlewares('api')
         */
        $per_page = request('per_page', 15);

        $comments = $file->acceptedMainComments()->paginate($per_page);

        return fractal()
            ->collection($comments)
            ->transformWith(CommentTransformer::class)
            ->paginateWith(new IlluminatePaginatorAdapter($comments))
            ->withResourceName('comments')
            ->respond();
    }

    public function toggleReaction(File $file)
    {
        $file->toggleReaction('like');

        return apiResponse()->content(['is_reacted' => $file->is_reacted, 'reaction_summary' => $file->reactionSummary()])->success();
    }


    public function download(EnsureUserHasFileRequest $request, File $file)
    {

        try {
            if (is_null($file->link)) {
                return apiResponse()->message('This file not found.')->fail();
            }
            $file_name = $file->getMedia('file-image')[0]->file_name;

            $file_without_ext = substr($file_name, 0, strrpos($file_name, "."));

            if (!Storage::exists($file_without_ext . '.eps') && !Storage::exists($file_without_ext . '.psd')) {
                return apiResponse()->message('This file not found.')->fail();
            }
            $url = $file->link;

            // handle daily download count
            Event::dispatch(new DailyFileDownloadEvent($file));

            File::query()->where('id', $file->id)->increment('download_count');
            return apiResponse()->content(compact('url'))->success();
        } catch (\Throwable $th) {
            $statusCode = 500;
            if ($th instanceof HttpResponseException) {
                $statusCode = $th->getResponse()->getStatusCode();
            }
            return apiResponse()->message($th->getMessage())->status($statusCode)->fail();
        }
    }

    public function generateS3TemporaryUrl(GenerateTemporaryUrlRequest $request, File $file)
    {


        // check Has been set preview  for main file
        if (!isset($file->getMedia('file-image')[0])) {
            return apiResponse()->message('There is no preview for this file.')->fail();
        }

        $expirationTime = $request->input('expiration_time');

        $file_name = $file->getMedia('file-image')[0]->file_name;

        $file_without_ext = substr($file_name, 0, strrpos($file_name, "."));


        $url = Storage::temporaryUrl($file_without_ext . '.eps', now()->addSeconds($expirationTime));

        $file->update([
            'link' => $url
        ]);

        return apiResponse()->empty();
    }
}
