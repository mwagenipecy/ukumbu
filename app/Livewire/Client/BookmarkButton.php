<?php

namespace App\Livewire\Client;

use App\Models\Bookmark;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BookmarkButton extends Component
{
    public $model;
    public $isBookmarked = false;

    public function mount($model)
    {
        $this->model = $model;
        
        if (Auth::check()) {
            $this->isBookmarked = $model->bookmarks()
                ->where('user_id', Auth::id())
                ->exists();
        }
    }

    public function toggleBookmark()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $existingBookmark = $this->model->bookmarks()
            ->where('user_id', Auth::id())
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            $this->isBookmarked = false;
        } else {
            Bookmark::create([
                'user_id' => Auth::id(),
                'bookmarkable_type' => get_class($this->model),
                'bookmarkable_id' => $this->model->id,
            ]);
            $this->isBookmarked = true;
        }

        $this->dispatch('bookmarkToggled');
    }

    public function render()
    {
        return view('livewire.client.bookmark-button');
    }
}