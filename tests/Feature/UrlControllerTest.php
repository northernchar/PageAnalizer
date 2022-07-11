<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Url;


class UrlControllerTest extends TestCase
{
    protected $urls;
    protected $modelCount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->modelCount = 3;
        $this->urls = Url::factory()->count($this->modelCount)->create();
    }

    public function testIndex()
    {
        $response = $this->get('/urls');
        $response->assertStatus(200);

    }

    public function testIndexWithId()
    {
        $id = $this->urls->first()->toArray()['id'];

        $response = $this->get(route("urls.id", [
            'id' => $id,
        ]));
        $response->assertOk();

        // $url = $this->urls->first()->toArray();
        // $this->assertDatabaseHas('urls', $url);

    }

    public function testStore()
    {
        $data = Url::factory()->make()->only('name');

        $response = $this->post(route('urls.post'), [
            'url' => $data,
        ]);
        $response->assertRedirect(route('urls.id', [
            'id' => $this->modelCount + 1
        ]));
        $response->assertSessionHasNoErrors();

        $parsed = parse_url($data['name'], PHP_URL_SCHEME) . "://" . parse_url($data['name'], PHP_URL_HOST);
        $this->assertDatabaseHas('urls', [
            'name' => $parsed
        ]);

    }
}