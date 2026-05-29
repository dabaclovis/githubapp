<div @class(['container', 'py-4']) style="max-width: 860px;">

    @include('partials.messages.form')

    {{-- Back link --}}
    <a href="{{ url()->previous() }}" @class(['btn', 'btn-sm', 'btn-outline-secondary', 'mb-3'])>
        &larr; Back to Discussions
    </a>

    {{-- Post --}}
    <section @class(['card', 'rounded-3', 'border', 'shadow-sm', 'mb-4'])>
        <div @class(['card-header', 'rounded-top', 'text-white', 'py-3']) style="background: linear-gradient(120deg, #093028, #237a57);">
            <h4 @class(['mb-0'])>{{ Str::ucfirst($post->title) }}</h4>
            <small @class(['opacity-75'])>{{ $post->created_at->diffForHumans() }}</small>
        </div>
        <div @class(['card-body'])>
            <p @class(['mb-0']) style="line-height: 1.8;">{{ Str::ucfirst($post->content) }}</p>
        </div>
    </section>

    {{-- Conversation --}}
    <section @class(['card', 'rounded-3', 'border', 'shadow-sm'])>
        <div @class([
            'card-header',
            'bg-light',
            'border-bottom',
            'd-flex',
            'align-items-center',
            'justify-content-between',
        ])>
            <h5 @class(['mb-0'])>Conversation</h5>
            <span @class(['badge', 'bg-secondary', 'rounded-pill'])>
                {{ $post->comments->count() }} {{ Str::plural('comment', $post->comments->count()) }}
            </span>
        </div>
        <div @class(['card-body'])>
            {{-- Add comment --}}
            <form wire:submit.prevent="addComment" aria-label="Add Comment" @class(['mb-4'])>
                <label @class(['form-label', 'small', 'text-muted']) for="newCommentField">Add a comment</label>
                <div @class(['input-group'])>
                    <input id="newCommentField" type="text" wire:model.defer="newComment" @class(['form-control'])
                        placeholder="Write your comment..." aria-label="Comment">
                    <button @class(['btn', 'btn-primary']) type="submit">
                        <span wire:loading wire:target="addComment" @class(['spinner-border', 'spinner-border-sm', 'me-1'])></span>
                        Comment
                    </button>
                </div>
                @error('newComment')
                    <div @class(['text-danger', 'small', 'mt-1'])>{{ $message }}</div>
                @enderror
            </form>

            {{-- Comments list --}}
            @forelse($post->comments as $comment)
                <article wire:key="comment-{{ $comment->id }}" @class(['card', 'border', 'rounded-3', 'mb-3'])>
                    <div @class(['card-body', 'py-2', 'px-3'])>

                        @if ($editingCommentId === $comment->id)
                            <form wire:submit.prevent="updateComment" @class(['mb-2'])
                                aria-label="Edit Comment">
                                <div @class(['input-group'])>
                                    <input type="text" wire:model.defer="editCommentContent"
                                        @class(['form-control']) aria-label="Edit Comment">
                                    <button @class(['btn', 'btn-primary']) type="submit">Save</button>
                                    <button @class(['btn', 'btn-outline-secondary']) type="button"
                                        wire:click="cancelEditComment">Cancel</button>
                                </div>
                                @error('editCommentContent')
                                    <div @class(['text-danger', 'small', 'mt-1'])>{{ $message }}</div>
                                @enderror
                            </form>
                        @else
                            <div>
                                <p @class(['mb-2'])>{{ Str::ucfirst($comment->content) }}</p>
                                <div @class(['d-flex', 'align-items-center', 'gap-1'])>
                                    <button @class(['btn', 'btn-sm', 'btn-outline-success', 'py-0', 'px-2'])
                                        wire:click="selectComment({{ $comment->id }})" type="button">
                                        <small>{{ $selectedComment === $comment->id ? 'Cancel' : 'Reply' }}</small>
                                    </button>
                                    <button @class(['btn', 'btn-sm', 'btn-outline-primary', 'py-0', 'px-2'])
                                        wire:click="startEditComment({{ $comment->id }})" type="button">
                                        <small>Edit</small>
                                    </button>
                                    <button @class(['btn', 'btn-sm', 'btn-outline-danger', 'py-0', 'px-2'])
                                        wire:click="deleteComment({{ $comment->id }})" type="button">
                                        <small>Delete</small>
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Replies --}}
                        @if ($comment->replies && $comment->replies->count())
                            <div @class(['mt-2', 'ml-3', 'ps-3']) style="border-left: 3px solid #0d6efd;">
                                @foreach ($comment->replies as $reply)
                                    <div wire:key="reply-{{ $reply->id }}" @class(['rounded-2', 'px-3', 'py-2', 'mt-1', 'small'])
                                        style="background: #f2f6f8;">
                                        @if ($editingReplyId === $reply->id)
                                            <form wire:submit.prevent="updateReply" aria-label="Edit Reply">
                                                <div @class(['input-group'])>
                                                    <input type="text" wire:model.defer="editReplyContent"
                                                        @class(['form-control', 'form-control-sm']) aria-label="Edit Reply">
                                                    <button @class(['btn', 'btn-sm', 'btn-primary']) type="submit">Save</button>
                                                    <button @class(['btn', 'btn-sm', 'btn-outline-secondary']) type="button"
                                                        wire:click="cancelEditReply">Cancel</button>
                                                </div>
                                            </form>
                                            @error('editReplyContent')
                                                <div @class(['text-danger', 'small', 'mt-1'])>{{ $message }}</div>
                                            @enderror
                                        @else
                                            <div @class(['d-flex', 'justify-content-between', 'align-items-center'])>
                                                <span>{{ Str::ucfirst($reply->content) }}</span>
                                                <div>
                                                    <button @class([
                                                        'btn',
                                                        'btn-sm',
                                                        'btn-link',
                                                        'text-primary',
                                                        'py-0',
                                                        'px-1',
                                                        'mr-1',
                                                    ]) type="button"
                                                        wire:click="startEditReply({{ $reply->id }})">Edit</button>
                                                    <button @class([
                                                        'btn',
                                                        'btn-sm',
                                                        'btn-link',
                                                        'text-danger',
                                                        'py-0',
                                                        'px-1',
                                                        'mr-1',
                                                    ]) type="button"
                                                        wire:click="deleteReply({{ $reply->id }})">Delete</button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Reply form --}}
                        @if ($selectedComment === $comment->id)
                            <form wire:submit.prevent="addReply" @class(['mt-2']) aria-label="Add Reply">
                                <div @class(['input-group'])>
                                    <input type="text" wire:model.defer="newReply" @class(['form-control'])
                                        placeholder="Write a reply..." aria-label="Reply">
                                    <button @class(['btn', 'btn-success']) type="submit">
                                        <span wire:loading wire:target="addReply" @class(['spinner-border', 'spinner-border-sm', 'me-1'])></span>
                                        Send
                                    </button>
                                </div>
                                @error('newReply')
                                    <div @class(['text-danger', 'small', 'mt-1'])>{{ $message }}</div>
                                @enderror
                            </form>
                        @endif

                    </div>
                </article>
            @empty
                <div @class([
                    'alert',
                    'alert-light',
                    'border',
                    'rounded-3',
                    'text-center',
                    'text-muted',
                ])>
                    No comments yet. Be the first to join this thread.
                </div>
            @endforelse
        </div>
    </section>
</div>
