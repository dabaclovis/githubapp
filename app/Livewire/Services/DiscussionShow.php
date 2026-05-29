<?php

namespace App\Livewire\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.users', [
    'title' => 'Discussion',
    'description' => 'View post discussion, comments and replies.',
    'keywords' => 'discussion, post, comments, replies',
])]
class DiscussionShow extends Component
{
    public Post $post;
    public ?int $selectedComment = null;
    public ?int $editingCommentId = null;
    public ?int $editingReplyId = null;
    public string $newComment = '';
    public string $newReply = '';
    public string $editCommentContent = '';
    public string $editReplyContent = '';

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

    public function startEditComment(int $commentId): void
    {
        $comment = Comment::query()
            ->whereKey($commentId)
            ->where('commentsable_id', $this->post->id)
            ->where('commentsable_type', Post::class)
            ->first();

        if (! $comment) {
            return;
        }

        $this->editingCommentId = $comment->id;
        $this->editCommentContent = $comment->content;
    }

    public function cancelEditComment(): void
    {
        $this->reset(['editingCommentId', 'editCommentContent']);
    }

    public function updateComment(): void
    {
        $this->validate([
            'editCommentContent' => 'required|string|min:2|max:2000',
        ]);

        if (! $this->editingCommentId) {
            return;
        }

        $comment = Comment::query()
            ->whereKey($this->editingCommentId)
            ->where('commentsable_id', $this->post->id)
            ->where('commentsable_type', Post::class)
            ->first();

        if (! $comment) {
            return;
        }

        $comment->update([
            'content' => trim($this->editCommentContent),
        ]);

        $this->cancelEditComment();
        $this->loadPost();

        session()->flash('message', 'Comment updated successfully!');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::query()
            ->whereKey($commentId)
            ->where('commentsable_id', $this->post->id)
            ->where('commentsable_type', Post::class)
            ->first();

        if (! $comment) {
            return;
        }

        $comment->replies()->delete();
        $comment->delete();

        if ($this->selectedComment === $commentId) {
            $this->reset(['selectedComment', 'newReply']);
        }

        if ($this->editingCommentId === $commentId) {
            $this->cancelEditComment();
        }

        $this->loadPost();
        session()->flash('message', 'Comment deleted successfully!');
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

    public function startEditReply(int $replyId): void
    {
        $reply = Reply::query()
            ->whereKey($replyId)
            ->whereHasMorph('repliesable', [Comment::class], function ($query) {
                $query->where('commentsable_id', $this->post->id)
                    ->where('commentsable_type', Post::class);
            })
            ->first();

        if (! $reply) {
            return;
        }

        $this->editingReplyId = $reply->id;
        $this->editReplyContent = $reply->content;
    }

    public function cancelEditReply(): void
    {
        $this->reset(['editingReplyId', 'editReplyContent']);
    }

    public function updateReply(): void
    {
        $this->validate([
            'editReplyContent' => 'required|string|min:1|max:2000',
        ]);

        if (! $this->editingReplyId) {
            return;
        }

        $reply = Reply::query()
            ->whereKey($this->editingReplyId)
            ->whereHasMorph('repliesable', [Comment::class], function ($query) {
                $query->where('commentsable_id', $this->post->id)
                    ->where('commentsable_type', Post::class);
            })
            ->first();

        if (! $reply) {
            return;
        }

        $reply->update([
            'content' => trim($this->editReplyContent),
        ]);

        $this->cancelEditReply();
        $this->loadPost();

        session()->flash('message', 'Reply updated successfully!');
    }

    public function deleteReply(int $replyId): void
    {
        $reply = Reply::query()
            ->whereKey($replyId)
            ->whereHasMorph('repliesable', [Comment::class], function ($query) {
                $query->where('commentsable_id', $this->post->id)
                    ->where('commentsable_type', Post::class);
            })
            ->first();

        if (! $reply) {
            return;
        }

        $reply->delete();

        if ($this->editingReplyId === $replyId) {
            $this->cancelEditReply();
        }

        $this->loadPost();
        session()->flash('message', 'Reply deleted successfully!');
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
