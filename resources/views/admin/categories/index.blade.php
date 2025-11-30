<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Categories Management</h3>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Category
            </a>
        </div>
    </x-slot>

    <section class="section">
        <div class="row">
            {{-- Expense Categories --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-arrow-up-circle text-danger me-2"></i>
                            Expense Categories
                        </h4>
                    </div>
                    <div class="card-body">
                        @foreach($categories->where('type', 'expense') as $category)
                            <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded" 
                                 style="background-color: {{ '#' . substr($category->color, 4, 6) }}15">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-lg me-3" style="background-color: {{ '#' . substr($category->color, 4, 6) }}30">
                                      
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $category->name }}</h6>
                                        <small class="text-muted">{{ $category->transactions_count }} transactions</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-light-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($category->transactions_count === 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if($categories->where('type', 'expense')->isEmpty())
                            <p class="text-muted text-center py-4">No expense categories yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Income Categories --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-arrow-down-circle text-success me-2"></i>
                            Income Categories
                        </h4>
                    </div>
                    <div class="card-body">
                        @foreach($categories->where('type', 'income') as $category)
                            <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded" 
                                 style="background-color: {{ '#' . substr($category->color, 4, 6) }}15">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-lg me-3" style="background-color: {{ '#' . substr($category->color, 4, 6) }}30">
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $category->name }}</h6>
                                        <small class="text-muted">{{ $category->transactions_count }} transactions</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-light-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($category->transactions_count === 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if($categories->where('type', 'income')->isEmpty())
                            <p class="text-muted text-center py-4">No income categories yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
