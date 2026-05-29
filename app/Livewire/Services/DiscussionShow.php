<?php

namespace App\Livewire\Services;

use App\Models\Comment;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', [
    'title' => 'Discussion',
    'description' => 'View post discussion, comments and replies.',
    'keywords' => 'discussion, post, comments, replies',
])]
class DiscussionShow extends Component
{
    public Post $post;
    public ?int $selectedComment = null;
    public string $newComment = '';
    public string $newReply = '';

    public function mount(Post $post): void
    {
        $this->post = Post::query()
            ->with([
                'comments' => fn($q) => $q->latest()->with('replies'),
            ])
            ->findOrFail($post->id);
    }

    public function addComment(): void
    {
        $this->validate([
            'newComment' => 'required|string|min:2|max:2000',
        ]);

        $this->post->comments()->create([
            'content' => trim($this->newComment),
        ]);

        $this->reset('newComment');
        $this->loadPost();

        session()->flash('message', 'Comment added successfully!');
    }

    public function selectComment(int $commentId): void
    {
        $this->selectedComment = ($this->selectedComment === $commentId) ? null : $commentId;
        $this->reset('newReply');
    }

    public function addReply(): void
    {
        $this->validate([
            'newReply' => 'required|string|min:1|max:2000',
        ]);

        if (! $this->selectedComment) {
            return;
        }

        $comment = Comment::query()
            ->whereKey($this->selectedComment)
            ->where('commentsable_id', $this->post->id)
            ->where('commentsable_type', Post::class)
            ->first();

        if (! $comment) {
            return;
        }

        $comment->replies()->create([
            'content' => trim($this->newReply),
        ]);

        $this->reset(['newReply', 'selectedComment']);
        $this->loadPost();

        session()->flash('message', 'Reply added successfully!');
    }

    private function loadPost(): void
    {
        $this->post = Post::query()
            ->with([
                'comments' => fn($q) => $q->latest()->with('replies'),
            ])
            ->findOrFail($this->post->id);
    }

    public function render()
    {
        return view('livewire.services.discussion-show');
    }
}
