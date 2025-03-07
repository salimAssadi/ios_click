<?php

namespace App\View\Components;

use App\Models\Form;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class isoClickForm extends Component
{
    public $identifier;
    public $identifierValue;
    public $form;
    public $formData;

    public function __construct($identifier,$identifierValue)
    {
        $this->identifier = $identifier;
        $this->identifierValue = $identifierValue;
        $this->form = Form::where($this->identifier,$this->identifierValue)->first();
        $this->formData = @$this->form->form_data ?? [];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.iso-click-form');
    }
}
