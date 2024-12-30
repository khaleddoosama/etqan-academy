<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MediaDisplay extends Component
{
    public $path;
    public $type;
    public $title;
    public $id;

    public function __construct($path, $type, $title, $id)
    {
        $this->path = $path;
        $this->type = $type;
        $this->title = $title;
        $this->id = $id;
    }

    public function render()
    {
        return view('components.media-display');
    }
}
