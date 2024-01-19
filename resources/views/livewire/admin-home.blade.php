<div class="container-fluid dashboard-content position-relative z-2 ">
    <div class="d-flex flex-row justify-content-between align-items-center" style="width: 100%">
        @if(session('status'))
        <div class="alert alert-success mt-3" role="alert">
            {{session('status')}}
           </div>
        @endif
        <span class="admin-welcome mb-3">Selamat Datang Admin!</span>
    </div>
    <div class="row d-flex flex-column no-padding align-items-center justify-content-center">
        <div class="col-lg-12 no-padding border card">
            <div class="row no-padding d-flex justify-content-center align-items-center" style="height: 150px">
                <div class="col-lg-2 m-3 mini-card shadow-sm rounded-3 border" style="height: 100px; width: auto;">
                    <i class="icon fa-solid fa-file-signature mx-2"></i>
                    <div class="d-flex flex-column align-items-center justify-content-center flex-column mx-2">
                        <span>{{ $postsCount }}</span>
                        <span>Postingan</span>
                    </div>
                </div>
                <div class="col-lg-2 m-3 mini-card shadow-sm rounded-3 border" style="height: 100px; width: auto;">
                    <i class="icon fa-solid fa-list mx-2"></i>
                    <div class="d-flex flex-column align-items-center justify-content-center flex-column mx-2">
                       <span> {{ $categoriesCount }}</span>
                        <span>Kategori</span>
                    </div>
                </div>
                <div class="col-lg-2 m-3 mini-card shadow-sm rounded-3 border" style="height: 100px; width: auto;">
                    <i class="icon fa-solid fa-tags mx-2"></i>
                    <div class="d-flex flex-column align-items-center justify-content-center flex-column mx-2">
                        <span>{{ $tagsCount }}</span>
                        <span>Tag</span>
                    </div>
                </div>
                <div class="col-lg-2 m-3 mini-card shadow-sm rounded-3 border" style="height: 100px; width: auto;">
                    <i class="icon fa-solid fa-comments mx-2"></i>
                    <div class="d-flex flex-column align-items-center justify-content-center flex-column mx-2">
                        <span>{{ $commentsCount }}</span>
                        <span>Komentar</span>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
