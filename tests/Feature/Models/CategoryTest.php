<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {
        factory(Category::class,1)->create();
        $categories = Category::all();
        $this->assertCount(1,$categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEquals(
            [
                'id',
                'name',
                'description',
                'is_active',
                'deleted_at',
                'created_at',
                'updated_at'
            ],
            $categoryKey
        );
    }
    public function testCreate()
    {
        $category = Category::create(['name'=>'teste1']);
        $category->refresh();
        $this->assertEquals('teste1',$category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $this->assertRegExp('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/',$category->id);

        $category = Category::create([
            'name'=>'teste1',
            'description'=>null
        ]);
        $this->assertNull($category->description);

        $category = Category::create([
            'name'=>'teste1',
            'description'=> 'teste de descricao'
        ]);
        $this->assertEquals('teste de descricao',$category->description);

        $category = Category::create([
            'name'=>'teste1',
            'is_active'=> false
        ]);
        $this->assertEquals(false,$category->is_active);

        $category = Category::create([
            'name'=>'teste1',
            'is_active'=> true
        ]);
        $this->assertEquals(true,$category->is_active);
    }
    public function testUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create([
            'description' => 'test_description'
        ]);


        //print_r($category->id);

        $data=[
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' => true
        ];
        $category->update($data);

        foreach ($data as $key => $value){
            $this->assertEquals($value, $category->{$key});
        }
    }
    public function testDelete()
    {
        $category = Category::create(['name'=>'teste1']);
        $category->refresh();
        //$id = $category->id;
        //print_r($category->find($id)->getAttributes());
        //print_r($id);
        $category->delete();
        $categories = Category::all();
        $this->assertCount(0,$categories);

    }
}
