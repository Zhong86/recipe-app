@extends('layouts.app')

@section('title', 'Create Recipes')

@section('content')

    {{-- ── Hero ─────────────────────────────────────────── --}}
    <div class="form-hero">
        <div class="page">
            <div class="form-hero-eyebrow">
                {{ isset($recipe) ? 'Edit Recipe' : 'New Recipe' }}
            </div>
            <h1 class="form-hero-title">
                {{ isset($recipe) ? 'Update your recipe' : 'Share your creation' }}
            </h1>
        </div>
    </div>

    <div class="page">
        <div class="recipe-form-page">

            @if ($errors->any())
                <div class="form-alert">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin:.5rem 0 0 1rem;line-height:1.8">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($recipe) ? url('/recipes/' . $recipe->id . '/update') : url('/recipes/create') }}"
                method="POST" enctype="multipart/form-data" id="recipe-form" novalidate>
                @csrf
                @if (isset($recipe))
                    @method('PUT')
                @endif

                <div class="recipe-form-grid">

                    {{-- ── LEFT: Main fields ──────────────────────── --}}
                    <div class="recipe-form-main">

                        {{-- Basics --}}
                        <div class="form-block">
                            <div class="form-block-title">
                                <span>📝</span> Basics
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="title">
                                    Recipe Title <span class="required">*</span>
                                </label>
                                <input type="text" id="title" name="title"
                                    class="form-input {{ $errors->has('title') ? 'has-error' : '' }}"
                                    placeholder="e.g. Classic Buttermilk Pancakes"
                                    value="{{ old('title', $recipe->title ?? '') }}" autocomplete="off">
                                @error('title')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-field">
                                <label class="form-label" for="description">Description</label>
                                <textarea id="description" name="description" class="form-textarea {{ $errors->has('description') ? 'has-error' : '' }}"
                                    placeholder="A short, enticing description of your recipe…" rows="3">{{ old('description', $recipe->description ?? '') }}</textarea>
                                @error('description')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="form-field" style="margin-bottom:0">
                                    <label class="form-label" for="category">
                                        Category <span class="required">*</span>
                                    </label>
                                    <select id="category" name="category"
                                        class="form-select {{ $errors->has('category') ? 'has-error' : '' }}">
                                        <option value="" disabled
                                            {{ old('category', $recipe->category ?? '') === '' ? 'selected' : '' }}>Select
                                            category…</option>
                                        @foreach (['breakfast', 'lunch', 'dinner', 'snack', 'dessert', 'drink'] as $cat)
                                            <option value="{{ $cat }}"
                                                {{ old('category', $recipe->category ?? '') === $cat ? 'selected' : '' }}>
                                                {{ ucfirst($cat) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <span class="form-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-row" style="gap:.75rem;margin-bottom:0">
                                    <div class="form-field" style="margin-bottom:0">
                                        <label class="form-label" for="cook_time">
                                            Cook Time (min) <span class="required">*</span>
                                        </label>
                                        <input type="number" id="cook_time" name="cook_time" min="1"
                                            class="form-input {{ $errors->has('cook_time') ? 'has-error' : '' }}"
                                            placeholder="30" value="{{ old('cook_time', $recipe->cook_time ?? '') }}">
                                        @error('cook_time')
                                            <span class="form-error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-field" style="margin-bottom:0">
                                        <label class="form-label" for="serving">
                                            Servings <span class="required">*</span>
                                        </label>
                                        <input type="number" id="serving" name="serving" min="1"
                                            class="form-input {{ $errors->has('serving') ? 'has-error' : '' }}"
                                            placeholder="4" value="{{ old('serving', $recipe->serving ?? '') }}">
                                        @error('serving')
                                            <span class="form-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Photo --}}
                        <div class="form-block">
                            <div class="form-block-title">
                                <span>📷</span> Photo
                            </div>

                            <div id="upload-area">
                                @if (isset($recipe) && $recipe->image_url)
                                    <div class="image-preview" id="img-preview">
                                        <img src="{{ $recipe->image_url }}" alt="Current image" id="preview-img">
                                        <button type="button" class="image-preview-remove" id="remove-img"
                                            title="Remove image">✕</button>
                                    </div>
                                    <div id="upload-zone" class="image-upload-zone" style="display:none;margin-top:.75rem">
                                        <input type="file" name="image" accept="image/*" id="image-input">
                                        <div class="upload-icon">🖼️</div>
                                        <p class="upload-label">
                                            <strong>Click to upload</strong> or drag and drop<br>
                                            <span>JPG, PNG or WebP — max 2 MB</span>
                                        </p>
                                    </div>
                                @else
                                    <div id="upload-zone" class="image-upload-zone">
                                        <input type="file" name="image" accept="image/*" id="image-input">
                                        <div class="upload-icon">🖼️</div>
                                        <p class="upload-label">
                                            <strong>Click to upload</strong> or drag and drop<br>
                                            <span>JPG, PNG or WebP — max 2 MB</span>
                                        </p>
                                    </div>
                                    <div class="image-preview" id="img-preview" style="display:none;margin-top:.75rem">
                                        <img id="preview-img" src="" alt="Preview">
                                        <button type="button" class="image-preview-remove" id="remove-img"
                                            title="Remove">✕</button>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <span class="form-error" style="margin-top:.5rem;display:flex">{{ $message }}</span>
                            @enderror
                            <p class="form-hint" style="margin-top:.6rem">A great photo helps your recipe stand out.</p>
                        </div>

                        {{-- Ingredients --}}
                        <div class="form-block">
                            <div class="form-block-title">
                                <span>🧂</span> Ingredients
                            </div>

                            @error('ingredients')
                                <span class="form-error" style="margin-bottom:1rem;display:flex">{{ $message }}</span>
                            @enderror

                            <table class="ingredients-table">
                                <thead>
                                    <tr>
                                        <th style="width:40%">Ingredient</th>
                                        <th style="width:20%">Quantity</th>
                                        <th style="width:30%">Unit</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="ingredients-body">
                                    @php
                                        $existingIngredients = old(
                                            'ingredients',
                                            isset($recipe) ? $recipe->ingredients->toArray() : [[]],
                                        );
                                        if (empty($existingIngredients)) {
                                            $existingIngredients = [[]];
                                        }
                                    @endphp

                                    @foreach ($existingIngredients as $i => $ingredient)
                                        <tr class="ingredient-row">
                                            <td>
                                                <input type="text" name="ingredients[{{ $i }}][name]"
                                                    class="form-input" placeholder="e.g. All-purpose flour"
                                                    value="{{ $ingredient['name'] ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="number" name="ingredients[{{ $i }}][quantity]"
                                                    class="form-input qty-input" placeholder="1" min="0"
                                                    step="0.01"
                                                    value="{{ isset($ingredient['quantity']) ? rtrim(rtrim(number_format((float) $ingredient['quantity'], 2), '0'), '.') : '' }}">
                                            </td>
                                            <td>
                                                <select name="ingredients[{{ $i }}][unit]"
                                                    class="form-select">
                                                    @php
                                                        $units = [
                                                            'tsp',
                                                            'tbsp',
                                                            'cup',
                                                            'ml',
                                                            'l',
                                                            'g',
                                                            'kg',
                                                            'oz',
                                                            'lb',
                                                            'piece',
                                                            'slice',
                                                            'pinch',
                                                            'bunch',
                                                        ];
                                                        $selectedUnit = isset($ingredient['unit'])
                                                            ? (is_object($ingredient['unit'])
                                                                ? $ingredient['unit']->value
                                                                : $ingredient['unit'])
                                                            : '';
                                                    @endphp
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit }}"
                                                            {{ $selectedUnit === $unit ? 'selected' : '' }}>
                                                            {{ $unit }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="remove-row-btn" onclick="removeRow(this)"
                                                    title="Remove">✕</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <button type="button" class="btn-add-row" id="add-ingredient">
                                <span>＋</span> Add ingredient
                            </button>
                        </div>

                        {{-- Steps --}}
                        <div class="form-block">
                            <div class="form-block-title">
                                <span>📋</span> Instructions
                            </div>

                            @error('steps')
                                <span class="form-error" style="margin-bottom:1rem;display:flex">{{ $message }}</span>
                            @enderror

                            <div id="steps-container">
                                @php
                                    $existingSteps = old(
                                        'steps',
                                        isset($recipe)
                                            ? $recipe->steps->sortBy('order')->pluck('instruction')->toArray()
                                            : [''],
                                    );
                                    if (empty($existingSteps)) {
                                        $existingSteps = [''];
                                    }
                                @endphp

                                @foreach ($existingSteps as $s => $step)
                                    <div class="step-entry" data-index="{{ $s }}">
                                        <div class="step-num">{{ $s + 1 }}</div>
                                        <textarea name="steps[{{ $s }}]" class="form-textarea"
                                            placeholder="Describe this step clearly and concisely…" rows="3">{{ $step }}</textarea>
                                        <button type="button" class="remove-row-btn" onclick="removeStep(this)"
                                            title="Remove step" style="margin-top:.55rem">✕</button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn-add-row" id="add-step">
                                <span>＋</span> Add step
                            </button>
                        </div>

                        {{-- Tags --}}
                        <div class="form-block">
                            <div class="form-block-title">
                                <span>🏷️</span> Tags
                            </div>

                            <div class="form-field">
                                <div class="tags-wrap" id="tags-wrap"
                                    onclick="document.getElementById('tag-input').focus()">
                                    @php
                                        $existingTags = old(
                                            'tags',
                                            isset($recipe) ? $recipe->tags->pluck('name')->toArray() : [],
                                        );
                                    @endphp
                                    @foreach ($existingTags as $tag)
                                        <span class="tag-chip" data-tag="{{ $tag }}">
                                            {{ $tag }}
                                            <input type="hidden" name="tags[]" value="{{ $tag }}">
                                            <button type="button" onclick="removeTag(this.parentElement)"
                                                aria-label="Remove tag">✕</button>
                                        </span>
                                    @endforeach
                                    <input type="text" id="tag-input" class="tag-text-input"
                                        placeholder="{{ count($existingTags) ? '' : 'Type a tag and press Enter or comma…' }}"
                                        autocomplete="off">
                                </div>
                                <p class="form-hint">e.g. <em>vegan, quick, comfort food</em> — press <kbd
                                        style="font-family:monospace;font-size:.75rem;background:var(--parchment);padding:.1rem .35rem;border-radius:2px;border:1px solid var(--border)">Enter</kbd>
                                    or comma to add</p>
                            </div>
                        </div>
                    </div>{{-- end main --}}

                    {{-- ── RIGHT: Sidebar ─────────────────────────── --}}
                    <aside class="form-sidebar">
                        <div class="sidebar-publish">
                            <div class="publish-title">
                                {{ isset($recipe) ? 'Save Changes' : 'Publish Recipe' }}
                            </div>

                            <div class="form-field" style="margin-bottom:1rem">
                                <select id="is_public" name="is_public" class="form-select" onchange="updateVisibilityText()">
                                    <option value="true">
                                        Public
                                    </option>
                                    <option value="false">
                                        Private
                                    </option>
                                </select>
                            </div>

                            <div class="publish-meta">
                                <div class="dot"></div>
                                <span
                                    id="visibility-text">{{ isset($recipe) ? 'Updating existing recipe' : 'Visible to all visitors once published' }}</span>
                            </div>

                            <button type="submit" class="btn-submit">
                                {{ isset($recipe) ? '✓ Save Changes' : '🍴 Publish Recipe' }}
                            </button>

                            <a href="{{ isset($recipe) ? url('/recipe/' . $recipe->id) : url('/recipes') }}"
                                class="btn-cancel">
                                Cancel
                            </a>

                            <ul class="form-checklist" id="checklist">
                                <li class="checklist-item" id="check-title">
                                    <div class="check-icon">✓</div>
                                    Title added
                                </li>
                                <li class="checklist-item" id="check-category">
                                    <div class="check-icon">✓</div>
                                    Category selected
                                </li>
                                <li class="checklist-item" id="check-time">
                                    <div class="check-icon">✓</div>
                                    Cook time & servings
                                </li>
                                <li class="checklist-item" id="check-ingredients">
                                    <div class="check-icon">✓</div>
                                    At least 1 ingredient
                                </li>
                                <li class="checklist-item" id="check-steps">
                                    <div class="check-icon">✓</div>
                                    At least 1 step
                                </li>
                            </ul>
                        </div>

                        @if (isset($recipe))
                            <div class="sidebar-publish" style="border-color:#FFCDD2;">
                                <div class="publish-title" style="color:#C62828;border-color:#FFCDD2;">Danger Zone</div>
                                <button type="submit" class="btn-cancel" form="delete-recipe-form"
                                    style="border-color:#FFCDD2;color:#C62828;width:100%"
                                    onclick="return confirm('Are you sure you want to delete this recipe? This cannot be undone.')">
                                    🗑 Delete Recipe
                                </button>
                            </div>
                        @endif

                    </aside>

                </div>{{-- end grid --}}

            </form>
            @if (isset($recipe))
                <form action="{{ url('/recipes/' . $recipe->id) }}" method="POST" id="delete-recipe-form">
                    @csrf
                    @method('DELETE')
                </form>
            @endif

        </div>
    </div>


    @push('scripts')
        <script>
            const UNITS = @json($units);

            // Shared template helpers
            const unitOptions = () => UNITS.map(u => `<option value="${u}">${u}</option>`).join('');

            const newIngredientRow = (i) => `
<tr class="ingredient-row">
<td><input type="text" name="ingredients[${i}][name]" class="form-input" placeholder="e.g. Salt"></td>
<td><input type="number" name="ingredients[${i}][quantity]" class="form-input qty-input" placeholder="1" min="0" step="0.01"></td>
<td><select name="ingredients[${i}][unit]" class="form-select">${unitOptions()}</select></td>
<td><button type="button" class="remove-row-btn" onclick="removeRow(this)">✕</button></td>
</tr>`;

            const newStepEntry = (i) => `
<div class="step-entry" data-index="${i}">
<div class="step-num">${i + 1}</div>
<textarea name="steps[${i}]" class="form-textarea" placeholder="Describe this step…" rows="3"></textarea>
<button type="button" class="remove-row-btn" onclick="removeStep(this)" style="margin-top:.55rem">✕</button>
</div>`;

            // Reindex helpers
            const reindex = (selector, namePattern) =>
                document.querySelectorAll(selector).forEach((el, i) =>
                    el.querySelectorAll('[name]').forEach(f => f.name = f.name.replace(namePattern, `$1[${i}]`)));

            // Ingredients
            let ingIdx = {{ count($existingIngredients) }};
            document.getElementById('add-ingredient').addEventListener('click', () => {
                document.getElementById('ingredients-body').insertAdjacentHTML('beforeend', newIngredientRow(ingIdx++));
                document.querySelector('#ingredients-body tr:last-child input[type=text]').focus();
                updateChecklist();
            });

            function removeRow(btn) {
                const tbody = document.getElementById('ingredients-body');
                tbody.querySelectorAll('.ingredient-row').length > 1 ?
                    btn.closest('tr').remove() :
                    btn.closest('tr').querySelectorAll('input,select').forEach(el => el.value = '');
                reindex('#ingredients-body .ingredient-row', /(ingredients)\[\d+\]/);
                ingIdx = tbody.querySelectorAll('.ingredient-row').length;
                updateChecklist();
            }

            // Steps
            let stepIdx = {{ count($existingSteps) }};
            document.getElementById('add-step').addEventListener('click', () => {
                document.getElementById('steps-container').insertAdjacentHTML('beforeend', newStepEntry(stepIdx++));
                document.querySelector('#steps-container .step-entry:last-child textarea').focus();
                updateChecklist();
            });

            function removeStep(btn) {
                const container = document.getElementById('steps-container');
                if (container.querySelectorAll('.step-entry').length > 1) btn.closest('.step-entry').remove();
                else btn.closest('.step-entry').querySelector('textarea').value = '';
                container.querySelectorAll('.step-entry').forEach((e, i) => {
                    e.dataset.index = i;
                    e.querySelector('.step-num').textContent = i + 1;
                    e.querySelector('textarea').name = `steps[${i}]`;
                });
                stepIdx = container.querySelectorAll('.step-entry').length;
                updateChecklist();
            }

            // Tags
            const tagInput = document.getElementById('tag-input');
            const tagsWrap = document.getElementById('tags-wrap');

            tagInput.addEventListener('keydown', e => {
                if (['Enter', ','].includes(e.key)) {
                    e.preventDefault();
                    addTag();
                }
                if (e.key === 'Backspace' && !tagInput.value) {
                    tagsWrap.querySelector('.tag-chip:last-of-type')?.remove();
                }
            });

            function addTag() {
                const val = tagInput.value.trim().replace(/,$/, '');
                const existing = [...tagsWrap.querySelectorAll('.tag-chip')].map(c => c.dataset.tag.toLowerCase());
                if (!val || existing.includes(val.toLowerCase())) {
                    tagInput.value = '';
                    return;
                }
                tagsWrap.insertAdjacentHTML('beforebegin', `
<span class="tag-chip" data-tag="${val}">${val}
<input type="hidden" name="tags[]" value="${val}">
<button type="button" onclick="this.closest('.tag-chip').remove()">✕</button>
</span>`);
                // Move inserted element before input
                tagsWrap.insertBefore(tagsWrap.previousElementSibling, tagInput);
                tagInput.value = '';
                tagInput.placeholder = '';
            }

            function removeTag(chip) {
                chip.remove();
            }

            // Image preview
            const imageInput = document.getElementById('image-input');
            const imgPreview = document.getElementById('img-preview');
            const uploadZone = document.getElementById('upload-zone');

            imageInput?.addEventListener('change', function() {
                if (!this.files[0]) return;
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('preview-img').src = e.target.result;
                    imgPreview.style.display = 'block';
                    uploadZone.style.display = 'none';
                };
                reader.readAsDataURL(this.files[0]);
            });

            document.getElementById('remove-img')?.addEventListener('click', () => {
                if (imageInput) imageInput.value = '';
                document.getElementById('preview-img').src = '';
                imgPreview.style.display = 'none';
                uploadZone.style.display = 'block';
            });

            uploadZone?.addEventListener('dragover', e => {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });
            ['dragleave', 'drop'].forEach(ev => uploadZone?.addEventListener(ev, () => uploadZone.classList.remove(
                'dragover')));

            // Live checklist
            const checkFields = ['title', 'category', 'cook_time', 'serving'];
            const setCheck = (id, done) => document.getElementById(id)?.classList.toggle('done', done);

            function updateChecklist() {
                const v = id => document.getElementById(id)?.value.trim();
                setCheck('check-title', !!v('title'));
                setCheck('check-category', !!v('category'));
                setCheck('check-time', v('cook_time') > 0 && v('serving') > 0);
                setCheck('check-ingredients', [...document.querySelectorAll('#ingredients-body input[type=text]')].some(i => i
                    .value.trim()));
                setCheck('check-steps', [...document.querySelectorAll('#steps-container textarea')].some(t => t.value.trim()));
            }

            checkFields.forEach(id => document.getElementById(id)?.addEventListener('input', updateChecklist));
            document.getElementById('ingredients-body').addEventListener('input', updateChecklist);
            document.getElementById('steps-container').addEventListener('input', updateChecklist);
            updateChecklist();

            function updateVisibilityText() {
                const select = document.getElementById('is_public');
                const textSpan = document.getElementById('visibility-text');
                const isPublic = select.value === 'true';

                if (isPublic) {
                    textSpan.textContent = 'Visible to all visitors once published';
                } else {
                    textSpan.textContent = 'Visible only to you';
                }
            }
        </script>
    @endpush

@endsection
