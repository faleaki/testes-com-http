<?php

namespace Tests\Feature\Htpp\Controllers\Api;

use Mockery;
use Tests\TestCase;
use ReflectionClass;
use App\Models\CastMember;
use Tests\Traits\TestSaves;
use Illuminate\Http\Request;
use Tests\Traits\TestValidations;
use Illuminate\Support\Facades\Lang;
use Tests\Stubs\Models\CastMemberStub;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\TestResponse;
use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Stubs\Controllers\CastMemberControllerStub;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Api\Model;

class CastMemberControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CastMemberStub::dropTable();
        CastMemberStub::createTable();
        $this->controller = new CastMemberControllerStub();
    }
    protected function tearDown(): void
    {
        CastMemberStub::dropTable();
        parent::tearDown();
    }
    public function testIndex()
    {
        /** @var CastMemberStub */
        $cast_member = CastMemberStub::create(['name' => 'test_name', 'type' => '1']);
        $result = $this->controller->index()->toArray();
        $this->assertEquals([$cast_member->toArray()], $result);
    }

    public function testInvalidationDataInStore()
    {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testStore()
    {
        /** @var Request $request */
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'type' => 1]);
        $obj = $this->controller->store($request);
        $this->assertEquals(
            CastMemberStub::find(1)->toArray(),
            $obj->toArray()
        );
    }

    public function testIfindOrFailFetchModel()
    {
        /** @var CastMemberStub $cast_member */
        $cast_member = CastMemberStub::create(['name' => 'test_name', 'type' => 0]);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$cast_member->id]);
        $this->assertInstanceOf(CastMemberStub::class, $result);
    }

    public function testIfindOrFailFetchModelThrowExceptionWhenIdInvalid()
    {
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function testShow()
    {
        $cast_member = CastMemberStub::create(['name' => 'test_name', 'type' => 0]);
        $result = $this->controller->show($cast_member->id);
        $this->assertEquals($result->toArray(), CastMemberStub::find(1)->toArray());
    }

    public function testUpdate()
    {
        $cast_member = CastMemberStub::create(['name' => 'test_name', 'type' => 0]);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_changed', 'type' => 1]);
        $obj = $this->controller->update($request, $cast_member->id);
        $this->assertEquals(
            CastMemberStub::find(1)->toArray(),
            $obj->toArray()
        );
    }
    public function testDelete()
    {
        $cast_member = CastMemberStub::create(['name' => 'test_name', 'type' => 1]);
        $response = $this->controller->destroy($cast_member->id);
        $this->createTestResponse($response)
            ->assertStatus(204);
    }

}
