<?php

namespace App\Livewire\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.users', [
    'title' => 'Discussion Board',
    'description' => 'Browse and create discussion posts.',
    'keywords' => 'discussion, posts, community',
])]
class Discussion extends Component
{
    use WithPagination;

    public bool $showPostModal = false;
    public bool $isEditingPost = false;
    public ?int $editingPostId = null;
    public string $title = '';
    public string $content = '';

    protected array $rules = [
        'title' => ['required', 'string', 'max:255'],
        'content' => ['required', 'string', 'min:3', 'max:5000'],
    ];

    public function createPost(): void
    {
        $validated = $this->validate();
        $owner = $this->resolvePostOwner();

        Post::create([
            'title' => Str::ucfirst(trim($validated['title'])),
            'content' => Str::ucfirst(trim($validated['content'])),
            'slug' => Str::slug($validated['title']) . '-' . Str::lower(Str::random(8)),
            'postsable_id' => $owner['id'],
            'postsable_type' => $owner['type'], //User::class,
        ]);

        $this->reset(['title', 'content']);
        $this->showPostModal = false;
        $this->resetPage();

        session()->flash('message', 'Post created successfully!');
    }

    public function openEditPostModal(int $postId): void
    {
        $post = Post::query()->findOrFail($postId);
        $this->ensureCanManagePost($post);

        $this->editingPostId = $post->id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->isEditingPost = true;
        $this->showPostModal = true;
    }

    public function updatePost(): void
    {
        if (! $this->editingPostId) {
            return;
        }

        $post = Post::query()->findOrFail($this->editingPostId);
        $this->ensureCanManagePost($post);

        $validated = $this->validate();

        $post->update([
            'title' => Str::ucfirst(trim($validated['title'])),
            'content' => Str::ucfirst(trim($validated['content'])),
            'slug' => Str::slug($validated['title']) . '-' . Str::lower(Str::random(8)),
        ]);

        $this->closePostModal();
        $this->resetPage();

        session()->flash('message', 'Post updated successfully!');
    }

    public function deletePost(int $postId): void
    {
        $post = Post::query()->findOrFail($postId);
        $this->ensureCanManagePost($post);

        $post->delete();

        $this->resetPage();
        session()->flash('message', 'Post deleted successfully!');
    }

    public function openPostModal(): void
    {
        $this->isEditingPost = false;
        $this->editingPostId = null;
        $this->showPostModal = true;
    }

    public function closePostModal(): void
    {
        $this->showPostModal = false;
        $this->isEditingPost = false;
        $this->editingPostId = null;
        $this->reset(['title', 'content']);
    }

    private function ensureCanManagePost(Post $post): void
    {
        $user = auth()->user();

        if (! $user) {
            throw ValidationException::withMessages([
                'title' => 'You are not authorized to manage this post.',
            ]);
        }

        if ($user->role === 'admin') {
            return;
        }

        $isOwner = $post->postsable_type === $user::class && (int) $post->postsable_id === (int) $user->getKey();

        if (! $isOwner) {
            throw ValidationException::withMessages([
                'title' => 'You are not authorized to manage this post.',
            ]);
        }
    }

    private function resolvePostOwner(): array
    {
        $authenticatedUser = auth()->user();

        if ($authenticatedUser) {
            return [
                'id' => $authenticatedUser->getKey(),
                'type' => $authenticatedUser::class,
            ];
        }

        $fallbackUserId = User::query()->value('id') ?? 1;

        return [
            'id' => $fallbackUserId,
            'type' => User::class,
        ];
    }

    public function render()
    {
        $posts = Post::query()
            ->select(['id', 'title', 'content', 'slug', 'created_at'])
            ->withCount('comments')
            ->latest()
            ->paginate(9);

        return view('livewire.services.discussion', compact('posts'));
    }
}
