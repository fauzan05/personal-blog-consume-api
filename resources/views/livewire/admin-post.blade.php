<div class="container-fluid dashboard-content position-relative z-2 ">
    <div class="d-flex flex-row justify-content-between align-items-center mb-4" style="width: 100%">
        @if (session('status'))
            <div class="alert alert-success mt-3" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <span class="admin-welcome mb-3">Menu Postingan &nbsp <i class="fa-solid fa-file"></i></span>
        @if (session('create_post_status'))
            <div class="alert alert-success" role="alert" style="auto">
                {{ session('create_post_status') }}
            </div>
        @endif
        @if (session('update_post_status'))
            <div class="alert alert-success" role="alert" style="auto">
                {{ session('update_post_status')['message'] }}
            </div>
        @endif
        <div class="d-flex flex-row gap-3">
            <button wire:click="configMode()" class="btn btn-primary">
                {{ $configState ? 'Buat Postingan' : 'Konfigurasi Postingan' }}
                &nbsp
                <div wire:loading wire:target="configMode()" class="spinner-border spinner-border-sm" role="status">
                </div>
            </button>
            <button wire:click="showPostsMode()" class="btn btn-success">
                {{!$showPostsModeState ? 'Lihat Semua Postingan' : 'Kembali'}}
                &nbsp
                <div wire:loading wire:target="showPostsMode()" class="spinner-border spinner-border-sm" role="status">
                </div>
            </button>
        </div>
    </div>
    @if (!$showPostsModeState)
        <div class="row d-flex flex-row no-padding align-items-start justify-content-center">
            <div class="col-lg-8 d-flex card flex-column justify-content-center align-items-center me-3">
                <span class="create-post my-3">{{ $configState ? 'Edit Postingan' : 'Buat Postingan' }}</span>
                <hr class="no-padding" style="width: 80%">
                <div style="width: 80% !important;">
                    <form class="my-4" wire:submit="{{ $configState ? 'updatePost' : 'post' }}" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input type="text" wire:model="{{ $configState ? 'update_title' : 'title' }}"
                                class="form-control" id="title" placeholder="Masukkan Judul">
                            <div id="inputHelp" class="form-text">Panjang max. 100 karakter</div>
                            @error('title')
                                <span class="error text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Gambar Utama</label>
                            <input class="form-control" wire:model="{{ $configState ? 'update_image' : 'image' }}"
                                type="file" id="formFile1">
                            <div id="inputHelp" class="form-text">Ukuran min. 20Kb, Max. 50Mb. Format gambar.</div>
                            @if ($configState)
                                <div id="inputHelp" class="form-text">Gambar terkini : {{ $update_image ?? ' -' }}</div>
                            @endif
                            @error('image')
                                <span class="error text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @if ($configState)
                            <div class="d-flex flex-column my-3">
                                <span>Pratinjau</span>
                                <img class="my-3 rounded" src="{{ asset('assets/images/' . $current_image) }}"
                                    alt="Gambar Pratinjau" style="width: 150px;">
                            </div>
                        @endif
                        <div wire:ignore style="width: 100% !important;">
                            <label for="body-update" class="form-label">Bodi</label>
                            <textarea class="form-control" id="body" wire:model.defer="{{ $configState ? 'body_update' : 'body' }}"
                                style="width: 100% !important;"></textarea>
                        </div>
                        {{-- {{!empty($configState) ? var_dump($selected_post) : ''}} --}}
                        @error('body')
                            <span class="error text-danger">{{ $message }}</span>
                        @enderror
                        @if ($configState)
                            <div class="d-flex flex-column my-3">
                                <span class="created-at">Dibuat : {{ !empty($created_at) ? $created_at : ' -' }}</span>
                                <span class="updated-at">Diperbarui :
                                    {{ !empty($updated_at) ? $updated_at : ' -' }}</span>
                            </div>
                        @endif
                        <button type="submit" wire:click="loadingCreatePostState()" class="btn btn-primary my-4"
                            style="width: 100%">
                            <div wire:loading wire:target="loadingCreatePostState()"
                                class="spinner-border spinner-border-sm" role="status">
                            </div>
                            {{ $configState ? 'Edit Postingan' : 'Buat Postingan' }}
                        </button>
                        @if ($configState)
                            <button type="button" wire:click="deletePost({{ $current_post_id }})"
                                class="btn btn-danger my-1" style="width: 100%">
                                <div wire:loading wire:target="deletePost()" class="spinner-border spinner-border-sm"
                                    role="status">
                                </div>
                                Hapus Postingan
                            </button>
                        @endif
                </div>
            </div>
            <div class="col-lg-3" style="height: auto;">
                <div class="row d-flex flex-column">
                    @if ($configState)
                        <div
                            class="col-lg-12 container card d-flex flex-column justify-content-center align-items-center mb-5">
                            <span class="create-post text-center my-3">Daftar Postingan</span>
                            <div class="my-2 d-flex gap-2 flex-row" style="width: 80%">
                                <input class="form-control form-control-sm" type="text" wire:model.live="searchTitle"
                                    wire:keydown.enter="searchPost()" wire:keydown.escape="setDefaultSearchPost()"
                                    placeholder="Cari judul postingan..." aria-label=".form-control-sm example">
                                <button type="button" wire:click="searchPost()"
                                    class="btn btn-primary btn-sm">Cari</button>
                            </div>
                            <hr class="no-padding" style="width: 80%">
                            <div
                                class="list-posts-collection d-flex flex-column justify-content-start align-items-center">
                                @if (!empty($posts))
                                    @foreach ($posts as $post)
                                        <div wire:click="selectedPost({{ $post['id'] }})"
                                            class="list-group my-3 gap-2 list-post" style="width: 80%">
                                            <div class="list-group-item shadow-sm list-group-item-action {{ $selected_post_id === $post['id'] && $selected_post_state ? 'active' : '' }}"
                                                aria-current="true" style="transition: var(--tran-04)">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h5 class="mb-1">{{ $post['title'] }}</h5>
                                                </div>
                                                <hr>
                                                <small>{{ $post['created_at'] }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                @elseif(empty($posts))
                                    <div id="inputHelp" class="form-text">Postingan kosong/tidak ditemukan</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    {{-- Tag --}}
                    <div class="col-lg-12 mb-5 d-flex flex-column justify-content-center align-items-center card"
                        style="width: 100%;">
                        <span class="choose-tags my-2">Pilih Tag</span>
                        <hr class="no-padding" style="width: 80%">

                        @if (session('update_tag_status'))
                            <div class="d-flex flex-column mt-3 text-center justify-content-center align-items-center"
                                style="width: 80%">
                                <div class="alert alert-success gap-3 d-flex flex-column" role="alert">
                                    {{ session('update_tag_status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        <div class="tags-collection m-3 d-flex flex-column justify-content-start align-items-start">
                            @if (empty($tags))
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <div id="inputHelp" class="form-text">Tags kosong/belum dibuat</div>
                                    @if ($configState)
                                        @error('update_current_tag')
                                            <span class="error text-danger text-center">{{ $message }}</span>
                                        @enderror
                                    @endif
                                    @error('selected_tag')
                                        <span class="error text-danger text-center">{{ $message }}</span>
                                    @enderror
                                    @error('update_current_tag')
                                        <span class="error text-danger text-center">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif(!empty($tags))
                                {{-- Jika tag tidak kosong --}}
                                @foreach ($tags as $key => $tag)
                                    <div class="d-flex flex-row justify-content-between gap-3 align-items-center my-1"
                                        style="width: 100%">
                                        <div class="form-check">
                                            @if (!$optionTagState)
                                                @if (!$selected_post_state)
                                                    {{-- Jika tidak ada pembungkus, maka error karena selected_tag properti yang bersifat array --}}
                                                    <div>
                                                        <input class="form-check-input"
                                                            wire:model="selected_tag.{{ $tag['id'] }}"
                                                            type="checkbox" id="tag{{ $tag['id'] }}"
                                                            style="transition: var(--tran-05)">
                                                        <label class="form-check-label" for="tag{{ $tag['id'] }}">
                                                            {{ $tag['name'] }}
                                                    </div>
                                                @elseif($selected_post_state)
                                                    <div>
                                                        @php
                                                            $tagPrinted = false;
                                                        @endphp
                                                        @foreach ($update_current_tag as $key2 => $current_tag)
                                                            @if (is_array($current_tag) && (int) $tag['id'] == (int) $current_tag['id'])
                                                                <div class="d-flex flex-row justify-content-between gap-3 align-items-center my-1"
                                                                    style="width: 100%">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input"
                                                                            wire:model="update_current_tag.{{ $tag['id'] }}"
                                                                            type="checkbox"
                                                                            id="flexCheckDefault{{ $tag['id'] }}"
                                                                            style="transition: var(--tran-05)" checked>
                                                                        <label class="form-check-label"
                                                                            for="flexCheckDefault{{ $tag['id'] }}">
                                                                            {{ $current_tag['name'] }}
                                                                    </div>
                                                                </div>
                                                                @php
                                                                    $tagPrinted = true;
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                        @if (!$tagPrinted)
                                                            <input class="form-check-input"
                                                                wire:model="update_current_tag.{{ $tag['id'] }}"
                                                                type="checkbox" id="tag{{ $tag['id'] }}"
                                                                style="transition: var(--tran-05)">
                                                            <label class="form-check-label"
                                                                for="tag{{ $tag['id'] }}">
                                                                {{ $tag['name'] }}
                                                        @endif
                                                    </div>
                                                @endif
                                            @elseif($optionTagState)
                                                <div class="d-flex flex-row option justify-content-between">
                                                    <input wire:model="update_tag.{{ $tag['id'] }}"
                                                        class="form-control form-control-sm" type="text"
                                                        placeholder="{{ $tag['name'] }}"
                                                        aria-label=".form-control-sm example">
                                                    <div
                                                        class="d-flex flex-row ms-2 gap-2 justify-content-center align-items-center">
                                                        {{-- {{var_dump($tag['post'])}} --}}
                                                        @if (!$tag['post'])
                                                            <button type="button"
                                                                wire:click="deleteTag('{{ $tag['id'] }}')"
                                                                class="btn btn-danger delete-tag btn-sm d-flex flex-row">
                                                                <i class="d-flex fa-solid fa-delete-left"></i>
                                                                &nbsp
                                                                <div wire:loading
                                                                    wire:target="deleteTag('{{ $tag['id'] }}')"
                                                                    class="spinner-border spinner-border-sm"
                                                                    role="status">
                                                                </div>
                                                            </button>
                                                        @endif
                                                        <button id="save-tag" type="button"
                                                            wire:click="editTag('{{ $tag['name'] }}')"
                                                            class="btn btn-success save-tag btn-sm d-flex flex-row">
                                                            <i class="fa-solid fa-check"></i>
                                                            &nbsp
                                                            <div wire:loading
                                                                wire:target="editTag('{{ $tag['name'] }}')"
                                                                class="spinner-border spinner-border-sm"
                                                                role="status">
                                                            </div>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                @error('selected_tag')
                                    <span class="error text-danger text-center">{{ $message }}</span>
                                @enderror
                                @error('update_current_tag')
                                    <span class="error text-danger text-center">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>

                        <div class="d-flex flex-row gap-3 my-3">
                            <button type="button" wire:click="setCreateTagState()"
                                class="btn btn-outline-{{ (bool) $darkModeState === true ? 'light' : 'dark' }}">
                                <div wire:loading wire:target="setCreateTagState()"
                                    class="spinner-border spinner-border-sm" role="status">
                                </div>
                                @if (!$createTagState)
                                    Buat Tag &nbsp <i class='fa-solid fa-pen'></i>
                                @elseif($createTagState)
                                    Tutup
                                @endif
                            </button>
                            <button id="option-tag-button" type="button" wire:click="optionTag()"
                                class="btn btn-outline-danger">
                                <div wire:loading wire:target="optionTag()" class="spinner-border spinner-border-sm"
                                    role="status" id="liveAlertBtn">
                                </div>
                                @if (!$optionTagState)
                                    Edit Tag &nbsp<i class="fa-solid fa-pen-to-square"></i>
                                @elseif($optionTagState)
                                    Tutup
                                @endif
                            </button>
                        </div>
                        <div id="inputHelp" class="form-text my-3" style="width: 80%">Catatan: <br> Tag yang sudah
                            dipakai di post lain tidak bisa dihapus. Nama Tag tidak boleh sama.</div>
                        @if ($createTagState)
                            </form>
                        @endif
                        @if (session('create_tag_status'))
                            <div class="alert alert-success mt-3 text-center" role="alert" style="width: 80%">
                                {{ session('create_tag_status') }}
                            </div>
                        @endif
                        @if ($createTagState)
                            <div class="d-flex flex-column align-items-center justify-content-center my-4"
                                style="width: 80%">
                                <form wire:submit="createTag">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1"><i
                                                    class="fa-solid fa-tag"></i></span>
                                            <input type="text" wire:model="tag" class="form-control"
                                                placeholder="Tag" aria-describedby="basic-addon1">
                                            @error('tag')
                                                <span
                                                    class="error text-danger text-center my-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button type="submit" wire:click="loadingCreateTagState()"
                                            class="btn btn-outline-primary">
                                            <div wire:loading wire:target="loadingCreateTagState()"
                                                class="spinner-border spinner-border-sm" role="status">
                                            </div>
                                            Buat Tag
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>

                    {{-- CATEGORIES --}}
                    <div class="col-lg-12 d-flex mb-5 flex-column justify-content-center align-items-center card"
                        style="width: 100%">
                        <span class="choose-categories my-2">Pilih Kategori</span>
                        <hr class="no-padding" style="width: 80%">
                        @if (session('update_category_status'))
                            <div class="d-flex flex-column mt-3 text-center justify-content-center align-items-center"
                                style="width: 80%">
                                <div class="alert alert-success gap-3 d-flex flex-column" role="alert">
                                    {{ session('update_category_status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        <div
                            class="categories-collection m-3 d-flex flex-column justify-content-start align-items-start">
                            @if (empty($categories))
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <div id="inputHelp" class="form-text">Kategori kosong/belum dibuat</div>
                                    @error('selected_category')
                                        <span class="error text-danger text-center">{{ $message }}</span>
                                    @enderror
                                    @error('update_current_category')
                                        <span class="error text-danger text-center">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif(!empty($categories))
                                @foreach ($categories as $key => $category)
                                    <div class="d-flex flex-row justify-content-between gap-3 align-items-center my-1"
                                        style="width: 100%">
                                        <div class="form-check">
                                            {{-- cek apakah opsi optionCategoryState true/false --}}
                                            @if (!$optionCategoryState)
                                                @if (!$selected_post_state)
                                                    <input class="form-check-input" wire:model="selected_category"
                                                        type="radio" name="exampleRadios"
                                                        id="category{{ $key + 1 }}"
                                                        value="{{ $category['id'] }}">
                                                    <label class="form-check-label"
                                                        for="category{{ $key + 1 }}">
                                                        {{ $category['name'] }}
                                                    </label>
                                                @elseif($selected_post_state)
                                                    <input class="form-check-input"
                                                        wire:model="update_current_category" type="radio"
                                                        name="exampleRadios" id="category{{ $key + 1 }}"
                                                        value="{{ $category['id'] }}">
                                                    <label class="form-check-label"
                                                        for="category{{ $key + 1 }}">
                                                        {{ $category['name'] }}
                                                    </label>
                                                @endif
                                                {{-- Edit kategori --}}
                                            @elseif($optionCategoryState)
                                                <div class="d-flex flex-row option justify-content-between">
                                                    <input wire:model="update_category.{{ $category['id'] }}"
                                                        class="form-control form-control-sm" type="text"
                                                        placeholder="{{ $category['name'] }}"
                                                        aria-label=".form-control-sm example">
                                                    <div
                                                        class="d-flex flex-row ms-2 justify-content-center gap-2 align-items-center">
                                                        {{-- {{var_dump($tag['post'])}} --}}
                                                        @if (!$category['post'])
                                                            <button type="button"
                                                                wire:click="deleteCategory('{{ $category['id'] }}')"
                                                                class="btn btn-danger delete-tag btn-sm d-flex flex-row">
                                                                <i class="d-flex fa-solid fa-delete-left"></i>
                                                                &nbsp
                                                                <div wire:loading
                                                                    wire:target="deleteCategory('{{ $category['id'] }}')"
                                                                    class="spinner-border spinner-border-sm"
                                                                    role="status">
                                                                </div>
                                                            </button>
                                                        @endif
                                                        <button type="button"
                                                            wire:click="editCategory('{{ $category['name'] }}')"
                                                            class="btn btn-success save-tag btn-sm d-flex flex-row">
                                                            <i class="fa-solid fa-check"></i>
                                                            &nbsp
                                                            <div wire:loading
                                                                wire:target="editCategory('{{ $category['name'] }}')"
                                                                class="spinner-border spinner-border-sm"
                                                                role="status">
                                                            </div>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                @error('selected_category')
                                    <span class="error text-danger text-center">{{ $message }}</span>
                                @enderror
                                @error('update_current_category')
                                    <span class="error text-danger text-center">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>
                        @if ($createCategoryState)
                            </form>
                        @endif
                        <div class="d-flex flex-row gap-3 my-3">
                            <button type="button" wire:click="setCreateCategoryState()"
                                class="btn my-3 btn-outline-{{ (bool) $darkModeState === true ? 'light' : 'dark' }}">
                                <div wire:loading wire:target="setCreateCategoryState()"
                                    class="spinner-border spinner-border-sm" role="status">
                                </div>
                                @if (!$createCategoryState)
                                    Buat Kategori &nbsp <i class='fa-solid fa-pen'></i>
                                @elseif($createCategoryState)
                                    Tutup
                                @endif
                            </button>
                            <button type="button" wire:click="optionCategory()" class="btn my-3 btn-outline-danger">
                                <div wire:loading wire:target="optionCategory()"
                                    class="spinner-border spinner-border-sm" role="status" id="liveAlertBtn">
                                </div>
                                @if (!$optionCategoryState)
                                    Edit Kategori &nbsp<i class="fa-solid fa-pen-to-square"></i>
                                @elseif($optionCategoryState)
                                    Tutup
                                @endif
                            </button>
                        </div>
                        <div id="inputHelp" class="form-text my-3" style="width: 80%">Catatan: <br> Kategori yang
                            sudah
                            dipakai di post lain tidak bisa dihapus. Nama Kategori tidak boleh sama.</div>
                        @if (session('create_category_status'))
                            <div class="alert alert-success mt-3 text-center" role="alert" style="width: 80%">
                                {{ session('create_category_status') }}
                            </div>
                        @endif
                        @if ($createCategoryState)
                            <div class="d-flex flex-column align-items-center justify-content-center my-4"
                                style="width: 80%">
                                <form wire:submit="createCategory">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1"><i
                                                    class="fa-solid fa-list"></i></span>
                                            <input type="text" wire:model="category" class="form-control"
                                                placeholder="Kategori" aria-describedby="basic-addon1">
                                            @error('category')
                                                <span
                                                    class="error text-danger text-center my-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button type="submit" wire:click="loadingCreateCategoryState()"
                                            class="btn btn-outline-primary">
                                            <div wire:loading wire:target="loadingCreateCategoryState()"
                                                class="spinner-border spinner-border-sm" role="status">
                                            </div>
                                            Buat Kategori
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-12 d-flex mb-5 flex-column justify-content-center align-items-center card">
                        <span class="choose-address my-2">Pilih Lokasi</span>
                        <hr class="no-padding" style="width: 80%">
                        @if ($selected_post_state)
                            <div id="inputHelp" class="form-text" style="width: 80%">Lokasi Sekarang : <br>
                                {{ $update_current_address }}</div>
                        @endif
                        <div class="address-collection m-3 d-flex flex-column justify-content-start align-items-start">
                            @if (empty($addresses))
                                <div id="inputHelp" class="form-text text-center">Alamat kosong/belum dibuat. Buat
                                    lokasi
                                    di pengaturan atau kustom yang instan.</div>
                                @error('selected_address')
                                    <span class="error text-danger text-center">{{ $message }}</span>
                                @enderror
                                @error('update_selected_address')
                                    <span class="error text-danger text-center">{{ $message }}</span>
                                @enderror
                            @elseif(!empty($addresses))
                                @foreach ($addresses as $key => $address)
                                    <div class="form-check my-1">
                                        <input class="form-check-input" wire:model="selected_address" type="radio"
                                            name="addressRadios" id="address{{ $key + 1 }}"
                                            value="{{ $address['id'] }}">
                                        <label class="form-check-label" for="address{{ $key + 1 }}">
                                            {{ $address['province'] }}, {{ $address['country'] }}
                                        </label>
                                    </div>
                                @endforeach
                                @error('selected_address')
                                    <span class="error text-danger text-center">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>
                        @if ($createLocationState)
                            </form>
                        @endif
                        <button type="button" wire:click="createAddress()"
                            class="btn my-3 btn-outline-{{ (bool) $darkModeState === true ? 'light' : 'dark' }}">
                            <div wire:loading wire:target="createAddress()" class="spinner-border spinner-border-sm"
                                role="status">
                            </div>
                            @if (!$createLocationState)
                                Buat Lokasi Kustom &nbsp <i class='fa-solid fa-pen'></i>
                            @elseif($createLocationState)
                                Tutup
                            @endif
                        </button>
                        @if ($createLocationState)
                            {{-- <livewire:mini-create-address> --}}
                            <div class="d-flex flex-column align-items-center justify-content-center my-4">
                                <div class="mb-3" style="width: 80%">
                                    <label for="input1" class="form-label">Masukkan Lokasi</label>
                                    <input type="text"
                                        wire:model="{{ $this->selected_post_state ? 'update_current_address' : 'custom_address' }}"
                                        class="form-control" id="input1">
                                    <div id="inputHelp" class="form-text">Masukkan nama lokasi secara kustom</div>
                                    @error('custom_address')
                                        <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                    @error('update_current_address')
                                        <span class="error text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" wire:click="resetFormCustomAddress()"
                                    class="btn btn-outline-danger">
                                    <div wire:loading wire:target="resetFormCustomAddress()"
                                        class="spinner-border spinner-border-sm" role="status">
                                    </div>
                                    Reset
                                </button>
                                <hr style="width: 80%">
                                <div id="inputHelp" class="form-text mt-3" style="width: 80%">Catatan: <br> Jika form
                                    kustom lokasi masih terbuka, maka yang akan dimasukkan adalah data yang ada di form
                                    kustom lokasi, bukan yang berada di opsi lokasi</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @if (!$createLocationState && !$createCategoryState && !$createTagState)
                </form>
            @endif
        </div>
    @elseif($showPostsModeState)
        <div class="row d-flex flex-row no-padding align-items-start justify-content-center">
            <div class="col-lg-12 d-flex flex-row justify-content-around">
                <div class="input-group mb-3" style="width: 30%">
                    <input type="text" class="form-control" wire:model="searchTitle" wire:keydown.enter="searchPost()" wire:keydown.escape="setDefaultSearchPost()"  placeholder="Cari judul postingan..."
                         aria-describedby="button-addon2">
                    <button class="btn btn-outline-primary" wire:click="searchPost()" type="button" id="button-addon2">
                        <div wire:loading wire:target="searchPost()" class="spinner-border spinner-border-sm" role="status">
                        </div>
                        &nbsp Cari</button>
                </div>
                {{-- <div class="dropdown-center">
                    <label for="title" class="form-label">Filter berdasarkan &nbsp</label>
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-solid fa-list"></i>
                        &nbsp Kategori
                    </button>
                    <ul class="dropdown-menu">
                        @foreach($categories as $key => $category)
                        <li><a class="dropdown-item" wire:model="selectedSortByCategory.{{ $category['id'] }}" wire:click="filterPostsByCategory({{$category['id']}})" href="#">{{ $category['name'] }}</a></li>
                        @endforeach
                    </ul>
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-solid fa-tag"></i>
                        &nbsp Tag
                    </button>
                    <ul class="dropdown-menu">
                        @foreach($tags as $key => $tag)
                        <li><a class="dropdown-item" wire:model="selectedSortByTag.{{ $tag['id'] }}" href="#">{{ $tag['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div> --}}
            </div>
                <hr>
                <div class="col-lg-12 d-flex flex-row justify-content-center align-items-center my-3">   
                    <h4>{{ $searchPostState ? "Menampilkan hasil pencarian '$searchTitle'" : 'Menampilkan semua postingan' }}</h4>
                </div>
                <div class="col-lg-12">
                    <div class="row d-flex flex-row justify-content-center align-items-start">
                        @if(!empty($posts))
                        @foreach($posts as $key => $post)
                        <div class="col-lg-4 col-md-4 col-sm-6 my-2">
                            <div class="card-shadow post" style="height: 200px">
                            <div class="row d-flex flex-column justify-content-center align-items-center">
                                <img src="{{asset('assets/images/' . $post['media'][0]['name'])}}" alt="" style="height: 100px; object-fit:contain">
                                <hr style="width: 90%">
                                <h3 class="text-center">{{ $post['title'] }}</h3>
                            </div>
                            </div>
                        </div>
                        @endforeach
                        @elseif(empty($posts))
                        <div class="col-lg-12">
                            <div id="inputHelp" class="form-text text-center">Postingan kosong/tidak ditemukan</div>
                        </div>
                        @endif
                    </div>
                </div>
        </div>
    @endif
</div>
@script
    <script>
        ClassicEditor
            .create(document.querySelector('#body'), {
                ckfinder: {
                    uploadUrl: "{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}"
                }
            })
            .then(editor => {
                MyEditor = editor;
                editor.model.document.on('change:data', () => {
                    // let body = document.getElementById('body').getAttribute('data-body')
                    // eval(body).set('body', document.getElementById('body').value)
                    // console.log(editor.getData())
                    let body_content = editor.getData()
                    Livewire.dispatch('body', {
                        data: body_content
                    })
                    Livewire.dispatch('body-updated', {
                        data: body_content
                    })
                });
                // editor.setData(contentBody)
            })
            .catch(error => {
                console.error(error);
            });
        Livewire.on("selected", (data) => {
            console.log("berhasil");
            MyEditor.setData(data.data);
        })
        Livewire.on("reset-body", (data) => {
            MyEditor.setData(data.data);
        })
    </script>
@endscript
