<?php

namespace Tests\Feature\Htpp\Controllers\Api;

use Mockery;
use App\Http\Controllers\Api\GenreController;
use App\Models\Genre;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\Exceptions\TestException;
use Illuminate\Http\Request;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations,TestValidations, TestSaves;

    private $genre;
    private $sendData;  

    protected function setUp():void
    {
        parent::setUp();
        $this->genre=factory(Genre::class)->create([
            'is_active' => true
        ]);
        $this->sendData = [
            'name' => 'name',    
        ];
    }
    public function testIndex()
    {
        //$genre=factory(Genre::class)->create();
        $response = $this->get(route('genres.index'));
        //$genre->refresh();
        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }
    public function testShow()
    {
        $genre=factory(Genre::class)->create();
        $response = $this->get(route('genres.show',['genre' => $genre->id]));
        $genre->refresh();
        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidationRequired()
    {
        $data = [
        'name' => '',
        'categories_id' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationMax()
    {
        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationIsActiveField()
    {
        $data = [
            'is_active' => 's'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testSave()
    {
        $category = factory(Category::class)->create();
        $data = [
            [
                'send_data' => $this->sendData + [
                    'categories_id' => [$category->id],
                ],
                'test_data' => $this->sendData + ['is_active' => true]
            ],
            [
                'send_data' => $this->sendData + [ 
                    'is_active' => false,
                    'categories_id' => [$category->id],
                ],
                'test_data' => $this->sendData + [ 'is_active' => false]
            ]
        ];

        foreach ($data as $key => $value){
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);
            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);

            $categoriesCollection=Genre::find($this->genre->id)->categories->map(function($singlecateg)
            {
                return $singlecateg->id;
            });
            $this->assertEquals(
                $categoriesCollection->toArray(),
                [$category->id]);
        }
    }

    public function testRollback()
    {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = \Mockery::mock(Request::class);

        try{
            $controller->store($request);
        }catch (TestException $exception){
            $this->assertCount(1, Genre::all());
        }

    }

    public function testDestroy()
    {
        $genre=factory(Genre::class)->create();
        $response = $this->json('DELETE',route('genres.destroy',['genre' => $genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
    }

    protected function model()
    {
        return Genre::class;
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }
}