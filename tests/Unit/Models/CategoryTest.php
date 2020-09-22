<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFillable()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testIfUsesTraits()
    {
        $traits = [ SoftDeletes::class, Uuid::class ];
        $categoryTraits = array_keys(class_uses(Category::class));
        //print_r(class_uses(Category::class));
        $this->assertEquals($traits,$categoryTraits);
    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts,$this->category->getCasts());
    }

    public function testIncementing()
    {
        $this->assertFalse($this->category->incrementing);
    }
    public function testDateAttributes()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        //$category = new Category();
        //dd($category->getDates(), $dates);
        foreach ($dates as $date){
            $this->assertContains($date, $this->category->getDates());
            
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }
}
