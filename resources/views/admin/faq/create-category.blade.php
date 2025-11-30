<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Create FAQ Category</h3>
            <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.faq.categories.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug') }}" placeholder="e.g., getting_started" required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Unique identifier. Use lowercase with underscores.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">🇬🇧 Name (English) <span class="text-danger">*</span></label>
                                    <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" 
                                           value="{{ old('name_en') }}" placeholder="e.g., Getting Started" required>
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">🇹🇿 Name (Kiswahili) <span class="text-danger">*</span></label>
                                    <input type="text" name="name_sw" class="form-control @error('name_sw') is-invalid @enderror" 
                                           value="{{ old('name_sw') }}" placeholder="e.g., Kuanza" required>
                                    @error('name_sw')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Icon</label>
                                    <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" 
                                           value="{{ old('icon') }}" placeholder="e.g., rocket, person, wallet">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Icon name (e.g., rocket, person, wallet, question-circle)</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="display_order" class="form-control @error('display_order') is-invalid @enderror" 
                                           value="{{ old('display_order', 1) }}" min="0">
                                    @error('display_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Create Category
                                </button>
                                <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-icons me-2"></i>Available Icons</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Common icons you can use:</p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['rocket', 'person', 'wallet', 'receipt', 'gear', 'shield-lock', 'question-circle', 'credit-card', 'bell', 'graph-up', 'phone', 'envelope'] as $icon)
                            <span class="badge bg-light-secondary">
                                <i class="bi bi-{{ $icon }} me-1"></i> {{ $icon }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>

