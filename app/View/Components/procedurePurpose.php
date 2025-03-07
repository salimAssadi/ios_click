<?php

namespace App\View\Components;

use App\Models\ProcedureTemplate;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Config;

class procedurePurpose extends Component
{
    /**
     * Create a new component instance.
     */
    public $purposes;
    
    public function __construct($purposes)
    {   
        $this->purposes = $purposes;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.procedure-purpose');
    }
}
