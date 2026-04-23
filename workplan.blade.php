@extends('layouts.app')

@section('title', 'Department Work Plan')

@section('content')
    <style>
        .modal {
            z-index: 9999 !important;
        }

        .modal-backdrop {
            z-index: 9998 !important;
        }

        /* Tambahan agar scrollbar tidak dobel saat modal muncul */
        body.modal-open {
            overflow: hidden;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 11px;
            }

            .card {
                border: none;
            }
        }

        body {
            background-color: #f4f7f6;
        }

        /* Header Dokumen ala ISO */
        .doc-header-table {
            width: 100%;
            border: 2px solid #000;
            margin-bottom: 15px;
            background-color: #fff;
        }

        .doc-header-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
        }

        /* Tabel Utama */
        .table thead th {
            background-color: #212529;
            color: white;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            font-weight: 700;
            vertical-align: middle;
        }

        .table tbody td {
            font-size: 0.85rem;
            padding: 0.75rem;
            vertical-align: middle !important;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }

        .label-side {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 600;
            display: block;
            margin-bottom: 2px;
        }

        .value-side {
            font-weight: 700;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 10px;
            color: #212529;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: #fff !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>

    <div class="container-fluid py-3">

        {{-- HEADER DOKUMEN (ISO STYLE) --}}
        {{-- <table class="doc-header-table text-center">
            <tr>
                <td width="20%">
                    <h4 class="mb-0 fw-bold">LOGO</h4>
                </td>
                <td width="55%">
                    <h5 class="mb-0 fw-bold text-uppercase">DEPARTMENT WORK PLAN</h5>
                </td>
                <td width="25%" class="text-start small">
                    <div>Doc No: <strong>DWP/{{ $selectedYear }}/{{ strtoupper($selectedWh) }}</strong></div>
                    <div>Year: <strong>{{ $selectedYear }}</strong></div>
                </td>
            </tr>
        </table> --}}

        {{-- TOOLBAR ACTION --}}
        <div class="card mb-3 no-print bg-dark">
            <div class="card-body py-2 d-flex justify-content-between align-items-center text-white">
                <form action="{{ route('workplan.index') }}" method="GET" class="d-flex gap-2">
                    {{-- Filter Tahun --}}
                    <select name="tahun" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Year:
                                {{ $y }}</option>
                        @endforeach
                    </select>

                    {{-- Filter Warehouse (Hanya Superuser) --}}
                    @if ($userRole === 'superuser')
                        <select name="warehouse" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            <option value="all" {{ $selectedWh == 'all' ? 'selected' : '' }}>-- All Warehouse --</option>
                            @foreach ($listWarehouse as $wh)
                                <option value="{{ $wh }}" {{ $selectedWh == $wh ? 'selected' : '' }}>WH:
                                    {{ $wh }}</option>
                            @endforeach
                        </select>
                    @endif

                    <a href="{{ route('workplan.index') }}" class="btn btn-danger btn-sm px-3">
                        <i data-lucide="refresh-cw" class="me-1" style="width:14px"></i> Reset
                    </a>
                </form>

                <div class="d-flex gap-2">
                    <button class="btn btn-success btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal"
                        data-bs-target="#addWorkPlanModal">
                        <i data-lucide="plus-circle" style="width: 16px;"></i> Add Plan
                    </button>
                    <button class="btn btn-info btn-sm text-white d-flex align-items-center gap-1" onclick="window.print()">
                        <i data-lucide="printer" style="width: 16px;"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{-- SIDEBAR INFO (KIRI) --}}
            <div class="col-lg-3">
                <div class="card h-100">
                    <div class="card-header bg-dark text-white p-2 small fw-bold text-center">
                        DETAILS INFORMATION
                    </div>
                    <div class="card-body">
                        <span class="label-side">DIRECTORATE</span>
                        <span class="value-side">{{ $directorate }}</span>

                        <span class="label-side">DIVISION / PLANT</span>
                        <span class="value-side">{{ $division }}</span>

                        <span class="label-side">DEPARTMENT</span>
                        <span class="value-side text-primary">{{ $department }}</span>

                        <hr>
                        <span class="label-side">TOTAL RECORDS</span>
                        <span class="badge bg-secondary">{{ $workPlans->flatten()->count() }} Items</span>
                    </div>
                </div>
            </div>

            {{-- TABLE PROGRESS (KANAN) --}}
            <div class="col-lg-9">
                <div class="card h-100">
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="sticky-top">
                                    <tr class="text-center">
                                        <th width="40">NO</th>
                                        <th width="100">SOURCE</th>
                                        <th>WORK PLAN DETAIL</th>
                                        <th width="120">TIMELINE</th>
                                        <th width="80">STATUS</th>
                                        <th width="110" class="no-print">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($workPlans as $source => $plans)
                                        @foreach ($plans as $index => $plan)
                                            <tr>
                                                @if ($index == 0)
                                                    <td class="text-center fw-bold bg-light" rowspan="{{ count($plans) }}">
                                                        {{ $loop->parent->iteration }}
                                                    </td>
                                                    <td class="text-center fw-bold text-primary bg-light-subtle"
                                                        rowspan="{{ count($plans) }}">
                                                        {{ $source ?? '-' }}
                                                    </td>
                                                @endif

                                                <td>
                                                    <div class="fw-bold">{{ $plan->work_plan }}</div>
                                                    <small class="text-muted d-block">Deliverable:
                                                        {{ $plan->deliverable }}</small>
                                                    <small class="text-primary" style="font-size: 10px;">PIC:
                                                        {{ $plan->in_charge }} ({{ $plan->section }})</small>
                                                </td>
                                                <td class="text-center small">
                                                    <div class="text-success">S: {{ $plan->start_date }}</div>
                                                    <div class="text-danger">F: {{ $plan->finish_date }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-light border py-0" data-bs-toggle="modal"
                                                        data-bs-target="#dataDetail">
                                                        <small>Detail</small>
                                                    </button>
                                                </td>
                                                <td class="text-center no-print">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <button class="btn btn-sm btn-outline-warning"
                                                            onclick="editWorkPlan({{ json_encode($plan) }})"
                                                            data-bs-toggle="modal" data-bs-target="#updateWorkPlanModal">
                                                            <i data-lucide="edit-3" style="width: 14px;"></i>
                                                        </button>
                                                        <form action="{{ route('workplan.delete', $plan->id) }}"
                                                            method="POST" onsubmit="return confirm('Hapus plan ini?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger">
                                                                <i data-lucide="trash-2" style="width: 14px;"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">No Work Plan data
                                                available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD WORK PLAN MODAL -->
    <div class="modal fade" id="addWorkPlanModal" tabindex="-1" aria-labelledby="addWorkPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ route('workplan.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Add Work Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">


                        <div class="mb-2">
                            <label class="form-label">Source No</label>
                            <input type="text" name="source" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Work Plan</label>
                            <input type="text" name="work_plan" class="form-control"
                                placeholder="Enter work plan...">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Deliverable</label>
                            <input type="text" name="deliverable" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Section In Charge</label>
                            <input type="text" name="in_charge" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Finish Date</label>
                            <input type="date" name="finish_date" class="form-control">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Save Work Plan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="updateWorkPlanModal" tabindex="-1" aria-labelledby="updateWorkPlanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('workplan.update', $plan->id ?? 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="workplan_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Update Work Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        {{-- WORK PLAN --}}
                        <select name="work_plan_id" class="form-control form-control-sm" required>
                            <option value="">-- Select Work Plan --</option>

                            @foreach ($workplanMaster as $wp)
                                <option value="{{ $wp->id }}">
                                    {{ $wp->work_plan }}
                                </option>
                            @endforeach

                        </select>

                        {{-- BULAN --}}
                        <div class="mb-2">
                            <label>Bulan</label>
                            <input type="month" name="month" class="form-control">
                        </div>

                        {{-- ITEM --}}
                        <div class="mb-2">
                            <label>Item</label>
                            <input type="text" name="item" class="form-control">
                        </div>

                        {{-- MULTIPLE FOTO --}}
                        <div class="mb-2">
                            <label>Lampiran Foto</label>
                            <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button class="btn btn-primary">
                            Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <div class="modal fade" id="dataDetail" tabindex="-1" aria-labelledby="dataDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">DATA MONITORING PER BULAN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Progress:</p>

                    {{-- Tabel Item + Pencapaian --}}


                    {{-- Jika ingin menambahkan form atau informasi lain di bawah tabel, bisa ditambahkan di sini --}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary">Perbarui</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function editWorkPlan(data) {
            // Ambil ID modal yang mau dipake (sesuaikan dengan ID modal lu bro)
            const modal = document.querySelector('#updateWorkPlanModal');

            // Mapping data dari baris tabel ke input modal
            document.getElementById('workplan_id').value = data.id;

            // Contoh cara isi input (sesuaikan nama 'name' di form lu)
            modal.querySelector('input[name="work_plan"]').value = data.work_plan;
            modal.querySelector('input[name="source"]').value = data.source;
            modal.querySelector('input[name="deliverable"]').value = data.deliverable;
            modal.querySelector('input[name="section"]').value = data.section;
            modal.querySelector('input[name="in_charge"]').value = data.in_charge;
            modal.querySelector('input[name="start_date"]').value = data.start_date;
            modal.querySelector('input[name="finish_date"]').value = data.finish_date;
        }
    </script>
@endsection
