<x-filament::widget>
    <x-filament::section header="holidays">
        <x-slot name="heading">
            {{ __('dashboard.holidays') }}
        </x-slot>
        <x-slot name="headerEnd">
            <x-filament::input.wrapper class="p-0">
                <x-filament::input.select wire:model.live="country">
                    @foreach($countries as $country)
                    <option value="{{$country}}">{{ __('dashboard.country.'.$country) }}</option>
                    @endforeach
                </x-filament::select>
            </x-filament::input.wrapper>
        </x-slot>

        <table class="w-full text-sm">
            <thead>
            <tr>
                <th class="text-left">{{ __('dashboard.holiday') }}</th>
                <th class="text-left">{{ __('dashboard.date') }}</th>
                <th class="text-left">{{ __('dashboard.day') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($holidays as $holiday)
                <tr>
                    <td>{{ $holiday['name'] }}</td>
                    <td>{{ $holiday['date'] }}</td>
                    <td>{{ $holiday['day_of_week'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-4 text-gray-500">{{ __('dashboard.country.holidays_not_found') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </x-filament::section>
</x-filament::widget>
