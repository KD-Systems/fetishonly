<?php

namespace App\Http\Controllers\Manager;

use App\CreatorMedia;
use App\Http\Controllers\Controller;
use App\Model\Attachment;
use App\Model\Post;
use App\PostTag;
use App\UserPostCategory;
use Illuminate\Http\Request;

class ManagePostController extends Controller
{
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request) {
        $request->validate([
            'user' => 'required'
        ]);

        if(!$request->has('files')) {
            return back();
        }

        $medias = [];

        foreach($request->get('files') as $file) {
            $medias[] = [
                'user_id' => $request->user,
                'attachment_id' => $file,
                'creator_id' => auth()->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        CreatorMedia::insert($medias);

        return redirect()->route('manager.medias');
    }


    public function medias() {
        $medias = CreatorMedia::with('attachment', 'user')->paginate();
        return view('manager.medias', compact('medias'));
    }

    public function assign($id) {
        $media = CreatorMedia::with('attachment', 'user')->firstOrFail();
        return view('manager.view', compact('media'));
    }


    public function update(Request $request, $id) {
        $creatorMedia = CreatorMedia::findOrFail($id);

        $request->validate([
            'text' =>'required'
        ]);

        $post = Post::create([
            'text' => $request->text,
            'user_id' => $creatorMedia->user_id,
            'status' => 1
        ]);

        $attachment = Attachment::where('id', $creatorMedia->attachment_id)->first();

        $attachment->update([
            'post_id' => $post->id,
            'user_id' => $creatorMedia->user_id
        ]);

        $post_tags = [];

        foreach($request->get('creators') as $creator) {
            $post_tags[] = [
                'post_id' => $post->id,
                'user_id' => $creator,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PostTag::insert($post_tags);


        $post_categories = [];

        foreach($request->get('categories') as $category) {
            $post_categories[] = [
                'user_id' => $creatorMedia->user_id,
                'post_id' => $post->id,
                'category_id' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }


        UserPostCategory::insert($post_categories);


        $creatorMedia->delete();

        return redirect()->route('manager.medias');

    }

}
