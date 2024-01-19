<div class="my-3 d-flex flex-column justify-content-center align-items-center">
    @if (session('create_address_status'))
    <div class="alert alert-success" role="alert" style="width: 80%">
        {{ session('create_address_status') }}
      </div>
    @endif
    <form wire:submit.prevent="createAddress">
        <div class="mb-3">
            <label for="input1" class="form-label">Masukkan Lokasi</label>
            <input type="text" wire:model="custom_address" class="form-control" id="input1">
            <div id="inputHelp" class="form-text">Masukkan nama lokasi secara kustom</div>
            @error('custom_address')
                <span class="error text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" wire class="btn btn-outline-primary">Buat</button>
    </form>
</div>


{{-- <div class="my-3 d-flex flex-column justify-content-center align-items-center">
    @if (session('create_address_status'))
    <div class="alert alert-success" role="alert" style="width: 80%">
        {{ session('create_address_status') }}
      </div>
    @endif
    <form wire:submit.prevent="createAddress">
        <div class="mb-3">
            <label for="input1" class="form-label">Kabupaten/Kota</label>
            <input type="text" wire:model="city" class="form-control" id="input1">
            <div id="inputHelp" class="form-text">Masukkan nama kabupaten/kota</div>
            @error('city')
                <span class="error text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="input2" class="form-label">Provinsi</label>
            <input type="text" wire:model="province" class="form-control" id="input2"
                >
            <div id="inputHelp" class="form-text">Masukkan nama provinsi</div>
            @error('province')
                <span class="error text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="input3" class="form-label">Negara</label>
            <input type="text" wire:model="country" class="form-control" id="input3"
                >
            <div id="inputHelp" class="form-text">Masukkan nama negara</div>
            @error('country')
                <span class="error text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="input4" class="form-label">Kode Pos</label>
            <input type="text" wire:model="postal_code" class="form-control" id="input4"
                >
            <div id="emailHelp" class="form-text">Masukkan kode pos</div>
            @error('postal_code')
                <span class="error text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" wire class="btn btn-outline-primary">Buat</button>
    </form>
</div> --}}
