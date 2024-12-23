<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserLayout extends Component
{
    public $page;
    public $currentOrganization;

    public function __construct($page, $currentOrganization = null)
    {
        $this->page = $page;
        $this->currentOrganization = $currentOrganization;
    }

    public function render()
    {
        return view('components.user-layout');
    }
}
