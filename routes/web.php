<?php

use App\Livewire\AdminPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login-admin', function () {
    return view('users.login');
});
Route::middleware('user.login')->group(function () {
    Route::get('/admin', function () {
        return view('dashboards.admin.home');
    });
    Route::get('/admin/posts', function () {
        return view('dashboards.admin.post');
    });
    Route::get('/admin/settings', function () {
        return view('dashboards.admin.setting');
    });
    Route::get('/admin/abouts', function () {
        return view('dashboards.admin.about');
    });
    Route::get('/logout', function () {
        Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . Cookie::get('token'),
        ])->delete(config('services.api_address') . 'logout');
        $cookie1 = Cookie::forget('token');
        $cookie2 = Cookie::forget('current-user');
        return redirect('login-admin')
            ->with('message', 'Berhasil Keluar')
            ->withCookies([$cookie1, $cookie2]);
    });
});

// Route::post('image-upload', function(Request $request) {
//     if ($request->hasFile('upload')) {
//         $originName = $request->file('upload')->getClientOriginalName();
//         $fileName = pathinfo($originName, PATHINFO_FILENAME);
//         $extension = $request->file('upload')->getClientOriginalExtension();
//         $fileName = $fileName . '_' . time() . '.' . $extension;

//         $request->file('upload')->move(public_path('media'), $fileName);

//         $url = asset('media/' . $fileName);
//         return response()->json(['fileName' => $fileName, 'uploaded'=> 1, 'url' => $url]);
//     }
// })->name('ckeditor.upload');
Route::post('image-upload', function (Request $request) {
    if ($request->hasFile('upload')) {
        $originName = $request->file('upload')->hashName();
        $fileName = pathinfo($originName, PATHINFO_FILENAME);
        $extension = $request->file('upload')->extension();
        $fileName = $fileName . '.' . $extension;
        $request->file('upload')->move(public_path('temp'), $fileName);
        $url = asset('temp/' . $fileName);
        return response()->json(['fileName' => $fileName, 'uploaded' => 1, 'url' => $url]);
    }
})->name('ckeditor.upload');
// Route::post('/ckeditor/upload', [AdminPost::class, 'upload'])->name('ckeditor.upload');
