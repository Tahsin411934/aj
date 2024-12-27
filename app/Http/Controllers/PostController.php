<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return response()->json(['posts' => $posts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        Post::create($request->all());

        return response()->json(['success' => 'পোস্ট সফলভাবে যুক্ত হয়েছে!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return response()->json(['success' => 'পোস্ট সফলভাবে আপডেট হয়েছে!']);
    }


    public function showPosts()
{
    return view('posts.index');
}


    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(['success' => 'পোস্ট সফলভাবে ডিলিট হয়েছে!']);
    }
}
