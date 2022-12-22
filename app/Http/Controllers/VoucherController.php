<?php

namespace App\Http\Controllers;

use App\Traits\FilterQueryBuilder;
use App\Models\Voucher;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\FilterBySpecialValue;
use App\Filters\FilterByDateTime;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateVoucherRequest;
use App\Http\Requests\StoreVoucherRequest;
use App\Transformers\VoucherTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Filters\FilterUniqueValue;
use App\Enums\VoucherTypeEnum;
use App\Http\Requests\ApplyVoucherCodeRequest;


class VoucherController extends Controller
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
         * @get('/api/panel/vouchers')
         * @name('panel.vouchers.index')
         * @middlewares('api', 'auth:sanctum')
         */
        $per_page = request()->input('per_page', 15);

        $vouchers = $this->queryBuilder(Voucher::class)
            ->allowedFilters([
                AllowedFilter::custom('unique', new FilterUniqueValue),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('percentage'),
                AllowedFilter::exact('status'),
                AllowedFilter::custom('rebate', new FilterBySpecialValue),
                AllowedFilter::custom('expired_at', new FilterByDateTime),
                AllowedFilter::custom('created_at', new FilterByDateTime),
            ])
            ->paginate($per_page);

        foreach (VoucherTypeEnum::asArray() as $key => $value) {
            $types[] = ['value' => $value, 'name' => VoucherTypeEnum::getDescription($key)];
        }

        return fractal()
            ->collection($vouchers)
            ->withResourceName('vouchers')
            ->paginateWith(new IlluminatePaginatorAdapter($vouchers))
            ->transformWith(VoucherTransformer::class)
            ->addMeta(['types' => $types])
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreVoucherRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreVoucherRequest $request): JsonResponse
    {
        /**
         * @post('/api/panel/vouchers')
         * @name('panel.vouchers.store')
         * @middlewares('api', 'auth:sanctum')
         */
        $voucherData = $request->safe()->all();

        $voucher = Voucher::query()->create($voucherData);

        return fractal()
            ->item($voucher)
            ->transformWith(VoucherTransformer::class)
            ->withResourceName('vouchers')
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Voucher $voucher): JsonResponse
    {
        /**
         * @get('/api/panel/vouchers/{voucher}')
         * @name('panel.vouchers.show')
         * @middlewares('api', 'auth:sanctum')
         */
        return fractal()
            ->item($voucher)
            ->transformWith(VoucherTransformer::class)
            ->withResourceName('vouchers')
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateVoucherRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateVoucherRequest $request, Voucher $voucher): JsonResponse
    {
        /**
         * @methods('PUT', PATCH')
         * @uri('/api/panel/vouchers/{voucher}')
         * @name('panel.vouchers.update')
         * @middlewares('api', 'auth:sanctum')
         */
        $voucherData = $request->safe()->all();
        $voucher->update($voucherData);

        return apiResponse()->empty();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Voucher $voucher): JsonResponse
    {
        /**
         * @delete('/api/panel/vouchers/{voucher}')
         * @name('panel.vouchers.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $voucher->delete();
        return apiResponse()->empty();
    }
    public function apply(ApplyVoucherCodeRequest $request)
    {
        /**
         * @post('/api/panel/vouchers/apply-voucher')
         * @name('panel.voucher.apply')
         * @middlewares('api', 'auth:sanctum')
         */

        $user = auth()->user();
        $voucher = Voucher::findByCode($request->code, $user->id);

        if (!$voucher) {
            return apiResponse()->message('در حال حاضر چنین کد تخفیفی وجود ندارد')->fail();
        }

        if ($voucher->type === VoucherTypeEnum::SOME_OF_USERS_HAVE_THIS) {
            $number_times_use = $voucher->users->toArray()[0]['number_times_use'];
            $voucher->users()->updateExistingPivot($user->id, [
                'number_times_use' => $number_times_use + 1,
                'last_date_of_use' => now()
            ]);
        }
        return fractal()
            ->item($voucher)
            ->transformWith(VoucherTransformer::class)
            ->withResourceName('vouchers')
            ->respond();
    }
}
