@extends('layouts.app')

@section('title', 'Browse Recipes')

@section('content')

    {{-- ── Hero ─────────────────────────────────────────── --}}
    <section class="hero">
        <div class="hero-eyebrow">Discover & Share</div>
        <h1 class="hero-title">
            Food made with<br><em>love & intention</em>
        </h1>
        <p class="hero-sub">
            Thousands of home-cooked recipes from real people.
            Find your next favourite meal.
        </p>

        <form action="{{ url('/recipes') }}" method="GET" class="search-bar">
            @if (request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <input type="text" name="q" placeholder="Search recipes, ingredients, cuisines…"
                value="{{ request('q') }}" autocomplete="off">
            <button type="submit">Search</button>
        </form>
    </section>

    {{-- ── Category strip ───────────────────────────────── --}}
    <div class="category-strip">
        <div class="category-inner">
            @php
                $cats = [
                    'all' => ['emoji' => '🍽️', 'label' => 'All'],
                    'breakfast' => ['emoji' => '🍳', 'label' => 'Breakfast'],
                    'lunch' => ['emoji' => '🥗', 'label' => 'Lunch'],
                    'dinner' => ['emoji' => '🍲', 'label' => 'Dinner'],
                    'snack' => ['emoji' => '🫐', 'label' => 'Snacks'],
                    'dessert' => ['emoji' => '🍰', 'label' => 'Dessert'],
                    'drink' => ['emoji' => '🧃', 'label' => 'Drinks'],
                ];
                $currentCat = request('category', 'all');
            @endphp

            @foreach ($cats as $slug => $cat)
                <a href="{{ url('/recipes') }}?category={{ $slug === 'all' ? '' : $slug }}"
                    class="cat-btn {{ $currentCat === $slug || ($slug === 'all' && !request('category')) ? 'active' : '' }}">
                    <span class="cat-emoji">{{ $cat['emoji'] }}</span>
                    {{ $cat['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="page">

        {{-- ── Featured Section ──────────────────────────── --}}
        @if (isset($featured) && $featured->count())
            <div class="section-head">
                <h2>Editor's <em>Picks</em></h2>
                <a href="{{ url('/recipes') }}">View all →</a>
            </div>

            <div class="featured-grid">
                {{-- Big feature card --}}
                @php $f = $featured->first() @endphp
                <a href="{{ url('/recipes/' . $f->id) }}" class="card-featured">
                    @if ($f->image_url)
                        <img src="{{ $f->image_url }}" alt="{{ $f->title }}" loading="lazy">
                    @else
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=900&q=80"
                            alt="{{ $f->title }}" loading="lazy">
                    @endif
                    <div class="card-featured-body">
                        <span class="card-badge">{{ ucfirst($f->category) }}</span>
                        <h3 class="card-featured-title">{{ $f->title }}</h3>
                        <div class="card-meta">
                            <span>⏱ {{ $f->cook_time }} min</span>
                            <span>👤 Serves {{ $f->serving }}</span>
                        </div>
                    </div>
                </a>

                {{-- Side cards --}}
                @foreach ($featured->skip(1)->take(2) as $sf)
                    <a href="{{ url('/recipes/' . $sf->id) }}" class="card-side">
                        @if ($sf->image_url)
                            <img src="{{ $sf->image_url }}" alt="{{ $sf->title }}" loading="lazy">
                        @else
                            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=600&q=80"
                                alt="{{ $sf->title }}" loading="lazy">
                        @endif
                        <div class="card-side-body">
                            <span class="card-badge">{{ ucfirst($sf->category) }}</span>
                            <h3 class="card-side-title">{{ $sf->title }}</h3>
                            <div class="card-meta">
                                <span>⏱ {{ $sf->cook_time }} min</span>
                                <span>👤 {{ $sf->serving }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- ── All / Filtered Recipes ─────────────────────── --}}
        <div class="section-head">
        <pre>{{ request()->path() }}</pre>
            <h2>
                @if (request('q'))
                    Results for <em>"{{ request('q') }}"</em>
                @elseif(request('category'))
                    {{ ucfirst(request('category')) }} <em>Recipes</em>
                @else
                    All <em>Recipes</em>
                @endif
            </h2>
            @auth
                <a href="{{ url('/recipes/create') }}">+ Submit yours →</a>
            @endauth
        </div>

        @if (isset($recipes) && $recipes->count())
            <div class="recipe-grid">
                @foreach ($recipes as $recipe)
                    <a href="{{ url('/recipe/' . $recipe->id) }}" class="recipe-card">
                        <div class="recipe-card-img">
                            @if ($recipe->image_url)
                                <img src="{{ $recipe->image_url }}" alt="{{ $recipe->title }}" loading="lazy">
                            @else
                                <div class="recipe-card-img-placeholder">
                                    {{ $cats[$recipe->category]['emoji'] ?? '🍽️' }}
                                </div>
                            @endif
                            <span class="recipe-card-cat">{{ ucfirst($recipe->category) }}</span>
                            @auth
                                @php $isLiked = auth()->user()->likedRecipes->contains($recipe); @endphp
                                <button class="recipe-card-fav {{ $isLiked ? 'liked' : '' }}" title="Save to favourites"
                                    data-recipe-id="{{ $recipe->id }}" onclick="toggleLike(event)">
                                    {{ $isLiked ? '♥' : '♡' }}
                                </button>
                            @endauth
                        </div>

                        <div class="recipe-card-body">
                            <h3 class="recipe-card-title">{{ $recipe->title }}</h3>
                            @if ($recipe->description)
                                <p class="recipe-card-desc">{{ $recipe->description }}</p>
                            @endif

                            <div class="recipe-card-footer">
                               <div class="recipe-card-author">
                                    <div class="avatar">
                                        {{ strtoupper(substr($recipe->user->name ?? 'A', 0, 2)) }}
                                    </div>
                                    <span>{{ $recipe->user->name ?? 'Anonymous' }}</span>
                                </div>
                                <div class="recipe-card-stats">
                                    <span class="stat">⏱ {{ $recipe->cook_time }}m</span>
                                    <span class="stat">👤 {{ $recipe->serving }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($recipes instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="pagination">
                    @if ($recipes->onFirstPage())
                        <span class="page-item"><span>‹</span></span>
                    @else
                        <span class="page-item"><a href="{{ $recipes->previousPageUrl() }}">‹</a></span>
                    @endif

                    @foreach ($recipes->getUrlRange(1, $recipes->lastPage()) as $page => $url)
                        <span class="page-item {{ $page == $recipes->currentPage() ? 'active' : '' }}">
                            @if ($page == $recipes->currentPage())
                                <span>{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </span>
                    @endforeach

                    @if ($recipes->hasMorePages())
                        <span class="page-item"><a href="{{ $recipes->nextPageUrl() }}">›</a></span>
                    @else
                        <span class="page-item"><span>›</span></span>
                    @endif
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">🍳</div>
                <h3>No recipes found</h3>
                <p>Try a different search term or category — or be the first to add one!</p>
            </div>
        @endif

        {{-- ── CTA Banner ──────────────────────────────────── --}}
        @guest
            <div class="cta-banner">
                <div>
                    <div class="cta-banner-eyebrow">Join the community</div>
                    <h2>Share your favourite<br>recipes with the world</h2>
                    <p>Create an account to save favourites, post your own recipes, and connect with other food lovers.</p>
                </div>
                <div class="cta-banner-actions">
                    <a href="{{ route('register') }}" class="btn-primary btn-primary-terra">Create account</a>
                    <a href="{{ route('login') }}" class="btn-primary btn-primary-ghost">Sign in</a>
                </div>
            </div>
        @endguest

    </div>
@endsection

@push('scripts')
<script>
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
            btn.innerHTML = data.is_liked ? '♥' : '♡';
            btn.classList.toggle('liked', data.is_liked);
        })
        .catch(err => console.error('Toggle failed', err));
}
</script>
@endpush
