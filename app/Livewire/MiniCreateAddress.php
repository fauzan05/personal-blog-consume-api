<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class MiniCreateAddress extends Component
{
    public $user_id;
    public $city;
    public $province;
    public $country;
    public $postal_code;
    public $token;
    public $state = false;
    public function mount()
    {
        $this->token = Cookie::get('token');
        $this->user_id = Cookie::get('current-user');
    }

    public function state()
    {
        $this->state = true;
    }

    public function createAddress()
    {
        $this->validate([ 
            'city' => 'required|string|min:3',
            'province' => 'required|string|min:3',
            'country' => 'required|string|min:3',
            'postal_code' => 'required|integer|min:3',
        ]);
        // dd($this->validate());
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $response = Http::withHeaders($headers)
                    ->post(config('services.api_address') . 'address', [
                        'user_id' => $this->user_id, 
                        'city' => $this->city,
                        'province' => $this->province,
                        'country' => $this->country,
                        'postal_code' => $this->postal_code,
                        'street' => null,
                        'village' => null,
                        'subdistrict' => null,
                    ]);
        // dd($response->body());
        session()->flash('create_address_status', 'Berhasil membuat alamat baru');
        $this->dispatch('create-address-status');
    }
    public function render()
    {
        return view('livewire.mini-create-address');
    }
}
