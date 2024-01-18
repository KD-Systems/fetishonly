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

        return view('pages.categories', compact('suggestions', 'categories'));
    }
}
