<?php

namespace App\Livewire;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminSetting extends Component
{
    use WithFileUploads;

    public $applicationSettings;
    public $darkModeState;
    public $headers;
    public $api_address;
    public $token;
    public $user;
    public $first_name;
    public $last_name;
    // public $username;
    public $email;
    public $place_of_birth;
    public $date_of_birth;
    public $phone_number;
    public $role;
    public $profile_photo_filename;
    public $bio;
    public $app_version;
    public $blog_name;
    public $navbar_color = 'var(--body-color)';
    public $navbar_text_color = 'var(--text-color)';
    public $footer_color = 'var(--body-color)';
    public $footer_text_color = 'var(--text-color)';
    public $logo_filename;
    public $update_password_state = false;
    public $create_address_state = false;
    public $edit_address_state = false;
    public $old_password;
    public $new_password;

    public $addresses;
    // public $street;
    // public $village;
    // public $subdistrict;
    // public $city;
    public $selected_address;

    public $edit_selected_province_address = [];
    public $edit_selected_country_address = [];
    public $province;
    public $country;
    // public $postal_code;

    public $new_password_confirmation; // max 20mb

    #[Validate('image|min:20|max:20000')]
    public $update_profile_image; //

    #[Validate('image|min:20|max:20000')]
    public $update_logo_image;

    public function mount()
    {
        (bool) ($this->darkModeState = (bool) Cookie::get('dark-mode') ? true : false);
        $this->api_address = config('services.api_address');
        $this->token = Cookie::get('token');
        $this->headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
        $this->getAdminSettings();
    }

    public function getAdminSettings()
    {
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->withHeaders($this->headers)->get($this->api_address . 'application-settings'),
            $pool->withHeaders($this->headers)->get($this->api_address . 'info'),
            $pool->withHeaders($this->headers)->get($this->api_address . 'address')
        ]);
        $responses[0] = json_decode($responses[0]->body(), JSON_OBJECT_AS_ARRAY);
        $responses[1] = json_decode($responses[1]->body(), JSON_OBJECT_AS_ARRAY);
        $responses[2] = json_decode($responses[2]->body(), JSON_OBJECT_AS_ARRAY);
        // App
        $this->applicationSettings = $responses[0]['data'];
        $this->app_version = $this->applicationSettings['app_version'];
        $this->blog_name = $this->applicationSettings['blog_name'];
        $this->navbar_color = $this->applicationSettings['navbar_color'];
        $this->navbar_text_color = $this->applicationSettings['navbar_text_color'];
        $this->footer_color = $this->applicationSettings['footer_color'];
        $this->footer_text_color = $this->applicationSettings['footer_text_color'];
        $this->logo_filename = $this->applicationSettings['logo_filename'];
        // User
        $this->user = $responses[1]['data'];
        $this->first_name = $this->user['first_name'];
        $this->last_name = $this->user['last_name'];
        $this->email = $this->user['email'];
        $this->place_of_birth = $this->user['place_of_birth'];
        $this->date_of_birth = $this->user['date_of_birth'];
        $this->phone_number = $this->user['phone_number'];
        $this->role = $this->user['role'];
        $this->profile_photo_filename = $this->user['profile_photo_filename'];
        $this->bio = $this->user['bio'];
        // Address
        $this->addresses = $responses[2]['data'];
        if (!empty($this->addresses)) {
            $this->selected_address = array_filter($this->addresses, function ($address) {
                return $address['is_active'] == true;
            });
        }
    }

    public function updateUserProfileState()
    {
        return;
    }

    #[On('admin-current-page')]
    public function setCurrentPage($data)
    {
        Cookie::queue('current-page', (int) $data);
        if ($data == 1) {
            return redirect('admin');
        }
        if ($data == 2) {
            return redirect('admin/posts');
        }
        if ($data == 3) {
            return redirect('admin/settings');
        }
        if ($data == 4) {
            return redirect('admin/about');
        }
    }
    #[On('dark-mode')]
    public function setDarkModeState()
    {
        Cookie::queue('dark-mode', (bool) !$this->darkModeState);
        $this->darkModeState = !$this->darkModeState;
    }

    public function updateUserProfile()
    {
        // dd(
        //     $this->first_name,
        //     $this->last_name,
        //     $this->email,
        //     $this->bio,
        //     $this->place_of_birth,
        //     $this->date_of_birth,
        //     $this->phone_number,
        //     $this->profile_photo_filename,
        //     $this->old_password
        // );
        Validator::make(['old_password' => $this->old_password], ['old_password' => 'required|string'])->validate();

        if (!empty($this->update_profile_image)) {
            $profile_filename = $this->update_profile_image->hashName();
            File::move($this->update_profile_image->getRealPath(), public_path('assets/user-profile-image/' . $profile_filename));
        }
        $response = Http::withHeaders($this->headers)->patch($this->api_address . 'update', [
            'first_name' => trim($this->first_name),
            'last_name' => trim($this->last_name),
            'email' => trim($this->email),
            'bio' => trim($this->bio),
            'place_of_birth' => trim($this->place_of_birth),
            'date_of_birth' => $this->date_of_birth,
            'phone_number' => $this->phone_number,
            'profile_photo_filename' => empty($profile_filename) ? $this->profile_photo_filename : $profile_filename,
            'old_password' => $this->old_password,
        ]);
        // dd( $response->body());
        if ($response->unauthorized()) {
            return session()->now('status_error', ['message' => 'Password Lama Salah', 'color' => 'danger']);
        }
        session()->flash('status_success', ['message' => 'Berhasil Mengubah Profil', 'color' => 'success']);
        $this->redirect('settings');
    }

    public function updateUserPassword()
    {
        $inputs = Validator::make(['old_password' => $this->old_password, 'new_password' => $this->new_password, 'new_password_confirmation' => $this->new_password_confirmation], ['old_password' => 'required|min:3|max:50|string', 'new_password' => 'required|min:3|max:50|string', 'new_password_confirmation' => 'required|min:3|max:50|same:new_password_confirmation'])->validate();
        $response = Http::withHeaders($this->headers)->patch($this->api_address . 'update-password', [
            'old_password' => $inputs['old_password'],
            'new_password' => $inputs['new_password'],
            'new_password_confirmation' => $inputs['new_password_confirmation'],
        ]);
        if ($response->unauthorized()) {
            return session()->now('status_error', ['message' => 'Password Lama Salah', 'color' => 'danger']);
        }
        session()->flash('status_success', ['message' => 'Berhasil Mengubah Password', 'color' => 'success']);
        $this->redirect('settings');
    }

    public function updateUserPasswordState()
    {
        $this->update_password_state = !$this->update_password_state;
    }
    public function updateAppSettingsState()
    {
        return;
    }

    public function createAddress()
    {
        return;
    }

    public function updateAppSettings()
    {
        Validator::make(['blog_name' => $this->blog_name], ['blog_name' => 'required|string'])->validate();
        if (!empty($this->update_logo_image)) {
            $logo_filename = $this->update_logo_image->hashName();
            File::move($this->update_logo_image->getRealPath(), public_path('assets/logo/' . $logo_filename));
        }
        $response = Http::withHeaders($this->headers)
            ->patch($this->api_address . 'application-settings', [
                'blog_name' => trim($this->blog_name),
                'navbar_color' => $this->navbar_color,
                'navbar_text_color' => $this->navbar_text_color,
                'footer_color' => $this->footer_color,
                'footer_text_color' => $this->footer_text_color,
                'logo_filename' => empty($logo_filename) ? $this->logo_filename : $logo_filename
            ]);
        session()->flash('status_success', ['message' => 'Berhasil Mengubah Konfigurasi Blog', 'color' => 'success']);
        $this->redirect('settings');
    }

    public function createAddressState()
    {
        $this->create_address_state = !$this->create_address_state;
    }

    public function createNewAddress()
    {
        Validator::make(
            ['province' => $this->province, 'country' => $this->country],
            ['province' => 'required|string', 'country' => 'required|string']
        )
            ->validate();
        $response = Http::withHeaders($this->headers)
            ->post($this->api_address . 'address', [
                'province' => $this->province,
                'country' => $this->country,
                'user_id' => $this->user['id']
            ]);
        session()->now('status_address', 'Berhasil membuat alamat');
        $this->getAllAddress();
    }

    public function getAllAddress()
    {
        $response = Http::withHeaders($this->headers)
            ->get($this->api_address . 'address');
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $this->addresses = $response['data'];
    }

    public function editAddressState()
    {
        $this->edit_address_state = !$this->edit_address_state;
        $this->reset('edit_selected_province_address');
        $this->reset('edit_selected_country_address');
    }

    public function editAddress($key)
    {
        if (empty($this->edit_selected_country_address) && empty($this->edit_selected_province_address)) {
            return;
        }          
        if (!empty($this->edit_selected_country_address) && empty($this->edit_selected_province_address)) {

            $id = array_keys($this->edit_selected_country_address)[0];
            // $edit_value_province = $this->edit_selected_province_address[$id];
            $edit_value_country = $this->edit_selected_country_address[$id];
            $current_value_province = "";
            foreach ($this->addresses as $key => $address) :
                if ($address['id'] == $id) {
                    $current_value_province = $address['province'];
                    break;
                }
            endforeach;
            Http::withHeaders($this->headers)
                ->patch($this->api_address . 'address/' . $id, [
                    'user_id' => $this->user['id'],
                    'province' => $current_value_province,
                    'country' => $edit_value_country,
                ]);
        }
        if (empty($this->edit_selected_country_address) && !empty($this->edit_selected_province_address)) {

            $id = array_keys($this->edit_selected_province_address)[0];
            // $edit_value_province = $this->edit_selected_province_address[$id];
            $edit_value_province = $this->edit_selected_province_address[$id];
            $current_value_country = "";
            foreach ($this->addresses as $key => $address) :
                if ($address['id'] == $id) {
                    $current_value_country = $address['country'];
                    break;
                }
            endforeach;
            Http::withHeaders($this->headers)
                ->patch($this->api_address . 'address/' . $id, [
                    'user_id' => $this->user['id'],
                    'province' => $edit_value_province,
                    'country' => $current_value_country,
                ]);
        }
        if (!empty($this->edit_selected_country_address) && !empty($this->edit_selected_province_address)) {
            $id = array_keys($this->edit_selected_country_address)[0];
            $edit_value_province = $this->edit_selected_province_address[$id];
            $edit_value_country = $this->edit_selected_country_address[$id];
            Http::withHeaders($this->headers)
                ->patch($this->api_address . 'address/' . $id, [
                    'user_id' => $this->user['id'],
                    'province' => $edit_value_province,
                    'country' => $edit_value_country,
                ]);
        }
        $this->reset('edit_selected_province_address');
        $this->reset('edit_selected_country_address');
        $this->getAllAddress();
        session()->now('status_address', 'Berhasil mengubah alamat');
    }

    public function deleteAddress($id)
    {
        Http::withHeaders($this->headers)
            ->delete($this->api_address . 'address/' . $id);
        $this->getAllAddress();
        session()->now('status_address', 'Berhasil menghapus alamat');
    }
    public function setMainAddress($id)
    {
        foreach ($this->addresses as $key => $address) :
            if ($address['is_active'] == true) {
                Http::withHeaders($this->headers)
                    ->patch($this->api_address . 'address/' . $address['id'], [
                        'user_id' => $this->user['id'],
                        'province' => $address['province'],
                        'country' => $address['country'],
                        'is_active' => (bool)false
                    ]);
                break;
            }
        endforeach;
        $current_province = "";
        $current_country  = "";
        foreach ($this->addresses as $key => $address) :
            if ($address['id'] == $id) {
                $current_province = $address['province'];
                $current_country = $address['country'];
            }
        endforeach;
        Http::withHeaders($this->headers)
            ->patch($this->api_address . 'address/' . $id, [
                'user_id' => $this->user['id'],
                'province' => $current_province,
                'country' => $current_country,
                'is_active' => (bool)true
            ]);
        $this->getAllAddress();
        session()->now('status_address', 'Berhasil mengubah alamat menjadi alamat utama');
    }
    public function render()
    {
        return view('livewire.admin-setting');
    }
}
