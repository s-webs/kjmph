<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SelectBlock extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string  $name,
        public string  $label,
        public iterable $options = [],   // можно передать массив или коллекцию
        public ?string $id = null,
        public bool    $required = false,
        public ?string $placeholder = null,
        public $selected = null,         // значение по умолчанию (например, из модели)
        public ?string $optionValue = null, // имя поля для value (id / code и т.п.)
        public ?string $optionLabel = null, // имя поля для текста (name / title и т.п.)
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select-block');
    }
}
