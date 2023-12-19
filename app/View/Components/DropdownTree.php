<?php

namespace App\View\Components;

use App\Models\MaintenanceSystem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class DropdownTree extends Component
{
    public function __construct(
        /** @var Collection<int, MaintenanceSystem> */
        public Collection $systems,
        public bool $onlyActive = true,
        public ?MaintenanceSystem $selectedSystem = null,
    ) {
        if ($this->systems->isEmpty()) {
            $this->systems = MaintenanceSystem::all();
        }
    }

    public function render(): View
    {
        return view('components.dropdown-tree');
    }
}
