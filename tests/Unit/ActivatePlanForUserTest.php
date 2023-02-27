<?php

namespace Tests\Unit;


use App\Enums\PlanStatusEnum;
use App\Models\User;
use Tests\TestCase;

class ActivatePlanForUserTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->userLogin();
    }

    public function tearDown(): void
    {

        // $this->removePlanFromUser(2);
        parent::tearDown();
    }

    /**
     * @test 
     * @dataProvider userPlanData
     */
    public function activate_plan_for_user($planId, $userId)
    {

        $res = $this->post("api/panel/users/${userId}/plans", ['plan_id' => $planId]);

        $res->assertStatus(204);

        $user = User::query()->where('id', $userId)->first();

        $plan = $user->plans()->first();


        $this->assertTrue($plan->pivot->status === PlanStatusEnum::ACTIVE);
    }

    /**
     * @test
     * @dataProvider userPlanData
     */
    public function deActivate_plan_from_user($planId, $userId)
    {

        $res = $this->put("api/panel/users/${userId}/plans/${planId}");

        $res->assertStatus(204);

        $user = User::query()->where('id', $userId)->first();

        $plan = $user->plans()->first();

        $this->assertTrue($plan->pivot->status === PlanStatusEnum::INACTIVE);
    }
    /**
     * @test
     * @dataProvider userPlanData
     */
    public function user_can_not_deActive_plan_does_not_exist_active_plan($planId, $userId)
    {

        $res = $this->put("api/panel/users/${userId}/plans/${planId}");

        $res->assertStatus(400);

        $user = User::query()->where('id', $userId)->first();

        $plan = $user->activePlan();

        $this->assertNull($plan);

        $plan = $user->plans()->first();

        $this->assertTrue($plan->pivot->status === PlanStatusEnum::INACTIVE);
    }




    private function userLogin()
    {

        $user = new User();

        $user->id = 1;

        $this->actingAs($user);
    }

    private function removePlanFromUser($userId)
    {

        $user = User::query()->where('id', $userId)->first();

        $user->plans()->delete();
    }

    // data provider
    public function userPlanData()
    {
        return [
            ['plan_id' => 1, 'user_id' => 2],
        ];
    }
}
