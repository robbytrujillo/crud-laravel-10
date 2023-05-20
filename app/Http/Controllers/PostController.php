<?php

namespace App\Http\Controllers;

// import Model "Post"
use App\Models\Post;

// import type view
use Illuminate\View\view;

// return type redirectResponse (20052023)
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

// import Facade "Storage" (20052023)
use Illuminate\Support\Facades\Storage;


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

    // membuat fuction show (20052023)
    public function show(string $id): View {
        
        // get post by ID (20052023)
        $post = Post::findOrFail($id);

        // render view with post (20052023)
        return view('posts.show', compact('post'));
}

// membuat fuction ubah dan hapus (20052023)
public function edit(string $id): View {
    // get post by ID (20052023)
    $post = Post::findOrFail($id);

    // render view with post
    return view ('posts.edit', compact('post'));
}

// membuat fuction update (20052023)
public function update(Request $request, $id): RedirectResponse {
    
    $this->validate($request, [
        'image' => 'image|mimes:jpeg,jpg,png|max:2048',
        'title' => 'required|min:5',
        'content' => 'required|min:10'
    ]);

    // get post by ID (20052023)
    $post = Post::findOrFail($id);

    // cek if image is upload (20052023)
    if ($request->hasFile('image')) {

        // upload new image (20052023)
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // delete old image (20052023)
        Storage::delete('public/posts/' . $post->image);

        // update post with new image
        $post->update([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

    } else {

        // update post without image (20052023)
        $post->update([
            'title' => $request->title,
            'content' => $request->content
        ]);
    }

    // redirect to index (20052023)
    return redirect()->route('posts.index')->with(['success' => 'Data Berhasil diubah']);

}
    
}