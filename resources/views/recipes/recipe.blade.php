@extends('layouts.app')

@section('title', $recipe->title)

@section('content')

    {{-- ── Recipe Hero ─────────────────────────────────── --}}
    <div class="recipe-hero">
        @if ($recipe->image_url)
            <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}" class="recipe-hero-img">
        @else
            <div class="recipe-hero-placeholder">🍽️</div>
        @endif
        <div class="recipe-hero-overlay"></div>

        <div class="recipe-hero-body">
            <div class="page">
                <nav class="recipe-breadcrumb">
                    <a href="{{ url('/recipes') }}">Recipes</a>
                    <span>›</span>
                    <a href="{{ url('/recipes?category=' . $recipe->category) }}">{{ ucfirst($recipe->category) }}</a>
                    <span>›</span>
                    <span>{{ $recipe->title }}</span>
                </nav>

                <span class="card-badge">{{ ucfirst($recipe->category) }}</span>
                <span class="card-badge">{{ $recipe->is_public ? 'Public' : 'Private' }}</span>
                @if ($recipe->forkedFrom)
                    <span class="card-badge">
                        🍴 Forked from
                        <a style="color: var(--cream)" href="{{ url("/recipe/{$recipe->forkedFrom->original->id}") }}">
                            {{ $recipe->forkedFrom->original->user->name }}
                        </a>
                    </span>
                @endif



                <h1 class="recipe-hero-title">{{ $recipe->title }}</h1>

                @if ($recipe->description)
                    <p class="recipe-hero-desc">{{ $recipe->description }}</p>
                @endif

                <div class="recipe-hero-meta">
                    <div class="recipe-meta-item">
                        <span class="meta-icon">⏱</span>
                        <div>
                            <div class="meta-label">Cook Time</div>
                            <div class="meta-value">{{ $recipe->cook_time }} min</div>
                        </div>
                    </div>
                    <div class="recipe-meta-divider"></div>
                    <div class="recipe-meta-item">
                        <span class="meta-icon">👤</span>
                        <div>
                            <div class="meta-label">Servings</div>
                            <div class="meta-value">{{ $recipe->serving }}</div>
                        </div>
                    </div>
                    <div class="recipe-meta-divider"></div>
                    <div class="recipe-meta-item">
                        <span class="meta-icon">⭐</span>
                        <div>
                            <div class="meta-label">Rating</div>
                            <div class="meta-value">
                                @if ($recipe->reviews->count())
                                    {{ number_format($recipe->reviews->avg('rating'), 1) }}
                                    <span style="font-size:0.75rem;opacity:0.7">({{ $recipe->reviews->count() }})</span>
                                @else
                                    No reviews
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="recipe-meta-divider"></div>
                    <div class="recipe-meta-item">
                        <span class="meta-icon" style="color: red">♥</span>
                        <div>
                            <div class="meta-label">Likes</div>
                            <div class="meta-value" id="like-count" data-count="{{ $recipe->usersLike()->count() }}">
                            </div>
                        </div>
                    </div>
                    <div class="recipe-meta-divider"></div>
                    <div class="recipe-meta-item">
                        <div class="avatar" style="width:32px;height:32px;font-size:0.7rem">
                            {{ strtoupper(substr($recipe->user->name ?? 'A', 0, 2)) }}
                        </div>
                        <div>
                            <div class="meta-label">By</div>
                            <div class="meta-value">{{ $recipe->user->name ?? 'Anonymous' }}</div>
                        </div>
                    </div>
                </div>
                @auth
                    @php $isLiked = auth()->user()->likedRecipes->contains($recipe); @endphp
                    <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                        <button onclick="toggleLike(event)" data-recipe-id="{{ $recipe->id }}"
                            style="display:inline-flex; align-items:center; gap:0.5rem;
                    font-family:'DM Sans',sans-serif; font-size:0.8rem;
                    font-weight:500; letter-spacing:0.08em; text-transform:uppercase;
                    padding:0.6rem 1.4rem; border-radius:2px; text-decoration:none;
                    background:rgba(255,255,255,0.1); color:var(--cream);
                    border:1px solid rgba(255,255,255,0.25);
                    backdrop-filter:blur(6px);
                    transition:all 0.2s;">
                            {{ !$isLiked ? '♥︎ Save Recipe' : 'Remove from Saved Recipes' }}
                        </button>
                        @if (auth()->id() === $recipe->user_id)
                            <a href="{{ url('/recipes/' . $recipe->id . '/edit') }}"
                                style="display:inline-flex; align-items:center; gap:0.5rem;
                    font-family:'DM Sans',sans-serif; font-size:0.8rem;
                    font-weight:500; letter-spacing:0.08em; text-transform:uppercase;
                    padding:0.6rem 1.4rem; border-radius:2px; text-decoration:none;
                    background:rgba(255,255,255,0.1); color:var(--cream);
                    border:1px solid rgba(255,255,255,0.25);
                    backdrop-filter:blur(6px);
                    transition:all 0.2s;">
                                ✏️ Edit Recipe
                            </a>
                            <form action="{{ url('/recipes/' . $recipe->id) }}" method="POST"
                                onsubmit="return confirm('Delete this recipe?')" style="margin:0">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    style="display:inline-flex; align-items:center; gap:0.5rem;
                        font-family:'DM Sans',sans-serif; font-size:0.8rem;
                        font-weight:500; letter-spacing:0.08em; text-transform:uppercase;
                        padding:0.6rem 1.4rem; border-radius:2px; cursor:pointer;
                        background:rgba(192,96,58,0.25); color:var(--terra-light);
                        border:1px solid rgba(192,96,58,0.4);
                        backdrop-filter:blur(6px);
                        transition:all 0.2s;">
                                    🗑 Delete
                                </button>
                            </form>
                        @else
                            <a href="{{ url('/recipes/' . $recipe->id . '/fork') }}"
                                style="display:inline-flex; align-items:center; gap:0.5rem;
                    font-family:'DM Sans',sans-serif; font-size:0.8rem;
                    font-weight:500; letter-spacing:0.08em; text-transform:uppercase;
                    padding:0.6rem 1.4rem; border-radius:2px; text-decoration:none;
                    background:rgba(255,255,255,0.1); color:var(--cream);
                    border:1px solid rgba(255,255,255,0.25);
                    backdrop-filter:blur(6px);
                    transition:all 0.2s;">
                                🍴Fork Recipe
                            </a>
                        @endif
                    </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- ── Main content ────────────────────────────────── --}}
    <div class="page">
        <div class="recipe-layout">

            {{-- ── LEFT: Ingredients + Tags ─────────────── --}}
            <aside class="recipe-sidebar">

                {{-- Ingredients --}}
                <div class="sidebar-card">
                    <h2 class="sidebar-title">Ingredients</h2>
                    <p class="sidebar-serves">For {{ $recipe->serving }} serving{{ $recipe->serving > 1 ? 's' : '' }}</p>

                    @if ($recipe->ingredients->count())
                        <ul class="ingredient-list">
                            @foreach ($recipe->ingredients as $ingredient)
                                <li class="ingredient-item">
                                    <span class="ingredient-name">{{ $ingredient->name }}</span>
                                    <span class="ingredient-qty">
                                        {{ $ingredient->quantity % 1 == 0
                                            ? (int) $ingredient->quantity
                                            : rtrim(rtrim(number_format($ingredient->quantity, 2), '0'), '.') }}
                                        {{ $ingredient->unit->value }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="sidebar-empty">No ingredients listed.</p>
                    @endif
                </div>

                {{-- Tags --}}
                @if ($recipe->tags->count())
                    <div class="sidebar-card">
                        <h2 class="sidebar-title">Tags</h2>
                        <div class="tag-list">
                            @foreach ($recipe->tags as $tag)
                                <a href="{{ url('/recipes?tag=' . $tag->name) }}" class="tag-pill">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Author card --}}
                <div class="sidebar-card sidebar-author">
                    <div class="author-avatar">
                        {{ strtoupper(substr($recipe->user->name ?? 'A', 0, 2)) }}
                    </div>
                    <div class="author-info">
                        <div class="author-label">Recipe by</div>
                        <div class="author-name">{{ $recipe->user->name ?? 'Anonymous' }}</div>
                    </div>
                    @auth
                        @if (auth()->id() === $recipe->user_id)
                            <a href="{{ url('/recipes/' . $recipe->id . '/edit') }}" class="btn-nav btn-outline"
                                style="font-size:0.72rem;padding:0.4rem 1rem">Edit</a>
                        @endif
                    @endauth
                </div>

            </aside>

            {{-- ── RIGHT: Steps + Reviews ───────────────── --}}
            <div class="recipe-main">

                {{-- Steps --}}
                <section class="recipe-section">
                    <div class="recipe-section-head">
                        <h2>Instructions</h2>
                        <span class="step-count">{{ $recipe->steps->count() }} steps</span>
                    </div>

                    @if ($recipe->steps->count())
                        <ol class="step-list">
                            @foreach ($recipe->steps->sortBy('order') as $step)
                                <li class="step-item">
                                    <div class="step-number">{{ $loop->iteration }}</div>
                                    <div class="step-body">
                                        <p class="step-text">{{ $step->instruction }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="recipe-empty">No instructions added yet.</p>
                    @endif
                </section>

                {{-- Reviews --}}
                <section class="recipe-section">
                    <div class="recipe-section-head">
                        <h2>Reviews</h2>
                        @if ($recipe->reviews->count())
                            <div class="review-summary">
                                <span class="review-avg">{{ number_format($recipe->reviews->avg('rating'), 1) }}</span>
                                <div class="review-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span
                                            class="{{ $i <= round($recipe->reviews->avg('rating')) ? 'star-filled' : 'star-empty' }}">★</span>
                                    @endfor
                                </div>
                                <span class="review-count">{{ $recipe->reviews->count() }}
                                    review{{ $recipe->reviews->count() !== 1 ? 's' : '' }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Write a review --}}
                    @auth
                        @if (!$recipe->reviews->where('user_id', auth()->id())->count())
                            <form action="{{ url("/recipes/{$recipe->id}/review") }}" method="POST" class="review-form">
                                @csrf
                                <div class="review-form-head">Leave a review</div>

                                <div class="rating-input">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" id="star{{ $i }}"
                                            value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                                        <label for="star{{ $i }}">★</label>
                                    @endfor
                                </div>

                                <textarea name="review" class="review-textarea" placeholder="Share your thoughts on this recipe…" rows="4">{{ old('review') }}</textarea>

                                @error('review')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror
                                @error('rating')
                                    <span class="field-error">{{ $message }}</span>
                                @enderror

                                <button type="submit" class="btn-primary btn-primary-terra">Post Review</button>
                            </form>
                        @endif
                    @else
                        <p class="review-cta">
                            <a href="{{ route('login') }}">Sign in</a> to leave a review.
                        </p>
                    @endauth

                    {{-- Review list --}}
                    @if ($recipe->reviews->count())
                        <div class="review-list">
                            @foreach ($recipe->reviews->sortByDesc('created_at') as $review)
                                <div class="review-item" id="review-{{ $review->id }}">

                                    {{-- ── Normal view ──────────────────── --}}
                                    <div class="review-view-{{ $review->id }}">
                                        <div class="review-header">
                                            <div class="review-author">
                                                <div class="avatar">
                                                    {{ strtoupper(substr($review->user->name ?? 'A', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="review-name">{{ $review->user->name ?? 'Anonymous' }}
                                                    </div>
                                                    <div class="review-date">
                                                        {{ \Carbon\Carbon::parse($review->created_at)->format('M j, Y') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="display:flex; align-items:center; gap:1rem;">
                                                <div class="review-rating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <span
                                                            class="{{ $i <= $review->rating ? 'star-filled' : 'star-empty' }}">★</span>
                                                    @endfor
                                                </div>

                                                {{-- Edit / Delete — only for the review author --}}
                                                @auth
                                                    @if (auth()->id() === $review->user_id)
                                                        <div style="display:flex; gap:0.5rem; flex-shrink:0;">
                                                            {{-- Edit button --}}
                                                            <button onclick="toggleReviewEdit({{ $review->id }})"
                                                                class="btn-review-action btn-review-edit"
                                                                title="Edit review">Edit</button>

                                                            {{-- Delete button --}}
                                                            <form
                                                                action="{{ url("/recipes/{$recipe->id}/review/{$review->id}") }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Delete this review?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn-review-action btn-review-delete"
                                                                    title="Delete review">Delete</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>

                                        <p class="review-text">{{ $review->review }}</p>
                                    </div>

                                    {{-- ── Inline edit form (hidden by default) ── --}}
                                    @auth
                                        @if (auth()->id() === $review->user_id)
                                            <div class="review-edit-form-{{ $review->id }}"
                                                style="display:none; margin-top:1rem;">
                                                <form action="{{ url("/recipes/{$recipe->id}/review/{$review->id}") }}"
                                                    method="POST" class="review-form" style="margin-bottom:0;">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="review-form-head" style="font-size:0.9rem;">Edit your review
                                                    </div>

                                                    {{-- Star rating --}}
                                                    <div class="rating-input">
                                                        @for ($i = 5; $i >= 1; $i--)
                                                            <input type="radio" name="rating"
                                                                id="edit-star-{{ $review->id }}-{{ $i }}"
                                                                value="{{ $i }}"
                                                                {{ $review->rating == $i ? 'checked' : '' }}>
                                                            <label
                                                                for="edit-star-{{ $review->id }}-{{ $i }}">★</label>
                                                        @endfor
                                                    </div>

                                                    <textarea name="review" class="review-textarea" rows="4">{{ $review->review }}</textarea>

                                                    <div style="display:flex; gap:0.75rem; margin-top:0.5rem;">
                                                        <button type="submit" class="btn-primary btn-primary-terra"
                                                            style="padding:0.6rem 1.5rem; font-size:0.78rem;">Save</button>
                                                        <button type="button"
                                                            onclick="toggleReviewEdit({{ $review->id }})"
                                                            class="btn-primary btn-primary-ghost"
                                                            style="padding:0.6rem 1.25rem; font-size:0.78rem;">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth

                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="padding:2rem 0">
                            <div class="empty-icon">💬</div>
                            <h3>No reviews yet</h3>
                            <p>Be the first to share your experience!</p>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function formatCount(count) {
                count = parseInt(count) || 0;
                if (count >= 1_000_000) {
                    return (count / 1_000_000).toFixed(1) + 'M';
                } else if (count >= 1_000) {
                    return (count / 1_000).toFixed(1) + 'K';
                } else {
                    return count;
                }
            }

            function toggleLike(event) {
                event.preventDefault();
                const btn = event.target;
                const recipeId = btn.dataset.recipeId;

                fetch(`/recipes/${recipeId}/toggle-like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        btn.innerHTML = !data.is_liked ? "♥︎ Save Recipe" : "Remove from Saved Recipes";
                        const countElement = document.getElementById('like-count');
                        const num = data.likes_count;
                        let formatted;

                        countElement.textContent = formatCount(num);
                        countElement.dataset.count = num;
                    })
                    .catch(err => console.error('Toggle failed', err));
            }

            document.addEventListener('DOMContentLoaded', function() {
                const el = document.getElementById("like-count");
                const count = el.dataset.count;
                el.textContent = formatCount(count);
            });

            function toggleReviewEdit(id) {
                const view = document.querySelector('.review-view-' + id);
                const form = document.querySelector('.review-edit-form-' + id);
                const isEditing = form.style.display !== 'none';

                view.style.display = isEditing ? '' : 'none';
                form.style.display = isEditing ? 'none' : 'block';
            }
        </script>
    @endpush
@endsection
