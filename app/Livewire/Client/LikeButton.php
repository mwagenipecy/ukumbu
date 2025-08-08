<?php

namespace App\Livewire\Client;

use App\Models\Like;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LikeButton extends Component
{
    public $model;
    public $isLiked = false;
    public $likesCount = 0;

    public function mount($model)
    {
        $this->model = $model;
        $this->likesCount = $model->likes()->count();
        
        if (Auth::check()) {
            $this->isLiked = $model->likes()
                ->where('user_id', Auth::id())
                ->exists();
        }
    }

    public function toggleLike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $existingLike = $this->model->likes()
            ->where('user_id', Auth::id())
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $this->isLiked = false;
            $this->likesCount--;
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'likeable_type' => get_class($this->model),
                'likeable_id' => $this->model->id,
            ]);
            $this->isLiked = true;
            $this->likesCount++;
        }

        $this->dispatch('likeToggled');
    }

    public function render()
    {
        return view('livewire.client.like-button');
    }
}