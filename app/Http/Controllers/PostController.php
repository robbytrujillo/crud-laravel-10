<?php

namespace App\Http\Controllers;

// import Model "Post"
use App\Models\Post;

// import type view
use Illuminate\View\view;

// return type redirectResponse (20052023)
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): View {
    // get post
    $posts = Post::latest()->paginate(5);
    
    // render view with posts
    return view('posts.index', compact('posts'));
    }

    // tambahkan fungsi create (20052023)
    public function create(): View {
        return view('posts.create');
    }

    // tambahkan fungsi store (20052023)
    public function store(Request $request): RedirectResponse {
        // insert post validate form (20052023)
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        // upload image (20052023)
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName()); 

        // create post (20052023)
        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        // redirect to index (20052023)
        return redirect()->route('posts.index')->with(['success' => 'Data berhasil disimpan!']);
    }
}
    
