<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $name,
        public bool $show = false,
        public string $maxWidth = '2xl'
    ) {}

    public function render()
    {
        return view('components.modal');
    }
} 