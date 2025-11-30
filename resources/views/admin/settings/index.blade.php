<x-admin-layout>
    <x-slot name="header">
        <h3>App Settings</h3>
    </x-slot>

    <section class="section">
        {{-- App Information - EDITABLE --}}
        @if($settings->has('app'))
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="bi bi-app-indicator text-primary me-2"></i>
                    App Information
                </h4>
                <p class="text-muted mb-0">Manage app version and maintenance settings</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'app') }}">
                    @csrf
                    @method('PUT')
                    @php $appData = $settings['app']->value; @endphp
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">App Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $appData['name'] ?? 'Kiasi Daily' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Current Version <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="version" value="{{ $appData['version'] ?? '1.0.0' }}" 
                                   placeholder="e.g., 1.0.0" required>
                            <small class="text-muted">Format: major.minor.patch (e.g., 1.2.3)</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Minimum Required Version</label>
                            <input type="text" class="form-control" name="minimum_version" value="{{ $appData['minimum_version'] ?? '1.0.0' }}"
                                   placeholder="e.g., 1.0.0">
                            <small class="text-muted">Users below this version must update</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="force_update" name="force_update" value="1"
                                       {{ ($appData['force_update'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="force_update">
                                    <strong>Force Update</strong>
                                    <br><small class="text-muted">Users must update to continue using the app</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1"
                                       {{ ($appData['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="maintenance_mode">
                                    <strong>Maintenance Mode</strong>
                                    <br><small class="text-muted">Show maintenance message to all users</small>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Maintenance Message</label>
                        <textarea class="form-control" name="maintenance_message" rows="2" 
                                  placeholder="We're currently performing maintenance. Please try again later.">{{ $appData['maintenance_message'] ?? '' }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Save App Settings
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Support Contact - EDITABLE --}}
        @if($settings->has('support'))
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="bi bi-headset text-success me-2"></i>
                    Support Contact
                </h4>
                <p class="text-muted mb-0">Manage support contact information</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'support') }}">
                    @csrf
                    @method('PUT')
                    @php $supportData = $settings['support']->value; @endphp
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="bi bi-envelope me-1"></i> Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $supportData['email'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="bi bi-telephone me-1"></i> Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{ $supportData['phone'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="bi bi-whatsapp me-1"></i> WhatsApp</label>
                            <input type="text" class="form-control" name="whatsapp" value="{{ $supportData['whatsapp'] ?? '' }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Save Support Info
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Company Information - EDITABLE --}}
        @if($settings->has('company'))
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="bi bi-building text-info me-2"></i>
                    Company Information
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'company') }}">
                    @csrf
                    @method('PUT')
                    @php $companyData = $settings['company']->value; @endphp
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $companyData['name'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" name="website" value="{{ $companyData['website'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $companyData['email'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" value="{{ $companyData['address'] ?? '' }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-check-circle me-1"></i> Save Company Info
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Social Media - EDITABLE --}}
        @if($settings->has('social'))
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="bi bi-share text-purple me-2"></i>
                    Social Media Links
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'social') }}">
                    @csrf
                    @method('PUT')
                    @php $socialData = $settings['social']->value; @endphp
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="bi bi-facebook me-1"></i> Facebook</label>
                            <input type="url" class="form-control" name="facebook" value="{{ $socialData['facebook'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="bi bi-twitter me-1"></i> Twitter</label>
                            <input type="url" class="form-control" name="twitter" value="{{ $socialData['twitter'] ?? '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><i class="bi bi-instagram me-1"></i> Instagram</label>
                            <input type="url" class="form-control" name="instagram" value="{{ $socialData['instagram'] ?? '' }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-check-circle me-1"></i> Save Social Links
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- App Store URLs - EDITABLE --}}
        @if($settings->has('update_urls'))
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="bi bi-cloud-download text-danger me-2"></i>
                    App Store URLs
                </h4>
                <p class="text-muted mb-0">Where users download/update the app</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update', 'update_urls') }}">
                    @csrf
                    @method('PUT')
                    @php $urlsData = $settings['update_urls']->value; @endphp
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-google-play me-1"></i> Google Play Store URL</label>
                            <input type="url" class="form-control" name="android" value="{{ $urlsData['android'] ?? '' }}"
                                   placeholder="https://play.google.com/store/apps/details?id=...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><i class="bi bi-apple me-1"></i> Apple App Store URL</label>
                            <input type="url" class="form-control" name="ios" value="{{ $urlsData['ios'] ?? '' }}"
                                   placeholder="https://apps.apple.com/app/...">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-check-circle me-1"></i> Save Store URLs
                    </button>
                </form>
            </div>
        </div>
        @endif

        <div class="row">
            {{-- Languages (Read-only) --}}
            @if($settings->has('languages'))
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-translate text-warning me-2"></i>
                            Supported Languages
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Native Name</th>
                                        <th>Default</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings['languages']->value as $lang)
                                    <tr>
                                        <td><code>{{ $lang['code'] }}</code></td>
                                        <td>{{ $lang['name'] }}</td>
                                        <td>{{ $lang['native_name'] }}</td>
                                        <td>
                                            @if($lang['is_default'] ?? false)
                                                <span class="badge bg-success">Default</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Currencies (Read-only) --}}
            @if($settings->has('currencies'))
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-currency-exchange text-success me-2"></i>
                            Supported Currencies
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Symbol</th>
                                        <th>Name</th>
                                        <th>Default</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings['currencies']->value as $currency)
                                    <tr>
                                        <td><code>{{ $currency['code'] }}</code></td>
                                        <td>{{ $currency['symbol'] }}</td>
                                        <td>{{ $currency['name'] }}</td>
                                        <td>
                                            @if($currency['is_default'] ?? false)
                                                <span class="badge bg-success">Default</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Date Formats (Read-only) --}}
        @if($settings->has('date_formats'))
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">
                    <i class="bi bi-calendar3 text-primary me-2"></i>
                    Date Formats
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($settings['date_formats']->value as $format)
                    <div class="col-md-4 mb-2">
                        <div class="p-3 rounded {{ ($format['is_default'] ?? false) ? 'bg-light-primary' : 'bg-light' }}">
                            <code>{{ $format['format'] }}</code>
                            <br>
                            <small class="text-muted">Example: {{ $format['example'] }}</small>
                            @if($format['is_default'] ?? false)
                                <span class="badge bg-primary ms-2">Default</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Info about read-only sections --}}
        <div class="alert alert-light border">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Note:</strong> Languages, Currencies, and Date Formats are read-only. To modify these, run: 
            <code>php artisan db:seed --class=AppConfigSeeder</code>
        </div>
    </section>
</x-admin-layout>
