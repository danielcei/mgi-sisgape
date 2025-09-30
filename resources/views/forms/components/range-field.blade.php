<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    x-data="{ open: false }"
>
    <div x-data="{
        formatPrice(value) {
            // Remove tudo que não é dígito
            value = value.replace(/\D/g, '');
            // Adiciona separador de milhar e decimais no padrão brasileiro
            if (value.length > 2) {
                return value.slice(0, -2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + ',' + value.slice(-2);
            }
            return '0,' + value.padStart(2, '0');
        },
        parsePrice(value) {
            // Remove separadores e converte para float
            return parseFloat(value.replace(/\D/g, '') / 100).toFixed(2);
        },
        state: @js($getState()),
        formattedValue() {
            return this.formatPrice(this.state);
        }
    }" class="relative flex items-center">

        <div
            class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white focus-within:ring-2 dark:bg-white/5 ring-gray-950/10 focus-within:ring-primary-600 dark:ring-white/20 dark:focus-within:ring-primary-500 fi-fo-text-input overflow-hidden">
            <div class="items-center gap-x-3 ps-3 flex border-e border-gray-200 pe-3 ps-3 dark:border-white/10">
                <span class="fi-input-wrp-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">R$</span>
            </div>
            <div class="min-w-0 flex-1">
            <input type="text"
                   x-model="state"
                   min="{{ $getMin() }}"
                   max="{{ $getMax() }}"
                   x-bind:value="formattedValue"
                   x-on:input="
           let value = $el.value;
           value = value.replace(/\D/g, '');
           value = (value / 100).toFixed(2);
           value = value.replace('.', ',');
           value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
           state = value;
           $el.value = state;
       "
                   class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] bg-white/0 ps-3 pe-3"
                   placeholder="R$ 0,00"
            {{ $applyStateBindingModifiers('wire:model.debounce.1ms') }}="{{ $getStatePath() }}"
            />
            </div>
        </div>

        <span x-show="false"
              id="range-value-{{ $getStatePath() }}"> {{ $getState() }}</span>


        <button type="button"
                x-data="{ open: false }"
                @mouseover="open = true"
                @mouseleave="open = false"
                class="ml-2 p-2 bg-transparent border-0 outline-none focus:outline-none relative">
            <!-- SVG Icon for Information -->
            <svg class="h-6 w-6 text-gray-500 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="16" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <line x1="12" y1="8" x2="12" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>

            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="absolute bg-white border border-gray-300 rounded-lg shadow-lg p-4 text-sm mt-1 right-0 w-48"
                 style="min-width: 300px">
                <p class="text-gray-700">Min: R$ {{ $getMinFormated() }}</p>
                <p class="text-gray-700">Max: R$ {{ $getMaxFormated() }}</p>
            </div>
        </button>
    </div>
</x-dynamic-component>
