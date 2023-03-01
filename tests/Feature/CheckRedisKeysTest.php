<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class CheckRedisKeysTest extends TestCase
{

    private $file;
    protected function setUp(): void
    {

        parent::setUp();

        $this->file = File::query()->findOrFail(4);
    }

    protected function tearDown(): void
    {

        Redis::flushAll();
        parent::tearDown();
    }

    /**
     * @test
     */

    public function check_some_keys_in_redis_when_have_been_shown_file()
    {

        $this->getJson("/api/frontend/files/{$this->file->id}");
        $this->assertEquals(1, Redis::exists($this->file->id));
        $this->assertEquals($this->file->title, Redis::hget($this->file->id, 'title'));
        $this->assertEquals($this->file->category->name, Redis::hget($this->file->id, 'category_name'));
        $this->assertEquals(1, Redis::hget($this->file->id, 'views'));
    }
    /**
     * @test
     */

    public function increment_views_when_show_a_file()
    {

        $this->getJson("/api/frontend/files/{$this->file->id}");
        $this->assertEquals(1, Redis::hget($this->file->id, 'views'));

        $this->getJson("/api/frontend/files/{$this->file->id}");
        $this->assertEquals(2, Redis::hget($this->file->id, 'views'));

        $this->file = File::query()->findOrFail(1);

        $this->getJson("/api/frontend/files/{$this->file->id}");
        $this->assertEquals(1, Redis::hget($this->file->id, 'views'));


        $this->getJson("/api/frontend/files/{$this->file->id}");
        $this->assertEquals(2, Redis::hget($this->file->id, 'views'));
    }

    /**
     * @test
     */

    public function check_some_keys_in_redis_after_update_file()
    {

        $data = [
            'title' => 'test-title',
            'sale_as_single' => true,
            'category_id' => 4,
            'amount' => 15000,
        ];
        $user = new User();

        $user->id = 1;

        $this->actingAs($user);

        $this->putJson("/api/panel/files/{$this->file->id}", $data);

        $updatedFile = File::query()->find($this->file->id);

        $categoryName = Category::query()->firstWhere('id', $updatedFile->id)->name;

        $this->assertEquals('test-title', $updatedFile->title);
        $this->assertEquals('test-title', Redis::hget($updatedFile->id, 'title'));

        $this->assertEquals($categoryName, $updatedFile->category->name);
        $this->assertEquals($categoryName, Redis::hget($updatedFile->id, 'category_name'));
    }
}
