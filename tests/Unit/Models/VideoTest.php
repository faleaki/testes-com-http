<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use App\Models\Genre;
use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase
{
    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFillable()
    {
        $fillable = ['title', 'description', 'year_launched', 'opened', 'rating', 'duration'];
        $this->assertEquals($fillable, $this->video->getFillable());
    }

    public function testIfUsesTraits()
    {
        $traits = [ SoftDeletes::class, Uuid::class, UploadFiles::class ];
        $videoTraits = array_keys(class_uses(Video::class));
        //print_r(class_uses(Video::class));
        $this->assertEquals($traits,$videoTraits);
    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'opened' => 'boolean', 'year_launched' => 'integer', 'duration' => 'integer'];
        $this->assertEquals($casts,$this->video->getCasts());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->video->incrementing);
    }
    public function testDateAttributes()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        //$video = new Video();
        //dd($video->getDates(), $dates);
        foreach ($dates as $date){
            $this->assertContains($date, $this->video->getDates());
            
        }
        $this->assertCount(count($dates), $this->video->getDates());
    }
}
