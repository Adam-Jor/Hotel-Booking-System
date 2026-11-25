<div class="rounded-card border border-line bg-white p-5">
    <h2 class="text-base font-bold">Quick booking</h2>
    <p class="mt-0.5 text-xs text-muted">Create a draft for a walk-in guest. No room held until it becomes pending.</p>

    <form wire:submit="save" class="mt-4 space-y-3">
        <div>
            <label class="label">Customer name <span class="text-accent">*</span></label>
            <input wire:model="customer_name" type="text" placeholder="Guest name" class="field">
            @error('customer_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">Hotel <span class="text-accent">*</span></label>
            <select wire:model.live="hotel_id" class="field">
                <option value="">Select hotel</option>
                @foreach ($hotels as $hotel)
                    <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                @endforeach
            </select>
            @error('hotel_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">Room <span class="text-accent">*</span></label>
            <select wire:model="room_id" class="field" @disabled(! $hotel_id)>
                <option value="">Select room</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_number }} · {{ $room->roomType?->name }} · Rp {{ number_format((float) $room->price, 0, ',', '.') }}</option>
                @endforeach
            </select>
            @error('room_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="label">Check-in</label>
                <input wire:model="check_in" type="date" class="field px-2">
            </div>
            <div>
                <label class="label">Check-out</label>
                <input wire:model="check_out" type="date" class="field px-2">
            </div>
        </div>
        @error('check_in') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        @error('check_out') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

        <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">Create draft</span>
            <span wire:loading wire:target="save">Saving…</span>
        </button>
    </form>
</div>
