<?php

namespace Tests\Feature;


use App\Enums\UserRoleEnum;
use App\Models\File;
use App\Models\User;
use App\Traits\DownloadKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadFileTest extends TestCase
{

    use DownloadKey;

    protected function setUp(): void
    {

        parent::setUp();

        // Event::fake();

        $this->userLogin();
    }

    protected function tearDown(): void
    {

        File::truncate();
        // User::query()->where('id', 1)->files()->delete();
        parent::tearDown();
    }
    /**
     * @test
     * 
     */

    public function user_can_download_single_file_if_purchased_it()
    {

        $res = $this->assignSingleFileToUser();

        $res->assertStatus(204);

        $user = auth()->user();

        $files = $user->files()->get();

        $files[0]->update(['link' => 'test link']);

        $this->assertEquals(1, $files->count());

        $res = $this->postJson("/api/frontend/files/{$files[0]->id}/download");

        // Event::assertDispatched(DailyFileDownloadEvent::class, function ($event) use ($files) {
        //     return $event->fileId === $files[0]->id;
        // });

        $downloadKey = $this->fileDownloadKey();

        $download_data = Cache::get($downloadKey);

        $this->assertNull($download_data);
    }

    /**
     * @test
     */

    public function user_can_download_file_if_have_got_a_plan()
    {
        $res = $this->assignFileThatNotSingleFileToUser();

        $res->assertStatus(204);

        $user = auth()->user();

        $files = $user->files()->get();

        $this->assertEquals(1, $files->count());

        $res = $this->post('api/frontend/files"' . $files[0] . '"/download');

        $userKey = $this->userKey($user->id);

        $downloadKey = $this->fileDownloadKey();

        $download_data = Cache::get($downloadKey);

        // dd($download_data);

        $this->assertNotNull($download_data);

        $this->assertEquals(1, $download_data[$userKey]);
    }

    /**
     * @test
     */

    public function user_cant_download_file_if_does_not_have_plan_or_file()
    {
        $fileData = $this->fileData(1);

        $res = $this->post('/api/panel/files', $fileData);

        $user = auth()->user();

        $files = $user->files()->get();

        $user->deActivatePlan(2);

        $this->assertEquals(0, $files->count());

        $res = $this->postJson("/api/frontend/files/1/download");

        $res->assertStatus(403);
    }
    private function userLogin()
    {

        $user = new User();

        $user->id = 1;

        $this->actingAs($user);
    }
    private function assignSingleFileToUser()
    {

        $fileData = $this->fileData(1);

        $res = $this->post('/api/panel/files', $fileData);

        $res->assertStatus(200);

        $single_files = File::query()->where('sale_as_single', 1)->pluck('id');

        $user = auth()->user();

        $userData = [
            'first_name' => "mohammad hasan",
            'last_name' => "soltani ichi",
            'role_id' => UserRoleEnum::ADMIN,
            'files' => $single_files->toArray(),
        ];

        return  $this->put("/api/panel/users/{$user->id}", $userData);
    }

    private function assignFileThatNotSingleFileToUser()
    {

        $fileData = $this->fileData(0);

        $res = $this->post('/api/panel/files', $fileData);


        $res->assertStatus(200);

        $not_single_files = File::query()->where('sale_as_single', 0)->pluck('id');

        $user = auth()->user();

        $userData = [
            'first_name' => "mohammad hasan",
            'last_name' => "soltani ichi",
            'role_id' => UserRoleEnum::ADMIN,
            'files' => $not_single_files->toArray(),
        ];

        return  $this->put("/api/panel/users/{$user->id}", $userData);
    }


    private function fileData($single)
    {

        return  [
            'title' => 'test.title.200',
            'percentage' => 1,
            'rebate' => 5,
            'amount' => 5000,
            'category_id' => 1,
            'sale_as_single' => $single,
        ];
    }
}
