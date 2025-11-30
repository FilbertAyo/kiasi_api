<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Edit Question</h3>
            <a href="{{ route('admin.faq.category', $question->category) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.faq.questions.update', $question) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Language</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $question->language == 'en' ? '🇬🇧 English' : '🇹🇿 Kiswahili' }}" disabled>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="display_order" class="form-control @error('display_order') is-invalid @enderror" 
                                           value="{{ old('display_order', $question->display_order) }}" min="0">
                                    @error('display_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Question <span class="text-danger">*</span></label>
                                <input type="text" name="question" class="form-control @error('question') is-invalid @enderror" 
                                       value="{{ old('question', $question->question) }}" required>
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Answer <span class="text-danger">*</span></label>
                                <textarea name="answer" class="form-control @error('answer') is-invalid @enderror" 
                                          rows="6" required>{{ old('answer', $question->answer) }}</textarea>
                                @error('answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active</strong>
                                        <br><small class="text-muted">If unchecked, this question won't be shown in the app</small>
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i> Update Question
                                </button>
                                <a href="{{ route('admin.faq.category', $question->category) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Question Info</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted">Category</td>
                                <td><strong>{{ $question->category->name_en }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Helpful Count</td>
                                <td>
                                    <i class="bi bi-hand-thumbs-up text-success me-1"></i>
                                    {{ $question->helpful_count ?? 0 }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Created</td>
                                <td>{{ $question->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Updated</td>
                                <td>{{ $question->updated_at->diffForHumans() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>

