<x-admin-layout>
    <x-slot name="header">
        <h3>Reports & Exports</h3>
    </x-slot>

    <section class="section">
        <div class="row">
            {{-- Export Transactions --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-receipt text-primary me-2"></i>
                            Export Transactions
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Download all transactions within a date range as CSV.</p>

                        <form method="GET" action="{{ route('admin.reports.transactions') }}">
                            <div class="mb-3">
                                <label for="trans_start_date" class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="trans_start_date" required
                                       class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="trans_end_date" class="form-label">End Date</label>
                                <input type="date" name="end_date" id="trans_end_date" required
                                       class="form-control">
                            </div>
                            <div class="mb-4">
                                <label for="trans_type" class="form-label">Type (Optional)</label>
                                <select name="type" id="trans_type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="expense">Expense</option>
                                    <option value="income">Income</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-download"></i> Export Transactions CSV
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Export Users --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-people text-success me-2"></i>
                            Export Users
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Download the list of all users as CSV.</p>

                        <form method="GET" action="{{ route('admin.reports.users') }}">
                            <div class="mb-4">
                                <label for="user_status" class="form-label">Status (Optional)</label>
                                <select name="status" id="user_status" class="form-select">
                                    <option value="">All Users</option>
                                    <option value="active">Active Only</option>
                                    <option value="blocked">Blocked Only</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-download"></i> Export Users CSV
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Export Monthly Summary --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="bi bi-calendar-month text-info me-2"></i>
                            Monthly Summary
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Download daily breakdown for a specific month.</p>

                        <form method="GET" action="{{ route('admin.reports.monthly') }}">
                            <div class="mb-3">
                                <label for="summary_year" class="form-label">Year</label>
                                <input type="number" name="year" id="summary_year" value="{{ date('Y') }}" 
                                       min="2000" max="2100" required class="form-control">
                            </div>
                            <div class="mb-4">
                                <label for="summary_month" class="form-label">Month</label>
                                <select name="month" id="summary_month" required class="form-select">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info w-100">
                                <i class="bi bi-download"></i> Export Monthly Summary CSV
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-admin-layout>
