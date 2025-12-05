<x-guest-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <!-- Header -->
                <div class="text-center mb-5">
                    <a href="{{ url('/') }}" class="d-inline-flex align-items-center text-decoration-none mb-4">
                        <img src="{{ asset('assets/images/logo/kiasi_logo.svg') }}" alt="Kiasi Daily" style="width: 50px; height: 50px; margin-right: 12px;">
                        <span class="h2 text-primary fw-bold mb-0">Kiasi Daily</span>
                    </a>
                    <h1 class="display-5 fw-bold mb-3">Terms and Conditions</h1>
                    <p class="text-muted">
                        @if($content->effective_date)
                            Effective Date: {{ $content->effective_date->format('F d, Y') }}
                        @endif
                        @if($content->version)
                            | Version: {{ $content->version }}
                        @endif
                    </p>
                </div>

                <!-- Language Switcher -->
                <div class="text-end mb-4">
                    <div class="btn-group" role="group">
                        <a href="{{ route('terms-and-conditions', ['lang' => 'en']) }}" 
                           class="btn btn-sm {{ $language === 'en' ? 'btn-primary' : 'btn-outline-primary' }}">
                            English
                        </a>
                        <a href="{{ route('terms-and-conditions', ['lang' => 'sw']) }}" 
                           class="btn btn-sm {{ $language === 'sw' ? 'btn-primary' : 'btn-outline-primary' }}">
                            Kiswahili
                        </a>
                    </div>
                </div>

                <!-- Content -->
                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <div class="content-body">
                            {!! $htmlContent !!}
                        </div>
                    </div>
                </div>

                <!-- Footer Links -->
                <div class="text-center mt-4">
                    <a href="{{ route('privacy-policy', ['lang' => $language]) }}" class="text-decoration-none me-3">
                        Privacy Policy
                    </a>
                    <span class="text-muted">|</span>
                    <a href="{{ url('/') }}" class="text-decoration-none ms-3">
                        Back to Home
                    </a>
                </div>

                <!-- Last Updated -->
                <div class="text-center mt-4 text-muted small">
                    <p>Last Updated: {{ $content->updated_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .content-body {
            line-height: 1.8;
            color: #333;
        }
        .content-body h2 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .content-body h2:first-child {
            margin-top: 0;
        }
        .content-body p {
            margin-bottom: 1rem;
        }
        .content-body ul, .content-body ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }
        .content-body li {
            margin-bottom: 0.5rem;
        }
        .content-body strong {
            font-weight: 600;
            color: #2c3e50;
        }
    </style>
    @endpush
</x-guest-layout>

