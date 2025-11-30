<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Edit Category</h3>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-light-secondary">
                <i class="bi bi-arrow-left"></i> Back to Categories
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update Category</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Category name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon (Material Icon Name)</label>
                                <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}" required
                                       class="form-control @error('icon') is-invalid @enderror"
                                       placeholder="e.g., restaurant, work, shopping_bag">
                                <small class="text-muted">
                                    Use Material Icons names. See: 
                                    <a href="https://fonts.google.com/icons" target="_blank" class="text-primary">Material Icons</a>
                                </small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="color" class="form-label">Color (Flutter Format)</label>
                                <input type="text" name="color" id="color" value="{{ old('color', $category->color) }}" required
                                       class="form-control @error('color') is-invalid @enderror"
                                       placeholder="e.g., 0xFFFF6B6B">
                                <small class="text-muted">Format: 0xFFRRGGBB (Flutter color format)</small>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" required
                                        class="form-select @error('type') is-invalid @enderror">
                                    <option value="expense" {{ old('type', $category->type) === 'expense' ? 'selected' : '' }}>Expense</option>
                                    <option value="income" {{ old('type', $category->type) === 'income' ? 'selected' : '' }}>Income</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Update Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
