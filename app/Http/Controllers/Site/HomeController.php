<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Meta;

class HomeController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.home');
        // Meta::set('title', 'Medição Digital - A medição em suas mãos!');
        // Meta::set('description', 'Medição Digital - A medição em suas mãos!');
        // Meta::set('robots', 'index,follow');
        // Meta::set('image', asset('img/share.png'));
        // Meta::set('canonical', env('APP_URL'));

        // // $posts = Post::where('status', 'Publicado')
        // //     ->orderBy('created_at', 'desc')->limit(3)->get();
        // // return view('site.home.index', compact('posts'));
        // return view('site.home.index');
    }
}
