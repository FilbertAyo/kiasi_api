<x-admin-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h3>Static Content</h3>
            <a href="{{ route('admin.content.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Add Content
            </a>
        </div>
    </x-slot>

    <section class="section">
        {{-- Info Card --}}
        <div class="alert alert-light-primary mb-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-info-circle fs-4 me-3"></i>
                <div>
                    <h5 class="alert-heading">How Privacy & Terms Work</h5>
                    <p class="mb-2">These documents are legal agreements between you and your users:</p>
                    <ul class="mb-2">
                        <li><strong>Terms & Conditions</strong> - Rules users must follow to use your app</li>
                        <li><strong>Privacy Policy</strong> - How you collect, use, and protect user data</li>
                        <li><strong>About</strong> - Information about your company and app</li>
                    </ul>
                    <p class="mb-0"><strong>When to update:</strong> Update version when making significant changes. Users should be notified of major updates.</p>
                </div>
            </div>
        </div>

        @php
            $typeLabels = [
                'terms' => ['label' => 'Terms & Conditions', 'icon' => 'bi-file-earmark-text', 'color' => 'primary'],
                'privacy' => ['label' => 'Privacy Policy', 'icon' => 'bi-shield-lock', 'color' => 'success'],
                'about' => ['label' => 'About', 'icon' => 'bi-info-circle', 'color' => 'info'],
            ];
        @endphp

        @forelse($contents as $type => $items)
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="bi {{ $typeLabels[$type]['icon'] ?? 'bi-file' }} text-{{ $typeLabels[$type]['color'] ?? 'secondary' }} me-2"></i>
                        {{ $typeLabels[$type]['label'] ?? ucfirst($type) }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Language</th>
                                    <th>Title</th>
                                    <th>Version</th>
                                    <th>Effective Date</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $content)
                                <tr>
                                    <td>
                                        <span class="badge bg-light-{{ $content->language == 'en' ? 'primary' : 'warning' }}">
                                            {{ $content->language == 'en' ? '🇬🇧 English' : '🇹🇿 Kiswahili' }}
                                        </span>
                                    </td>
                                    <td>{{ $content->title }}</td>
                                    <td><code>v{{ $content->version ?? '1.0' }}</code></td>
                                    <td>{{ $content->effective_date ? $content->effective_date->format('M d, Y') : '-' }}</td>
                                    <td>
                                        @if($content->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $content->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.content.edit', $content) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.content.destroy', $content) }}" method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this content?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-earmark-x display-1 text-muted"></i>
                    <h4 class="mt-3">No Content Found</h4>
                    <p class="text-muted">Start by adding your Terms & Conditions, Privacy Policy, and About content.</p>
                    <a href="{{ route('admin.content.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Content
                    </a>
                    <p class="text-muted mt-3">Or run the seeder: <code>php artisan db:seed --class=StaticContentSeeder</code></p>
                </div>
            </div>
        @endforelse

        {{-- Version History Info --}}
        <div class="card">
            <div class="card-header">
                <h4 class="card-title"><i class="bi bi-clock-history me-2"></i>Version Management Guide</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6><i class="bi bi-arrow-up-circle text-success me-1"></i> Minor Update (1.0 → 1.1)</h6>
                        <ul class="small text-muted">
                            <li>Typo fixes</li>
                            <li>Clarifications</li>
                            <li>Formatting changes</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="bi bi-arrow-up-circle-fill text-warning me-1"></i> Major Update (1.x → 2.0)</h6>
                        <ul class="small text-muted">
                            <li>New data collection practices</li>
                            <li>Changed user rights</li>
                            <li>New third-party sharing</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="bi bi-bell text-danger me-1"></i> Notify Users When</h6>
                        <ul class="small text-muted">
                            <li>Any privacy policy changes</li>
                            <li>Terms that affect user rights</li>
                            <li>Major feature changes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>

