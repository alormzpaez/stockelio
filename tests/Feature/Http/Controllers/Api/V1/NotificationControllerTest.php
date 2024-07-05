<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest(): void
    {
        $user = User::factory()->create();

        $this->getJson("api/v1/users/{$user->id}/notifications")->assertUnauthorized(); // index
        $this->getJson("api/v1/users/{$user->id}/notifications/1")->assertNotFound(); // show
        $this->postJson("api/v1/users/{$user->id}/notifications")->assertMethodNotAllowed(); // store
        $this->putJson("api/v1/users/{$user->id}/notifications/1")->assertNotFound(); // update
        $this->deleteJson("api/v1/users/{$user->id}/notifications/1")->assertNotFound(); // destroy
    }

    public function test_user(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $this->getJson("api/v1/users/{$user->id}/notifications")->assertOk(); // index
        $this->getJson("api/v1/users/{$user->id}/notifications/1")->assertNotFound(); // show
        $this->postJson("api/v1/users/{$user->id}/notifications")->assertMethodNotAllowed(); // store
        $this->putJson("api/v1/users/{$user->id}/notifications/1")->assertNotFound(); // update
        $this->deleteJson("api/v1/users/{$user->id}/notifications/1")->assertNotFound(); // destroy
    }
}
