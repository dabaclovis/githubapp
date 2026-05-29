<?php

namespace App\Livewire\Services;

use App\Models\Comment;
use App\Models\Post;
use Livewire\Component;
use Str;

class Discussion extends Component
{
    public $posts = [];
    public $selectedPost = null;
    public $newComment = '';
    public $newReply = '';
    public $selectedComment = null;
    public $title = '';
    public $content = '';

    protected $rules = [
        'title' => ['required', 'string', 'max:255'],
        'content' => ['required', 'string'],
    ];

    public function mount()
    {
        $this->refreshPosts();
    }

    public function refreshPosts()
    {
        $this->posts = Post::with(['comments.replies'])->latest()->get();
    }

    public function selectPost($postId)
    {
        $this->selectedPost = Post::with(['comments.replies'])->find($postId);
        $this->selectedComment = null;
        $this->newComment = '';
        $this->newReply = '';
    }

    public function createPost()
    {
        $this->validate();
        $post = Post::create([
            'title' => $this->title,
            'content' => $this->content,
            'slug' => Str::slug($this->title) . '-' . uniqid(),
            'postsable_id' => 1, // Adjust as needed
            'postsable_type' => 'App\\Models\\User', // Adjust as needed
        ]);
        $this->title = '';
        $this->content = '';
        $this->refreshPosts();
        $this->selectPost($post->id);
        session()->flash('message', 'Post created successfully!');
    }

    public function addComment()
    {
        if ($this->selectedPost && $this->newComment) {
            $this->selectedPost->comments()->create([
                'body' => $this->newComment,
            ]);
            $this->newComment = '';
            $this->selectPost($this->selectedPost->id);
            session()->flash('message', 'Comment added successfully!');
        }
    }

    public function selectComment($commentId)
    {
        $this->selectedComment = $commentId;
        $this->newReply = '';
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
            session()->flash('message', 'Reply added successfully!');
        }
    }

    public function render()
    {
        return view('livewire.services.discussion');
    }
}
