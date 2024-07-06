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

    private string $endpoint = 'api/v1/notifications';

    public function test_guest(): void
    {
        $this->getJson($this->endpoint)->assertUnauthorized(); // index
        $this->getJson("{$this->endpoint}/1")->assertMethodNotAllowed(); // show
        $this->postJson($this->endpoint)->assertMethodNotAllowed(); // store
        $this->putJson("{$this->endpoint}/some-id")->assertUnauthorized(); // update
        $this->putJson($this->endpoint)->assertUnauthorized(); // update all
        $this->deleteJson("{$this->endpoint}/1")->assertMethodNotAllowed(); // destroy
    }

    public function test_user(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson($this->endpoint)->assertOk(); // index
        $this->getJson("{$this->endpoint}/1")->assertMethodNotAllowed(); // show
        $this->postJson($this->endpoint)->assertMethodNotAllowed(); // store
        $this->putJson("{$this->endpoint}/some-id")->assertUnprocessable(); // update
        $this->putJson($this->endpoint)->assertUnprocessable(); // update all
        $this->deleteJson("{$this->endpoint}/1")->assertMethodNotAllowed(); // destroy
    }

    public function test_update(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $data = [
            'is_read' => true
        ];

        $this->putJson("{$this->endpoint}/some-id", $data)->assertValid();
    }

    public function test_update_invalid(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $data = [];

        $this->putJson("{$this->endpoint}/some-id", $data)
        ->assertInvalid([
            'is_read'
        ]);

        $data = [
            'is_read'
        ];

        $this->putJson("{$this->endpoint}/some-id", $data)
        ->assertInvalid([
            'is_read'
        ]);

        $data = [
            'is_read' => null
        ];

        $this->putJson("{$this->endpoint}/some-id", $data)
        ->assertInvalid([
            'is_read'
        ]);

        $data = [
            'is_read' => ''
        ];

        $this->putJson("{$this->endpoint}/some-id", $data)
        ->assertInvalid([
            'is_read'
        ]);

        $data = [
            'is_read' => ' '
        ];

        $this->putJson("{$this->endpoint}/some-id", $data)
        ->assertInvalid([
            'is_read'
        ]);
    }

    public function test_update_all(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $data = [
            'are_read' => true
        ];

        $this->putJson($this->endpoint, $data)->assertValid();
    }

    public function test_update_all_invalid(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $data = [];

        $this->putJson($this->endpoint, $data)
        ->assertInvalid([
            'are_read'
        ]);

        $data = [
            'are_read'
        ];

        $this->putJson($this->endpoint, $data)
        ->assertInvalid([
            'are_read'
        ]);

        $data = [
            'are_read' => null
        ];

        $this->putJson($this->endpoint, $data)
        ->assertInvalid([
            'are_read'
        ]);

        $data = [
            'are_read' => ''
        ];

        $this->putJson($this->endpoint, $data)
        ->assertInvalid([
            'are_read'
        ]);

        $data = [
            'are_read' => ' '
        ];

        $this->putJson($this->endpoint, $data)
        ->assertInvalid([
            'are_read'
        ]);
    }
}
