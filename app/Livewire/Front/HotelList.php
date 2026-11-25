<?php

namespace App\Livewire\Front;

use App\Models\Facility;
use App\Models\Hotel;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class HotelList extends Component
{
    use WithPagination;

    #[Url]
    public string $city = '';

    #[Url]
    public string $check_in = '';

    #[Url]
    public string $check_out = '';

    #[Url]
    public int $guests = 2;

    #[Url]
    public string $sort = 'recommended';

    #[Url]
    public ?int $min_price = null;

    #[Url]
    public ?int $max_price = null;

    #[Url]
    public ?int $star = null;

    #[Url]
    public ?int $facility = null;

    public bool $filtersOpen = false;

    public function mount(): void
    {
        $this->check_in = $this->check_in ?: Carbon::today()->toDateString();
        $this->check_out = $this->check_out ?: Carbon::tomorrow()->toDateString();
        $this->guests = max(1, $this->guests);

        if (Carbon::parse($this->check_out)->lte(Carbon::parse($this->check_in))) {
            $this->check_out = Carbon::parse($this->check_in)->addDay()->toDateString();
        }
    }

    public function updated($property): void
    {
        if (in_array($property, ['city', 'check_in', 'check_out', 'guests', 'sort', 'min_price', 'max_price', 'star', 'facility'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['city', 'min_price', 'max_price', 'star', 'facility', 'sort']);
        $this->sort = 'recommended';
        $this->resetPage();
    }

    public function render()
    {
        $conflict = function ($q) {
            $q->where('archived', 0)
                ->where('check_in_date', '<', $this->check_out)
                ->where('check_out_date', '>', $this->check_in)
                ->where(function ($q) {
                    $q->where('status', 'paid')
                        ->orWhere(fn ($q) => $q->where('status', 'pending')->where('expires_at', '>', now()));
                });
        };

        $roomFilter = function ($q) use ($conflict) {
            $q->where('archived', 0)
                ->where('status', 'available')
                ->whereDoesntHave('bookings', $conflict);
        };

        $query = Hotel::query()
            ->active()
            ->with('facilities')
            ->withMin(['rooms' => $roomFilter], 'price')
            ->withAvg('reviews', 'rating')
            ->withCount(['reviews'])
            ->whereHas('rooms', $roomFilter)
            ->when($this->city, fn ($q) => $q->where(function ($q) {
                $q->where('city', 'like', "%{$this->city}%")
                    ->orWhere('name', 'like', "%{$this->city}%");
            }))
            ->when($this->star, fn ($q) => $q->where('star_rating', '>=', $this->star))
            ->when($this->facility, fn ($q) => $q->whereHas('facilities', fn ($q) => $q->where('tbl_facilities.id', $this->facility)))
            ->when($this->min_price, fn ($q) => $q->whereHas('rooms', fn ($r) => $roomFilter($r)->where('price', '>=', $this->min_price)))
            ->when($this->max_price, fn ($q) => $q->whereHas('rooms', fn ($r) => $roomFilter($r)->where('price', '<=', $this->max_price)));

        $query = match ($this->sort) {
            'price_asc' => $query->orderBy('rooms_min_price'),
            'price_desc' => $query->orderByDesc('rooms_min_price'),
            'rating' => $query->orderByDesc('reviews_avg_rating'),
            default => $query->orderByDesc('star_rating')->orderByDesc('reviews_count'),
        };

        return view('livewire.front.hotel-list', [
            'hotels' => $query->paginate(12),
            'cities' => Hotel::active()->distinct()->orderBy('city')->pluck('city'),
            'facilities' => Facility::where('archived', 0)->orderBy('name')->get(),
            'nights' => Carbon::parse($this->check_in)->diffInDays(Carbon::parse($this->check_out)),
        ]);
    }
}
