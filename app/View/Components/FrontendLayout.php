<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class FrontendLayout extends Component
{
    /**
     * @param  string|null  $title
     * @param  string|null  $description
     * @param  string|null  $image
     * @param  string|null  $canonical
     * @param  string|null  $robots
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $canonical = null,
        public ?string $robots = null,
    ) {
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.frontend');
    }
}
