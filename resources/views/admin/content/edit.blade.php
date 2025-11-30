<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Edit {{ ucfirst($content->type) }} - {{ $content->language == 'en' ? 'English' : 'Kiswahili' }}</h3>
            <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.content.update', $content) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $content->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Version</label>
                                    <div class="input-group">
                                        <span class="input-group-text">v</span>
                                        <input type="text" name="version" class="form-control @error('version') is-invalid @enderror" 
                                               value="{{ old('version', $content->version) }}">
                                    </div>
                                    @error('version')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Effective Date</label>
                                    <input type="date" name="effective_date" class="form-control @error('effective_date') is-invalid @enderror" 
                                           value="{{ old('effective_date', $content->effective_date?->format('Y-m-d')) }}">
                                    @error('effective_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content <span class="text-danger">*</span> <small class="text-muted">(Markdown supported)</small></label>
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                          rows="20" required>{{ old('content', $content->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                               {{ old('is_active', $content->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active</strong>
                                            <br><small class="text-muted">If unchecked, this content won't be shown in the app</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="increment_version" name="increment_version" value="1">
                                        <label class="form-check-label" for="increment_version">
                                            <strong>Auto-increment version</strong>
                                            <br><small class="text-muted">Automatically update version number (e.g., 1.0 → 1.1)</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Update Content
                                </button>
                                <a href="{{ route('admin.content.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Info Card --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Content Info</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted">Type</td>
                                <td><strong>{{ ucfirst($content->type) }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Language</td>
                                <td>{{ $content->language == 'en' ? '🇬🇧 English' : '🇹🇿 Kiswahili' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Created</td>
                                <td>{{ $content->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Last Updated</td>
                                <td>{{ $content->updated_at->diffForHumans() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Markdown Guide --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-markdown me-2"></i>Markdown Guide</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm small">
                            <tr>
                                <td><code>## Heading</code></td>
                                <td>Section heading</td>
                            </tr>
                            <tr>
                                <td><code>**bold**</code></td>
                                <td><strong>bold text</strong></td>
                            </tr>
                            <tr>
                                <td><code>*italic*</code></td>
                                <td><em>italic text</em></td>
                            </tr>
                            <tr>
                                <td><code>- item</code></td>
                                <td>Bullet list</td>
                            </tr>
                            <tr>
                                <td><code>1. item</code></td>
                                <td>Numbered list</td>
                            </tr>
                            <tr>
                                <td><code>[link](url)</code></td>
                                <td>Hyperlink</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>

