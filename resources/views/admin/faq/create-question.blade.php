<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Add Question to: {{ $category->name_en }}</h3>
            <a href="{{ route('admin.faq.category', $category) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.faq.questions.store', $category) }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Language <span class="text-danger">*</span></label>
                            <select name="language" class="form-select @error('language') is-invalid @enderror" required>
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
                            <label class="form-label">Display Order</label>
                            <input type="number" name="display_order" class="form-control @error('display_order') is-invalid @enderror" 
                                   value="{{ old('display_order', 1) }}" min="0">
                            @error('display_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Question <span class="text-danger">*</span></label>
                        <input type="text" name="question" class="form-control @error('question') is-invalid @enderror" 
                               value="{{ old('question') }}" placeholder="e.g., How do I add a new expense?" required>
                        @error('question')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Answer <span class="text-danger">*</span></label>
                        <textarea name="answer" class="form-control @error('answer') is-invalid @enderror" 
                                  rows="5" required placeholder="Provide a clear and helpful answer...">{{ old('answer') }}</textarea>
                        @error('answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Create Question
                        </button>
                        <a href="{{ route('admin.faq.category', $category) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-admin-layout>

