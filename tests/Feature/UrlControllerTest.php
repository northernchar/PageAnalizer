<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Url;
use App\Models\UrlCheck;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlControllerTest extends TestCase
{
    protected $urls;
    protected $modelCount;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('urls')->insert([
            'name' => 'https://hexlet.io',
            'created_at' => fake()->date(),
        ]);
        DB::table('urls')->insert([
            'name' => 'https://code-basics.com',
            'created_at' => fake()->date(),
        ]);
        DB::table('urls')->insert([
            'name' => 'https://todoist.com',
            'created_at' => fake()->date(),
        ]);

        $this->modelCount = DB::table('urls')->count();
        $this->urls = DB::table('urls')->select()->get();
    }

    public function testIndex()
    {
        $response = $this->get('/urls');
        $response->assertStatus(200);
    }

    public function testIndexWithId()
    {
        $id = 1;

        $response = $this->get(route("urls.id", [
            'id' => $id,
        ]));
        $response->assertOk();
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

    public function testChecks()
    {
        $url_id = 2;

        DB::table('url_checks')->insert([
            'url_id' => $url_id,
            'created_at' => fake()->date(),
        ]);

        Http::fake();

        $response = $this->post(route('url.check', [ 'id' => $url_id]))
            ->assertRedirect(route('urls.id', ['id' => $url_id ]));
            
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('url_checks', [
            'url_id' => $url_id
        ]);
    }
}
