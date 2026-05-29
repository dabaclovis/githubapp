<div class="container py-4">
    <h2 class="mb-4">Discussion Board</h2>

    @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <h4>Posts</h4>
            <form wire:submit.prevent="createPost" class="mb-3" aria-label="Create Post">
                <div class="mb-2">
                    <input type="text" wire:model.defer="title" class="form-control" placeholder="Post title" required
                        aria-label="Post title">
                </div>
                <div class="mb-2">
                    <textarea wire:model.defer="content" class="form-control" placeholder="Post body" rows="2" required
                        aria-label="Post body"></textarea>
                </div>
                <button class="btn btn-primary w-100" type="submit">Create Post</button>
            </form>
            <ul class="list-group mb-3">
                @forelse($posts as $post)
                <li class="list-group-item @if($selectedPost && $selectedPost->id === $post->id) active @endif"
                    wire:click="selectPost({{ $post->id }})" style="cursor:pointer;">
                    {{ $post->title ?? 'Untitled Post' }}
                </li>
                @empty
                <li class="list-group-item text-muted">No posts yet.</li>
                @endforelse
            </ul>
        </div>
        <div class="col-md-8">
            @if($selectedPost)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $selectedPost->title ?? 'Untitled Post' }}</h5>
                    <p class="card-text">{{ $selectedPost->content ?? '' }}</p>
                </div>
            </div>
            <h5>Comments</h5>
            <ul class="list-group mb-3">
                @forelse($selectedPost->comments as $comment)
                <li class="list-group-item">
                    <div>
                        {{ $comment->body }}
                        <button class="btn btn-sm btn-link"
                            wire:click="selectComment({{ $comment->id }})">Reply</button>
                    </div>
                    @if($selectedComment === $comment->id)
                    <form wire:submit.prevent="addReply" class="mt-2" aria-label="Add Reply">
                        <div class="input-group">
                            <input type="text" wire:model.defer="newReply" class="form-control"
                                placeholder="Write a reply..." aria-label="Reply">
                            <button class="btn btn-primary" type="submit">Reply</button>
                        </div>
                    </form>
                    @endif
                    @if($comment->replies && $comment->replies->count())
                    <ul class="list-group mt-2">
                        @foreach($comment->replies as $reply)
                        <li class="list-group-item small">{{ $reply->body }}</li>
                        @endforeach
                    </ul>
                    @endif
                </li>
                @empty
                <li class="list-group-item text-muted">No comments yet.</li>
                @endforelse
            </ul>
            <form wire:submit.prevent="addComment" aria-label="Add Comment">
                <div class="input-group mb-3">
                    <input type="text" wire:model.defer="newComment" class="form-control" placeholder="Add a comment..."
                        aria-label="Comment">
                    <button class="btn btn-success" type="submit">Comment</button>
                </div>
            </form>
            @else
            <div class="alert alert-info">Select a post to view comments and participate in the discussion.</div>
            @endif
        </div>
    </div>
</div>