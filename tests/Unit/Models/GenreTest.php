<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{
    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFillable()
    {
        $fillable = ['name', 'is_active'];
        $this->assertEquals($fillable, $this->genre->getFillable());
    }

    public function testIfUsesTraits()
    {
        $traits = [ SoftDeletes::class, Uuid::class ];
        $genreTraits = array_keys(class_uses(Genre::class));
        //print_r(class_uses(Genre::class));
        $this->assertEquals($traits,$genreTraits);
    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts,$this->genre->getCasts());
    }

    public function testIncementing()
    {
        $this->assertFalse($this->genre->incrementing);
    }
    public function testDateAttributes()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        //$genre = new Genre();
        //dd($genre->getDates(), $dates);
        foreach ($dates as $date){
            $this->assertContains($date, $this->genre->getDates());
            
        }
        $this->assertCount(count($dates), $this->genre->getDates());
    }
}
