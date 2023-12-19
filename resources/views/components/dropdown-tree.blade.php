<div
    x-data="{
        selectedSystem: {{ Js::from($selectedSystem) }},
    }"
    id='maintenanceSystemDropdownTree'
    x-on:maintenance-system-selected.window.camel="selectedSystem = $event.detail"
    {{ $attributes }}
>
    @foreach ($systems as $system)
        <div
            x-data="{
                system: {{ Js::from($system) }},
                onlyActive: {{ $onlyActive }},
                expanded: false,

                init() {
                    this.expandAncestors();

                    $watch('selectedSystem', value => this.expandAncestors());
                },

                expandAncestors() {
                    let ancestors = this.selectedSystem?.ancestors_and_self?.map(s => s?.id).reverse();

                    if (ancestors) {
                        this.expanded = this.system.id == ancestors[this.system.depth];
                    } else {
                        this.expanded = false;
                    }
                },
            }"
            x-on:maintenance-system-show-active.window.camel="onlyActive = $event.detail"
            x-cloak
            x-show="
                if (onlyActive) {
                    return system.is_active
                } else return true;
            "
            x-bind:id="'maintenanceSystemDropdownTree::system_' + system?.id"
        >
            <div
                class="flex-center flex items-center justify-between border-b hover:cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-800"
                x-on:click="expanded = ! expanded"
                x-bind:class="system.id == selectedSystem?.id && 'bg-gray-200 dark:bg-gray-700'"
            >
                <div class="flex flex-1 items-center justify-between font-semibold">
                    <div class="flex items-center justify-start">
                        <div class="mr-2" x-cloak>
                            @if ($system->children->isNotEmpty())
                                <x-heroicon-s-plus class="w-4 text-green-500" x-show="! expanded" />
                                <x-heroicon-s-minus class="w-4 text-red-500" x-show="expanded" />
                            @else
                                <span class="text-red-500">&nbsp;&nbsp;</span>
                            @endif
                        </div>
                        <span class="flex-1">{{ $system->name }}</span>
                    </div>
                    @unless ($system->is_active)
                        <x-tag.alert size="text-sm">inactive</x-tag.alert>
                    @endunless
                </div>
                <div x-on:click.stop>
                    {{ $slot }}
                </div>
            </div>

            @unless ($system->children->isEmpty())
                <div
                    class="pl-4"
                    x-show="expanded"
                    x-collapse
                >
                    <x-maintenance.system.dropdown-tree :systems="$system->children" :$selectedSystem>
                        {{ $slot }}
                    </x-maintenance.system.dropdown-tree>
                </div>
            @endunless
        </div>
    @endforeach
</div>
