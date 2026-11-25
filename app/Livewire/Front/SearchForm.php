<?php

namespace App\Livewire\Front;

use App\Models\Hotel;
use Illuminate\Support\Carbon;
use Livewire\Component;

class SearchForm extends Component
{
    public string $city = '';
    public string $check_in = '';
    public string $check_out = '';
    public int $guests = 2;

    public function mount(): void
    {
        $this->city = (string) request('city', '');
        $this->check_in = (string) request('check_in', Carbon::today()->toDateString());
        $this->check_out = (string) request('check_out', Carbon::tomorrow()->toDateString());
        $this->guests = (int) request('guests', 2);
    }

    public function search()
    {
        $this->validate([
            'city' => ['nullable', 'string', 'max:100'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        if (Carbon::parse($this->check_in)->diffInDays(Carbon::parse($this->check_out)) > 30) {
            $this->addError('check_out', 'Maximum stay is 30 nights.');

            return;
        }

        return $this->redirect(route('hotels.index', [
            'city' => $this->city ?: null,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'guests' => $this->guests,
        ]), navigate: true);
    }

    public function render()
    {
        return view('livewire.front.search-form', [
            'cities' => Hotel::active()->distinct()->orderBy('city')->pluck('city'),
        ]);
    }
}
