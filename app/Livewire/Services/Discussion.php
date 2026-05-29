<?php

namespace App\Livewire\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app', [
    'title' => 'Discussion Board',
    'description' => 'Browse and create discussion posts.',
    'keywords' => 'discussion, posts, community',
])]
class Discussion extends Component
{
    use WithPagination;

    public bool $showPostModal = false;
    public string $title = '';
    public string $content = '';

    protected array $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string|min:3|max:5000',
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
            'postsable_type' => $owner['type'],
        ]);

        $this->reset(['title', 'content']);
        $this->showPostModal = false;
        $this->resetPage();

        session()->flash('message', 'Post created successfully!');
    }

    public function openPostModal(): void
    {
        $this->showPostModal = true;
    }

    public function closePostModal(): void
    {
        $this->showPostModal = false;
        $this->reset(['title', 'content']);
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
