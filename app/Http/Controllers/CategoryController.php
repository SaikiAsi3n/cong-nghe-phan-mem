<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

        // trả về thể loại
    public function index()
    {   
        return response()->json(Category::orderBy('id', 'desc')->get());
    }
    
        // thêm thể loại
    public function store(Request $request)
    {
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);
        return Category::create([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }
    
        // cập nhật
    public function update(Request $request)
    {   
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);
        return Category::where('id', $request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }

        // xóa
    public function destroy(Request $request)
    {
        // xóa ảnh
        $this->deleteFileFromServer($request->iconImage); 
         // xóa thông tin khác
        $this->validate($request, [
            'id' => 'required', 
        ]);
        return Category::where('id', $request->id)->delete();
    }
    
}
