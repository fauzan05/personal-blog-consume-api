<?php

namespace App\Livewire;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class AdminPost extends Component
{
    use WithFileUploads;
    public $api_address;
    public $user_id;
    public $token;
    public $posts;
    public $body;
    public $tags;
    public $tag;
    public $categories;
    public $category;
    public $addresses;
    public $custom_address;
    public $selected_tag = [];
    public $selected_category;
    public $selected_address;
    public $title;
    public $createLocationState = false;
    public $createCategoryState = false;
    public $createTagState = false;
    public $optionTagState = false;
    public $optionCategoryState = false;
    public $update_tag = [];
    public $update_category = [];
    // Config Mode
    public $current_post_id = 0;
    public $configState = false;
    public $selected_post_state = false;
    public $selected_post_id = 0;
    public $selected_post = [];
    public $current_image;
    public $update_title;
    public $update_image;
    public $body_update;
    public $update_current_tag = [];
    public $update_current_category;
    public $update_current_address;
    public $update_selected_address;
    public $created_at;
    public $updated_at;
    public $searchTitle;

    #[Validate('image|min:20|max:20000')] // max 20mb
    public $image;
    public $darkModeState;
    public $headers;

    // Posts Mode
    public $showPostsModeState = false;
    public $searchPostState = false;
    public $filterPostState = false;

    protected $rules = [
        'body' => 'required|string|min:1',
        'title' => 'required|string|max:100',
        'selected_tag' => 'required|array',
        'selected_category' => 'required|integer',
    ];

    public function mount()
    {
        (bool)$this->darkModeState = (bool)Cookie::get('dark-mode') ? true : false;
        $this->api_address = config('services.api_address');
        $this->token = Cookie::get('token');
        $this->headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
        $this->user_id = Cookie::get('current-user');
        $this->getAllData();
        $this->update_current_tag = $this->tags;
        $this->getAllPosts();
    }

    public function showPostsMode()
    {
        $this->showPostsModeState = !$this->showPostsModeState;
    }

    public function configMode()
    {
        $this->showPostsModeState = false;
        $this->configState = !$this->configState;
        // $this->selected_tag = $this->tags;
        $this->selected_post_state = !$this->selected_post_state;
        $this->dispatch('reset-body', data: "");
        $this->optionTagState = false;
        $this->createTagState = false;
        $this->optionCategoryState = false;
        $this->optionCategoryState = false;
        $this->createLocationState = false;
        $this->reset('update_title');
        $this->reset('update_current_tag');
        $this->reset('update_current_category');
        $this->reset('update_current_address');
        $this->reset('body_update');
        $this->reset('update_image');
        $this->reset('custom_address');
        $this->getAllPosts();
    }

    #[On('admin-current-page')]
    public function setCurrentPage($data)
    {
        Cookie::queue('current-page', (int)$data);
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
        Cookie::queue('dark-mode', (bool)!$this->darkModeState);
        $this->darkModeState = !$this->darkModeState;
    }

    public function resetFormCustomAddress()
    {
        $this->reset('custom_address');
        $this->reset('update_current_address');
    }

    public function createAddress()
    {
        $this->createLocationState = !$this->createLocationState;
        // $this->createCategoryState = !$this->createCategoryState;
        // $this->createTagState = !$this->createTagState;
    }
    public function setCreateCategoryState()
    {
        $this->createCategoryState = !$this->createCategoryState;
        // $this->createLocationState = !$this->createLocationState;
        // $this->createTagState = !$this->createTagState;
        $this->reset('category');
    }
    public function setCreateTagState()
    {
        $this->createTagState = !$this->createTagState;
        // $this->createCategoryState = !$this->createCategoryState;
        // $this->createLocationState = !$this->createLocationState;
        $this->reset('tag');
    }

    // LOADING
    public function loadingCreateTagState()
    {
        return;
    }
    public function loadingCreateCategoryState()
    {
        return;
    }
    public function loadingCreatePostState()
    {
        return;
    }

    // #[On('tags-update')]
    public function createTag()
    {
        Validator::make(['tag' => $this->tag], ['tag' => 'required|string'], ['required' => 'Kolom :attribute harus diisi'])->validate();
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
        Http::withHeaders($headers)->post($this->api_address . 'tag', [
            'name' => trim($this->tag),
        ]);
        $this->getTags();
        $this->dispatch('tags-update');
        session()->now('create_tag_status', 'Berhasil membuat tag ' . trim($this->tag));
    }

    // #[On('categories-update')]
    public function createCategory()
    {
        Validator::make(['category' => $this->category], ['category' => 'required|string'], ['required' => 'Kolom :attribute harus diisi'])->validate();
        $respose = Http::withHeaders($this->headers)->post($this->api_address . 'category', [
            'name' => trim($this->category),
            'description' => null,
        ]);
        $this->getCategories();
        $this->dispatch('categories-update');
        session()->now('create_category_status', 'Berhasil membuat kategori ' . trim($this->category));
    }

    #[On('body')]
    public function getBody($data)
    {
        $this->body = $data;
    }
    #[On('body-updated')]
    public function getBodyUpdated($data)
    {
        $this->body_update = $data;
    }

    public function post()
    {
        // dd($this->selected_tag);

        // $this->createTagState = false;
        // $this->createCategoryState = false;
        if (empty($this->tags)) {
            Validator::make(['selected_tag' => $this->selected_tag], ['selected_tag' => 'required|array'], ['required' => 'Postingan wajib memiliki tag, jika tidak ada buat terlebih dahulu'])->validate();
        }

        if (empty($this->categories)) {
            Validator::make(['selected_category' => $this->selected_category], ['selected_category' => 'required|integer'], ['required' => 'Postingan wajib memiliki kategori, jika tidak ada buat terlebih dahulu'])->validate();
        }
        if (empty($this->addresses)) {
            $this->createLocationState = true;
            Validator::make(['custom_address' => $this->custom_address], ['custom_address' => 'required|string'], ['required' => 'Postingan wajib memiliki alamat, jika tidak ada buat terlebih dahulu'])->validate();
        }
        $this->validate();
        Validator::make(['selected_address' => $this->selected_address], ['selected_address' => $this->createLocationState ? 'nullable|integer' : 'required|integer'])->validate();

        // mencari id address pada property selected_address, setelah itu dibuat menjadi string
        if (!$this->createLocationState) {
            $selected_address = '';
            foreach ($this->addresses as $key => $address) :
                if ($address['id'] == $this->selected_address) {
                    $selected_address = $address['city'] . ', ' . $address['province'];
                }
            endforeach;
        }

        // memindahkan file dari temp ke images
        $images_filename = [];
        $pattern = '/src="http:\/\/127.0.0.1:8001\/temp\/([^"]+)"/';
        if (preg_match_all($pattern, $this->body, $matches)) {
            $images_filename = $matches;
            // dd($images_filename);
        }
        foreach ($images_filename as $key => $image_filename) :
            if ($key === 1) {
                foreach ($image_filename as $item) :
                    // $image_path = public_path('temp/' . $item);
                    File::move(public_path('temp/' . $item), public_path('assets/images/' . $item));
                endforeach;
            }
        endforeach;
        // ubah lokasi dari temp ke images
        $this->body = str_replace('/temp/', '/assets/images/', $this->body);

        $response = Http::withHeaders($this->headers)->post($this->api_address . 'post', [
            'user_id' => $this->user_id,
            'category_id' => $this->selected_category,
            'tag_id' => array_keys($this->selected_tag),
            'title' => $this->title,
            'content' => $this->body,
            'location' => $this->createLocationState ? $this->custom_address : $selected_address,
        ]);


        // // File Image Upload
        // $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        // $post_id = $response['data'][0]['id'];
        // $path = $this->image->getRealPath();
        // $originalName = $this->image->getClientOriginalName();
        // $response = Http::attach('image_file', file_get_contents($path), $originalName)
        //     ->withHeaders($this->headers)
        //     ->post($this->api_address . 'media', [
        //         'post_id' => $post_id,
        //     ]);
        // File Image Upload #2
        $type = $this->image->extension();
        $filename = $this->image->hashName();
        File::move($this->image->getRealPath(), public_path('assets/images/' . $filename));
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $post_id = $response['data'][0]['id'];
        $path = public_path('assets/images' . $filename);
        $response = Http::post($this->api_address . 'media-link', [
            'post_id' => $post_id,
            'name' => $filename,
            'file_path' => $path,
            'type' => $type
        ]);
        // dd($response->body());
        // $response = 
        // dd($this->image->getRealPath());
        // dd($response->body());
        $this->createCategoryState = false;
        $this->createLocationState = false;
        $this->createTagState = false;
        session()->flash('create_post_status', 'Berhasil Membuat Postingan Dengan Judul ' . $this->title);
        $this->redirect('posts');
    }

    public function getAllData()
    {
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->withHeaders($this->headers)->get($this->api_address . 'tag'),
            $pool->withHeaders($this->headers)->get($this->api_address . 'category'),
            $pool->withheaders($this->headers)->get($this->api_address . 'address')
        ]);
        $responses[0] = json_decode($responses[0]->body(), JSON_OBJECT_AS_ARRAY);
        $responses[1] = json_decode($responses[1]->body(), JSON_OBJECT_AS_ARRAY);
        $responses[2] = json_decode($responses[2]->body(), JSON_OBJECT_AS_ARRAY);
        $this->tags = $responses[0]['data'] ?? [];
        $this->categories = $responses[1]['data'] ?? [];
        $this->addresses = $responses[2]['data'] ?? [];

        // $this->update_tag = $this->tags;
    }
    // Option
    public function optionTag()
    {
        $this->optionTagState = !$this->optionTagState;
        // $this->selected_post_state = false;
        // $this->selectedPost($this->selected_post_id);
        $this->reset('selected_tag');
    }
    public function optionCategory()
    {
        $this->optionCategoryState = !$this->optionCategoryState;
        $this->reset('update_category');
    }

    // Edit
    public function editTag($currentName)
    {
        if (empty($this->update_tag)) {
            return;
        }
        // dd($this->update_tag);
        $id = array_keys($this->update_tag)[0];
        $name = $this->update_tag[$id];

        // dd($name);
        $response = Http::withHeaders($this->headers)
            ->patch($this->api_address . 'tag/' . $id, [
                'update_name' => $name
            ]);
        if ($response->conflict()) {
            return session()->now('update_tag_status', 'Nama tag sudah digunakan');
        }
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        // dd($response);

        $this->reset('update_tag');
        $this->getTags();
        $this->getAllPosts();
        foreach ($this->posts as $key => $post) :
            if ($post['id'] == $id) {
                $this->selected_post = $post;
                break;
            }
        endforeach;
        $this->reset('update_current_tag');
        foreach ($this->selected_post['tag'] as $tagid) :
            $this->update_current_tag[$tagid['id']] = $tagid;
        endforeach;
        session()->now('update_tag_status', 'Berhasil mengubah nama tag ' . $currentName . ' menjadi ' . $response['data']['name']);
    }

    public function deleteTag($id)
    {
        Http::withHeaders($this->headers)
            ->delete($this->api_address . 'tag/' . $id);
        $this->getTags();
        session()->now('update_tag_status', 'Berhasil menghapus tag ');
    }

    public function editCategory($currentName)
    {
        // dd($this->update_category);
        if (empty($this->update_category)) {
            return;
        }
        $id = array_keys($this->update_category)[0];
        $name = $this->update_category[$id];
        // dd($currentName);
        $response = Http::withHeaders($this->headers)
            ->patch($this->api_address . 'category/' . $id, [
                'update_name' => $name,
                'description' => null
            ]);
        if ($response->conflict()) {
            return session()->now('update_category_status', 'Nama kategori sudah digunakan');
        }
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $this->getCategories();
        $this->reset('update_category');
        session()->now('update_category_status', 'Berhasil mengubah nama kategori ' . $currentName . ' menjadi ' . $response['data']['name']);
    }

    public function deleteCategory($id)
    {
        Http::withHeaders($this->headers)
            ->delete($this->api_address . 'category/' . $id);
        $this->getCategories();
        session()->now('update_category_status', 'Berhasil menghapus kategori');
    }

    public function getTags()
    {
        $response = Http::withHeaders($this->headers)->get($this->api_address . 'tag');
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $this->tags = $response['data'];
    }

    public function getCategories()
    {
        $response = Http::withHeaders($this->headers)->get($this->api_address . 'category');
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $this->categories = $response['data'];
    }

    public function getAllPosts()
    {
        $response = Http::withHeaders($this->headers)
            ->get($this->api_address . 'post');
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $this->posts = $response['data'];
    }

    public function selectedPost($id)
    {
        // dd($id);
        if ($this->selected_post_id != $id) {
            $this->selected_post_id = $id;
            $this->selected_post_state = true;
            $this->reset('selected_tag');
        } elseif ($this->selected_post_id == $id) {
            $this->selected_post_id = 0;
            $this->selected_post_state = !$this->selected_post_state;
            $this->reset('update_current_category');
            $this->reset('update_current_tag');
            $this->reset('body_update');
            $this->reset('current_image');
            $this->dispatch('reset-body', data: "");
            return $this->reset('update_title');
        }
        // $this->selected_post_state = !$this->selected_post_state;
        foreach ($this->posts as $key => $post) :
            if ($post['id'] == $id) {
                $this->selected_post = $post;
                break;
            }
        endforeach;

        $this->update_title = $this->selected_post['title'];
        $this->body_update = $this->selected_post['content'];
        // $response = Http::get($this->api_address . 'media/post/' . $id);
        // dd($response->header('Content-Type'));
        $this->current_image = $this->selected_post['media'][0]['name'];
        $this->update_current_category = $this->selected_post['category']['id'];
        $this->reset('update_current_tag');
        foreach ($this->selected_post['tag'] as $tagid) :
            $this->update_current_tag[$tagid['id']] = $tagid;
        endforeach;
        $this->created_at = $this->selected_post['created_at'];
        $this->updated_at = $this->selected_post['updated_at'];
        $this->update_current_address = $this->selected_post['location'];
        // $this->selected_tag = $this->selected_post['tag'];
        $this->dispatch("selected", data: $this->body_update);
    }

    public function updatePost()
    {
        // dd(
        //     $this->selected_post_id,
        //     $this->update_title,
        //     $this->body_update,
        //     $this->current_image,
        //     $this->update_current_category,
        //     $this->update_current_tag,
        //     $this->update_current_address
        // );
        $selected_tag_filter = array_filter($this->update_current_tag, function ($var) {
                return $var != false;
        });
        // dd($selected_tag_filter);
        if (empty($this->update_current_tag)) {
            Validator::make(['update_current_tag' => $this->update_current_tag], ['update_current_tag' => 'required|array'], ['required' => 'Postingan wajib memiliki tag, jika tidak ada buat terlebih dahulu'])->validate();
        }

        if (empty($this->update_current_category)) {
            Validator::make(['update_current_category' => $this->update_current_category], ['update_current_category' => 'required|integer'], ['required' => 'Postingan wajib memiliki kategori, jika tidak ada buat terlebih dahulu'])->validate();
        }
        if (empty($this->addresses)) {
            $this->createLocationState = true;
            Validator::make(['update_current_address' => $this->update_current_address], ['update_current_address' => 'required|string'], ['required' => 'Postingan wajib memiliki alamat, jika tidak ada buat terlebih dahulu'])->validate();
        }
        // $this->validate();
        Validator::make(['update_selected_address' => $this->update_selected_address], ['update_selected_address' => $this->createLocationState ? 'nullable|integer' : 'required|integer'])->validate();

        // mencari id address pada property selected_address, setelah itu dibuat menjadi string
        if (!$this->createLocationState) {
            $selected_address = '';
            foreach ($this->addresses as $key => $address) :
                if ($address['id'] == $this->selected_address) {
                    $selected_address = $address['city'] . ', ' . $address['province'];
                }
            endforeach;
        }

        // memindahkan file dari temp ke images
        $images_filename = [];
        $pattern = '/src="http:\/\/127.0.0.1:8001\/temp\/([^"]+)"/';
        if (preg_match_all($pattern, $this->body, $matches)) {
            $images_filename = $matches;
            // dd($images_filename);
        }
        foreach ($images_filename as $key => $image_filename) :
            if ($key === 1) {
                foreach ($image_filename as $item) :
                    // $image_path = public_path('temp/' . $item);
                    File::move(public_path('temp/' . $item), public_path('assets/images/' . $item));
                endforeach;
            }
        endforeach;
        // ubah lokasi dari temp ke images
        $this->body = str_replace('/temp/', '/assets/images/', $this->body);

        $response = Http::withHeaders($this->headers)
            ->patch($this->api_address . 'post/' . $this->selected_post_id, [
                'category_id' => $this->update_current_category,
                'tag_id' => array_keys($selected_tag_filter),
                'title' => $this->update_title,
                'content' => $this->body_update,
                'location' => $this->createLocationState ? $this->update_current_address : $selected_address
            ]);

            // dd($response->body());
    
        if (!empty($this->update_image)) {
            // File image upload 
            $type = $this->update_image->extension();
            $filename = $this->update_image->hashName();
            File::move($this->update_image->getRealPath(), public_path('assets/images/' . $filename));
            $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
            $post_id = $response['data'][0]['id'];
            $path = public_path('assets/images' . $filename);
            $response = Http::post($this->api_address . 'media-link', [
                'post_id' => $post_id,
                'name' => $filename,
                'file_path' => $path,
                'type' => $type
            ]);
        }
        session()->flash('update_post_status', ['message' => 'Berhasil Memperbarui Postingan Dengan Judul ' . $this->update_title]);
        $this->redirect('posts');
    }

    public function searchPost()
    {
        $this->searchPostState = true;
        if(empty($this->searchTitle)) {
            return $this->getAllPosts();
        }
        $response = Http::get($this->api_address . 'post/search/' . $this->searchTitle);
        $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
        $this->posts = $response['data'];
    }

    public function setDefaultSearchPost()
    {
        $this->searchPostState = false;
        $this->reset('searchTitle');
        $this->getAllPosts();
    }

    // public function filterPostsByCategory()
    // {
    //     $this->searchPostState = false;
    //     $this->filterPostState = true;
    //     $response = Http::get($this->api_address . 'post/search/' . $this->searchTitle);
    //     $response = json_decode($response->body(), JSON_OBJECT_AS_ARRAY);
    //     $this->posts = $response['data'];
    // }

    public function render()
    {
        return view('livewire.admin-post');
    }
}
