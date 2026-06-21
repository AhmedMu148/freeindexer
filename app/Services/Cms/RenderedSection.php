<?php

namespace App\Services\Cms;

class RenderedSection
{
    public $id;
    public $title;
    public $wrapperClass;
    public $anchorId;
    public $html;

    public function __construct($id, $title, $wrapperClass, $anchorId, $html)
    {
        $this->id = $id;
        $this->title = $title;
        $this->wrapperClass = $wrapperClass;
        $this->anchorId = $anchorId;
        $this->html = $html;
    }
}
