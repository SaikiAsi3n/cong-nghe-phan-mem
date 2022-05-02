<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {   
        return view('index');
    }
        // lấy ra bài viết
    public function getPosts(Request $request)
    {   
        return response()->json(Post::with(['tag', 'cat','user'])->orderBy('id', 'desc')->paginate($request->total));
    }
        // lấy ra thể loại bài viết
    public function getCategories()
    {   
        return response()->json(Category::select('id', 'categoryName', 'iconImage')->get());
    }
        // chi tiết bài viết
    public function postSingle(Request $request, $slug){
        $post = Post::where('slug', $slug)->with(['cat','tag' , 'user'])->first(['id', 'title', 'post', 'user_id', 'featuredImage','created_at']);
        
        $category_ids = [];
        foreach ($post->cat as $cat) {
            array_push($category_ids, $cat->id);
        }

            // hàm xử lý
        $relatedPosts = Post::with('user')->where('id', '!=', $post->id)->whereHas('cat', function($q) use($category_ids){
            $q->whereIn('category_id',$category_ids);
        })->limit(5)->orderBy('id','desc')->get((['id', 'title', 'postExcerpt', 'slug', 'user_id', 'featuredImage']));
              
        return response()->json([ 'post' => $post, 'relatedPosts' => $relatedPosts]);
    }

        // chi tiết thể loại
    public function categoryIndex(Request $request, $categoryName, $id)
    {
        $posts = Post::with(['tag', 'cat','user'])->whereHas('cat', function($q) use($id){
            $q->where('category_id',$id); 
        })->orderBy('id','desc')->select(['id', 'title', 'postExcerpt', 'slug', 'user_id', 'featuredImage','created_at'])->paginate($request->total);
        return response()->json([ 'posts' => $posts, 'categoryName' => $categoryName]); 
    }

        // thẻ bài viết
    public function tagIndex(Request $request, $tagName, $id)
    {
       $posts = Post::with(['tag', 'cat','user'])->whereHas('tag', function($q) use($id){
            $q->where('tag_id',$id);
        })->orderBy('id','desc')->select(['id', 'title', 'postExcerpt', 'slug', 'user_id', 'featuredImage','created_at'])->paginate($request->total);
        return response()->json([ 'posts' => $posts, 'tagName' => $tagName]); 
    }

        // người dùng 
    public function userIndex(Request $request, $userName, $id)
    {
       $posts = Post::with(['tag', 'cat','user'])->whereHas('user', function($q) use($id){
            $q->where('user_id',$id);
        })->orderBy('id','desc')->select(['id', 'title', 'postExcerpt', 'slug', 'user_id', 'featuredImage','created_at'])->paginate($request->total);
        return response()->json([ 'posts' => $posts, 'userName' => $userName]); 
    }

    public function search(Request $request)
    {   
        $str = $request->str;
        $posts = Post::orderBy('id', 'desc')->with(['cat','tag', 'user'])->select('id', 'title', 'postExcerpt', 'slug', 'user_id', 'featuredImage','created_at'); 
  
        $posts->when($str!='', function($q) use($str){
            $q->where('title','LIKE',"%$str%")
            ->orWhereHas('cat', function($q) use($str){
                // kiểm tra với cate
                $q->where('categoryName','LIKE',"%$str%");
            })
            ->orWhereHas('tag', function($q) use($str){
                // kiểm tra với tags
                $q->where('tagName','LIKE',"%$str%");
            });
        });

            // phân trang
        $posts = $posts->paginate($request->total); 
        $posts = $posts->appends($request->all()) ; 
   
        return response()->json(['posts' => $posts]);
    }
}
