<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>FAQ Management</h3>
            <a href="{{ route('admin.faq.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add Category
            </a>
        </div>
    </x-slot>

    <section class="section">
        {{-- Info Card --}}
        <div class="alert alert-light-info mb-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-lightbulb fs-4 me-3"></i>
                <div>
                    <h5 class="alert-heading">FAQ Best Practices</h5>
                    <ul class="mb-0">
                        <li>Keep answers clear and concise</li>
                        <li>Add questions in both Kiswahili and English</li>
                        <li>Update FAQs based on common support questions</li>
                        <li>Order questions by importance (most asked first)</li>
                    </ul>
                </div>
            </div>
        </div>

        @if($categories->count() > 0)
            <div class="row">
                @foreach($categories as $category)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-{{ $category->icon ?? 'folder' }} me-2"></i>
                                {{ $category->name_en }}
                            </h5>
                            @if(!$category->is_active)
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                <strong>Kiswahili:</strong> {{ $category->name_sw }}
                            </p>
                            <p class="mb-3">
                                <span class="badge bg-light-primary">{{ $category->questions_count }} questions</span>
                                <span class="badge bg-light-secondary">Order: {{ $category->display_order }}</span>
                            </p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.faq.category', $category) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye me-1"></i> View Questions
                                </a>
                                <a href="{{ route('admin.faq.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.faq.categories.destroy', $category) }}" method="POST" 
                                      onsubmit="return confirm('Delete this category and ALL its questions?')">
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
                @endforeach
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-question-circle display-1 text-muted"></i>
                    <h4 class="mt-3">No FAQ Categories Found</h4>
                    <p class="text-muted">Start by creating FAQ categories, then add questions to each category.</p>
                    <a href="{{ route('admin.faq.categories.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Category
                    </a>
                    <p class="text-muted mt-3">Or run the seeder: <code>php artisan db:seed --class=FaqSeeder</code></p>
                </div>
            </div>
        @endif
    </section>
</x-admin-layout>

