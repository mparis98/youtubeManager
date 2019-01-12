<?php
/**
 * Created by PhpStorm.
 * User: matthieuparis
 * Date: 10/01/2019
 * Time: 14:08
 */

namespace App\Event;


use App\Entity\User;
use App\Entity\Video;
use Symfony\Component\EventDispatcher\Event;

class VideoEvent extends Event {

    const CREATED = 'video.created';
    const UPDATED = 'video.updated';
    const REMOVED = 'video.removed';


    protected $video;

    public function __construct(Video $video)
    {
        $this->video = $video
        ;
    }
    public function getVideo(): Video
    {
        return $this->video
            ;
    }
}