<?php

namespace App\View\Components;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserLayout extends Component
{
    public $page;
    public $my_date;

    /**
     * Create a new component instance.
     */
    public function __construct($page)
    {
        $this->page = $page;
        $this->my_date = Carbon::now()->format('l, F j, Y');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-layout', ['my_date' => $this->my_date]);
    }
}
