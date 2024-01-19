<?php

namespace App\Livewire;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\Attributes\On;

class AdminHome extends Component
{
    public $token;
    public $posts;
    public $categories;
    public $tags;
    public $comments;
    public $postsCount;
    public $categoriesCount;
    public $tagsCount;
    public $commentsCount;
    public function mount()
    {
        $this->token = Cookie::get('token');
        $this->getAllData();
    }

    #[On('admin-current-page')]
    public function setCurrentPage($data)
    {
        Cookie::queue('current-page', (integer)$data);
        if($data == 1) {
            return redirect('admin');
        }
        if($data == 2) {
            return redirect('admin/posts');
        }
        if($data == 3) {
            return redirect('admin/settings');
        }
        if($data == 4) {
            return redirect('admin/about');
        }
    }

    public function getAllData()
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];
        $responses = Http::pool(fn (Pool $pool) => [
            $pool->withHeaders($headers)->get('http://127.0.0.1:8000/api/post'),
            $pool->withHeaders($headers)->get('http://127.0.0.1:8000/api/category'),
            $pool->withHeaders($headers)->get('http://127.0.0.1:8000/api/tag'),
            $pool->withHeaders($headers)->get('http://127.0.0.1:8000/api/comment')
        ]);
        $responses[0] = json_decode($responses[0]->body(), JSON_OBJECT_AS_ARRAY);
        $responses[1] = json_decode($responses[1]->body(), JSON_OBJECT_AS_ARRAY);
        $responses[2] = json_decode($responses[2]->body(), JSON_OBJECT_AS_ARRAY);
        $responses[3] = json_decode($responses[3]->body(), JSON_OBJECT_AS_ARRAY);
        $this->posts = $responses[0]['data'] ?? [];
        $this->categories = $responses[1]['data'] ?? [];
        $this->tags = $responses[2]['data'] ?? [];
        $this->comments = $responses[3]['data'] ?? [];

        $this->postsCount = count($responses[0]['data']);
        $this->categoriesCount = count($responses[1]['data']);
        $this->tagsCount = count($responses[2]['data']);
        $this->commentsCount = count($responses[3]['data']);
    }
    public function render()
    {
        return view('livewire.admin-home');
    }
}
