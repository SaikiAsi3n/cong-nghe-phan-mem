<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use App\Models\Post;
use App\Models\PostTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
        // lấy ra bài viết
    public function index(Request $request)
    {   
        if ($request->total){
            return response()->json(Post::with(['tag', 'cat','user'])->orderBy('id', 'desc')->paginate($request->total));
        } else {
            return response()->json(Post::with(['tag', 'cat','user'])->orderBy('id', 'desc')->get());
        }   
    }
        // tạo bài viết
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'postExcerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);

        $categories = $request->category_id;

        $tags = $request->tag_id;

        $postCategories = [];
        $postTags = [];
       
        try {
            $post = Post::create([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post, 
                'postExcerpt' => $request->postExcerpt,
                'user_id' => Auth::user()->id,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
                'featuredImage' => $request->featuredImage ?? null
            ]);

            // thêm thẻ
            foreach($categories as $c){
                DB::table('postcategories')->insert([
                    'category_id' => $c,
                    'post_id' => $post->id,
                ]);
            }
            foreach($tags as $t){
                DB::table('posttags')->insert([
                    'tag_id' => $t,
                    'post_id' => $post->id,
                ]);
            }
            
           
                  
                



            
 
            return 'Post Created';
        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function show($id)
    {
        return Post::with(['tag', 'cat'])->where('id', $id)->first();
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'postExcerpt' => 'required',
            'metaDescription' => 'required',
            'jsonData' => 'required',
            'category_id' => 'required',
            'tag_id' => 'required',
        ]);
        $categories = $request->category_id;
        $tags = $request->tag_id;
        $postCategories = [];
        $postTags = [];
        
        $post = Post::where('id', $id)->first();
        if($post->featuredImage && $post->featuredImage != $request->featuredImage){
            $this->deleteFileFromServer($post->featuredImage); 
        }

        try {
            Post::where('id', $id)->update([
                'title' => $request->title,
                'slug' => $request->title,
                'post' => $request->post,
                'postExcerpt' => $request->postExcerpt,
                'metaDescription' => $request->metaDescription,
                'jsonData' => $request->jsonData,
                'featuredImage' => $request->featuredImage ?? null
            ]);


            foreach ($categories as $c) {
                array_push($postCategories, ['category_id' => $c, 'post_id' => $id]);
            }

            // xóa và thêm
            PostCategory::where('post_id', $id)->delete();
            PostCategory::insert($postCategories);
            
            foreach ($tags as $t) {
                array_push($postTags, ['tag_id' => $t, 'post_id' => $id]);
            }
            Posttag::where('post_id', $id)->delete();
            Posttag::insert($postTags);

            return 'Post Updated';
        } catch (\Throwable $e) {
            return $e;
        }
    }


    public function destroy(Request $request)
    {   
        $post = Post::where('id', $request->id)->first();
        if($post->featuredImage){
            $this->deleteFileFromServer($post->featuredImage); 
        }
        return $post->delete();
    }


    public function uploadEditorImage(Request $request){
        
        $this->validate($request, [
            'image' => 'required|mimes:jpeg,jpg,png'
        ]);
        $picName = time().'.'.$request->image->extension();
        $request->image->move(public_path('uploads'),$picName );

        return response()->json([
            'success' => 1, 
            'file' => [
                'url' => env('APP_URL')."uploads/$picName"
            ]
        ]);
        

    }
}
