<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Create Content</h3>
            <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.content.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Content Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) == 'Terms' ? 'Terms & Conditions' : ($type == 'privacy' ? 'Privacy Policy' : ucfirst($type)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Language <span class="text-danger">*</span></label>
                            <select name="language" class="form-select @error('language') is-invalid @enderror" required>
                                <option value="">Select Language</option>
                                @foreach($languages as $code => $name)
                                    <option value="{{ $code }}" {{ old('language') == $code ? 'selected' : '' }}>
                                        {{ $code == 'en' ? '🇬🇧' : '🇹🇿' }} {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Version</label>
                            <input type="text" name="version" class="form-control @error('version') is-invalid @enderror" 
                                   value="{{ old('version', '1.0') }}" placeholder="e.g., 1.0">
                            @error('version')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="e.g., Terms and Conditions" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Effective Date</label>
                            <input type="date" name="effective_date" class="form-control @error('effective_date') is-invalid @enderror" 
                                   value="{{ old('effective_date', date('Y-m-d')) }}">
                            @error('effective_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span> <small class="text-muted">(Markdown supported)</small></label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                  rows="15" required placeholder="## Section Title&#10;&#10;Your content here...">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Use Markdown formatting: ## for headers, - for lists, **bold**, *italic*</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Create Content
                        </button>
                        <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-admin-layout>

