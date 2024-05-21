<?php

namespace App\Http\Controllers;

use App\Category;
use App\Providers\MembersHelperServiceProvider;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {

        $suggestions = MembersHelperServiceProvider::getSuggestedMembers();
        $categories = Category::withCount('categoryPost')->get();
        $alphabets = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y'];

        $categories = $categories->groupBy(function($item, $key){
            return $item->name[0];
        });

        return view('pages.categories', compact('suggestions', 'categories'));
    }
}
