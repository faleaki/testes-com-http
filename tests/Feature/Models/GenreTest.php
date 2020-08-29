<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Genre::class,1)->create();
        $genres = Genre::all();
        $this->assertCount(1,$genres);
        $GenreKey = array_keys($genres->first()->getAttributes());
        $this->assertEquals(
            [
                'id',
                'name',
                'is_active',
                'deleted_at',
                'created_at',
                'updated_at'
            ],
            $GenreKey
        );
    }
    public function testCreate()
    {
        $genre = Genre::create(['name'=>'teste1']);
        $genre->refresh();
        $this->assertEquals('teste1',$genre->name);
        $this->assertTrue($genre->is_active);
        $this->assertRegExp('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/',$genre->id);

        $genre = Genre::create([
            'name'=>'teste1'
        ]);
        $this->assertEquals('teste1',$genre->name);

        $genre = Genre::create([
            'name'=>'teste1',
            'is_active'=> false
        ]);
        $this->assertEquals(false,$genre->is_active);

        $genre = Genre::create([
            'name'=>'teste1',
            'is_active'=> true
        ]);
        $this->assertEquals(true,$genre->is_active);
    }
    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create([
            'name' => 'test_name'
        ]);


        //print_r($Genre->id);

        $data=[
            'name' => 'test_name_updated',
            'is_active' => true
        ];
        $genre->update($data);

        foreach ($data as $key => $value){
            $this->assertEquals($value, $genre->{$key});
        }
    }
    public function testDelete()
    {
        $genre = Genre::create(['name'=>'teste1']);
        $genre->refresh();
        //$id = $Genre->id;
        //print_r($Genre->find($id)->getAttributes());
        //print_r($id);
        $genre->delete();
        $genres = Genre::all();
        $this->assertCount(0,$genres);

    }
}
