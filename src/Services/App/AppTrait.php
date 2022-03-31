<?php


namespace App\Services\App;


use Cocur\Slugify\Slugify;

trait AppTrait
{
    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->title);
    }
}
