<?php

namespace App\Livewire\Services;

use App\Models\Comment;
use App\Models\Post;
use Livewire\Component;
use Str;

class Discussion extends Component
{
    public $posts = [];
    public $selectedPost = '';
    public $newComment = '';
    public $newReply = '';
    public $selectedComment = '';
    public $title = '';
    public $content = '';

    public function mount()
    {
        $this->posts = Post::with(['comments.replies'])->latest()->get();
    }

    public function selectPost($postId)
    {
        $this->selectedPost = Post::with(['comments.replies'])->find($postId);
        $this->selectedComment = null;
    }

    public function addComment()
    {
        if ($this->selectedPost && $this->newComment) {
            $this->selectedPost->comments()->create([
                'body' => $this->newComment,
            ]);
            $this->newComment = '';
            $this->selectPost($this->selectedPost->id);
        }
    }

    public function selectComment($commentId)
    {
        $this->selectedComment = $commentId;
    }

    public function addReply()
    {
        if ($this->selectedComment && $this->newReply) {
            $comment = Comment::find($this->selectedComment);
            $comment->replies()->create([
                'body' => $this->newReply,
            ]);
            $this->newReply = '';
            $this->selectPost($this->selectedPost->id);
        }
    }

    protected $rules = [
        'title' => ['required', 'string', 'max:255'],
        'content' => ['required', 'string'],
    ];
    public function createPost()
    {
        $this->validate();

        Post::create([
            'title' => $this->title,
            'content' => $this->content,
            'slug' => Str::slug($this->title) . '-' . uniqid(),
            'postsable_id' => 1,
            'postsable_type' => 'App\Models\User',
        ]);
        $this->pull([
            'title',
            'content',
        ]);
        // flash message
        session()->flash('message', 'Post created successfully!');
    }

    public function render()
    {
        return view('livewire.services.discussion');
    }
}
