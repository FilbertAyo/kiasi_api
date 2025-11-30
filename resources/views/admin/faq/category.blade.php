<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>
                <i class="bi bi-{{ $category->icon ?? 'folder' }} me-2"></i>
                {{ $category->name_en }} - Questions
            </h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.faq.questions.create', $category) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Add Question
                </a>
                <a href="{{ route('admin.faq.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <section class="section">
        {{-- Category Info --}}
        <div class="alert alert-light mb-4">
            <div class="row">
                <div class="col-md-6">
                    <strong>English:</strong> {{ $category->name_en }}
                </div>
                <div class="col-md-6">
                    <strong>Kiswahili:</strong> {{ $category->name_sw }}
                </div>
            </div>
        </div>

        @if($questions->count() > 0)
            {{-- English Questions --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">
                        <span class="me-2">🇬🇧</span> English Questions
                    </h4>
                </div>
                <div class="card-body">
                    @if(isset($questions['en']) && $questions['en']->count() > 0)
                        <div class="accordion" id="accordionEnglish">
                            @foreach($questions['en'] as $question)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#en{{ $question->id }}">
                                        <span class="badge bg-light-secondary me-2">#{{ $question->display_order }}</span>
                                        {{ $question->question }}
                                        @if(!$question->is_active)
                                            <span class="badge bg-secondary ms-2">Inactive</span>
                                        @endif
                                    </button>
                                </h2>
                                <div id="en{{ $question->id }}" class="accordion-collapse collapse" 
                                     data-bs-parent="#accordionEnglish">
                                    <div class="accordion-body">
                                        <p>{{ $question->answer }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="bi bi-hand-thumbs-up me-1"></i> {{ $question->helpful_count ?? 0 }} found helpful
                                            </small>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.faq.questions.edit', $question) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.faq.questions.destroy', $question) }}" method="POST"
                                                      onsubmit="return confirm('Delete this question?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No English questions yet.</p>
                    @endif
                </div>
            </div>

            {{-- Swahili Questions --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <span class="me-2">🇹🇿</span> Kiswahili Questions
                    </h4>
                </div>
                <div class="card-body">
                    @if(isset($questions['sw']) && $questions['sw']->count() > 0)
                        <div class="accordion" id="accordionSwahili">
                            @foreach($questions['sw'] as $question)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#sw{{ $question->id }}">
                                        <span class="badge bg-light-secondary me-2">#{{ $question->display_order }}</span>
                                        {{ $question->question }}
                                        @if(!$question->is_active)
                                            <span class="badge bg-secondary ms-2">Inactive</span>
                                        @endif
                                    </button>
                                </h2>
                                <div id="sw{{ $question->id }}" class="accordion-collapse collapse" 
                                     data-bs-parent="#accordionSwahili">
                                    <div class="accordion-body">
                                        <p>{{ $question->answer }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="bi bi-hand-thumbs-up me-1"></i> {{ $question->helpful_count ?? 0 }} waliona inasaidia
                                            </small>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.faq.questions.edit', $question) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil me-1"></i> Hariri
                                                </a>
                                                <form action="{{ route('admin.faq.questions.destroy', $question) }}" method="POST"
                                                      onsubmit="return confirm('Futa swali hili?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Hakuna maswali ya Kiswahili bado.</p>
                    @endif
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-chat-square-text display-1 text-muted"></i>
                    <h4 class="mt-3">No Questions Yet</h4>
                    <p class="text-muted">Add questions in both English and Kiswahili for this category.</p>
                    <a href="{{ route('admin.faq.questions.create', $category) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Question
                    </a>
                </div>
            </div>
        @endif
    </section>
</x-admin-layout>

