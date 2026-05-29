<div class="container-xl py-3" style="max-width: 1220px;">
    <header @class(['rounded-3', 'p-3', 'mb-3', 'text-white']) style="background: linear-gradient(120deg, #093028, #237a57);">
        <div @class([
            'd-flex',
            'align-items-center',
            'justify-content-between',
            'flex-wrap',
        ])>
            <div>
                <h3 @class(['mb-0'])>Discussion Board</h3>
                <p @class(['mb-0']) style="opacity: 0.75;">Share updates, gather feedback, and keep the
                    conversation alive.</p>
            </div>
            <button @class(['btn', 'btn-light', 'btn-sm', 'font-weight-bold']) type="button" wire:click="openPostModal">
                + New Post
            </button>
        </div>
    </header>

    @include('partials.messages.form')

    @if ($showPostModal)
        <div @class(['modal', 'd-block']) tabindex="-1" style="background: rgba(0,0,0,0.4);"
            wire:click.self="closePostModal" @keydown.escape.window="$wire.closePostModal()">
            <div @class(['modal-dialog', 'modal-lg', 'modal-dialog-centered'])>
                <div @class(['modal-content', 'rounded-3', 'shadow'])>

                    <div @class(['modal-header', 'bg-light', 'border-bottom'])>
                        <h5 @class(['modal-title'])>Create New Post</h5>
                        <button type="button" @class(['btn-close', 'btn-danger']) wire:click="closePostModal"
                            aria-label="Close">&times;
                        </button>
                    </div>

                    <form wire:submit.prevent="createPost" aria-label="Create Post">
                        <div @class(['modal-body'])>
                            <div @class(['mb-3'])>
                                <label @class(['form-label', 'small', 'text-muted']) for="postTitle">Title</label>
                                <input id="postTitle" type="text" wire:model.defer="title"
                                    @class(['form-control', 'is-invalid' => $errors->has('title')]) placeholder="Write a concise title" required>
                                @error('title')
                                    <div @class(['invalid-feedback'])>{{ $message }}</div>
                                @enderror
                            </div>
                            <div @class(['mb-3'])>
                                <label @class(['form-label', 'small', 'text-muted']) for="postBody">Message</label>
                                <textarea id="postBody" wire:model.defer="content" @class(['form-control', 'is-invalid' => $errors->has('content')])
                                    placeholder="What do you want to discuss?" rows="5" required></textarea>
                                @error('content')
                                    <div @class(['invalid-feedback'])>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div @class(['modal-footer', 'border-top-0', 'pt-0'])>
                            <button @class(['btn', 'btn-secondary']) type="button"
                                wire:click="closePostModal">Cancel</button>
                            <button @class(['btn', 'btn-success']) type="submit" wire:loading.attr="disabled"
                                wire:target="createPost">
                                <span wire:loading wire:target="createPost" @class(['spinner-border', 'spinner-border-sm', 'mr-1'])></span>
                                Publish Post
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    <div @class(['row', 'g-2'])>
        @forelse($posts as $post)
            <div @class(['col-12', 'mb-1']) wire:key="post-{{ $post->id }}">
                <div @class(['card', 'border', 'shadow-sm'])>
                    <div @class(['card-body', 'py-2', 'px-3', 'd-flex', 'flex-column'])>
                        <h6 @class(['card-title', 'mb-1'])>{{ Str::ucfirst($post->title) }}</h6>
                        <p @class(['card-text', 'text-muted', 'small', 'mb-2']) style="font-size: 0.9rem;">
                            {{ Str::ucfirst(Str::limit($post->content, 300)) }}
                        </p>
                        <div @class(['d-flex', 'align-items-center', 'justify-content-between'])>
                            <span @class(['badge', 'badge-secondary'])>
                                {{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}
                            </span>
                            <div @class(['d-flex', 'align-items-center'])>
                                <small @class(['text-muted', 'mr-3'])>{{ $post->created_at->diffForHumans() }}</small>
                                <a href="{{ route('discussion.show', $post->slug) }}" @class(['btn', 'btn-sm', 'btn-outline-success', 'py-0'])>
                                    Read more &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div @class(['col-12'])>
                <div @class(['card', 'border', 'rounded', 'text-center', 'py-4'])>
                    <div @class(['card-body', 'py-2'])>
                        <h6 @class(['text-muted'])>No posts yet.</h6>
                        <p @class(['text-muted', 'mb-0', 'small'])>Be the first to start a discussion!</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($posts->hasPages())
        <div @class(['mt-4', 'd-flex', 'justify-content-center'])>
            {{ $posts->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
