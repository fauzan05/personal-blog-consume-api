<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\On;

class Login extends Component
{
    #[Validate('required|email')]
    public $email;

    #[Validate('required|string')]
    public $password;
    public $remember_me = false;
    public $message;
    public $darkModeState;

    public function login()
    {
        // dd(isset($_COOKIE['dark-mode']));
        $this->validate();
        // dd($this->email, $this->password, $this->remember_me);
        $response = Http::post('http://127.0.0.1:8000/api/login', [
            'email' => $this->email,
            'password' => $this->password
        ]);
        if($response->unauthorized()) {
            $this->message = json_decode($response->body(), JSON_OBJECT_AS_ARRAY)['error']['error_message'];
            return session()->flash('message', $this->message);
        }
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        if($this->remember_me) {
            Cookie::queue('token', $response['token'], 129600);
            Cookie::queue('current-user', $response['data']['id'], 129600);
        }else{
            Cookie::queue('token', $response['token'], 1440);
            Cookie::queue('current-user', $response['data']['id'], 1440);
        }
        if(!isset($_COOKIE['dark-mode'])) {
            Cookie::queue('dark-mode', (boolean)false);
        }
        Cookie::queue('current-page', (integer)1);
        return redirect('/admin')->with('status', 'Login berhasil');
    }

    #[On('dark-mode')]
    public function setDarkModeState()
    {
        Cookie::queue('dark-mode', (boolean)!$this->darkModeState);
        $this->darkModeState = !$this->darkModeState;
    }

    public function loginButton()
    {
        return; // agar loading sesuai target, maka dibuat function kosongan
    }
    public function render()
    {
        return view('livewire.login');
    }
}
