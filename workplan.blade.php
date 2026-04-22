<!DOCTYPE html>
<html>

<head>
    <title>Department Work Plan</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <style>
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

        .doc-header {
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 10px;
        }

        .doc-title {
            font-weight: bold;
            font-size: 18px;
        }

        .doc-meta {
            font-size: 12px;
        }

        .section-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
        }

        .label {
            font-weight: 600;
        }

        .small-note {
            font-size: 11px;
            color: #777;
        }

        td[rowspan] {
            vertical-align: middle !important;
        }
    </style>
</head>

<body>

    <div class="container mt-3">

        {{-- HEADER ISO --}}
        <div class="doc-header text-center">
            <div class="doc-title">DEPARTMENT WORK PLAN</div>
            <div class="doc-meta">
            </div>
        </div>

        {{-- ACTION --}}
        <div class="d-flex justify-content-between mb-2 no-print">

            <div class="d-flex gap-2">

                {{-- ADD --}}
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addWorkPlanModal">
                    + Add Work Plan
                </button>

                {{-- UPDATE --}}
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateWorkPlanModal">
                    ✏ Update Work Plan
                </button>

            </div>

            <button class="btn btn-dark btn-sm" onclick="printPage()">
                🖨 Print
            </button>

        </div>

        {{-- INFO SECTION --}}
        <div class="section-box">

            <div class="row">
                <div class="col-md-6">
                    <div><span class="label">Directorate:</span></div>
                    <div><span class="label">Division/Plant:</span></div>
                    <div><span class="label">Department:</span></div>
                </div>

                <div class="col-md-6">
                    <div><span class="label">Effective Year:</span></div>
                    <div><span class="label">Revision No:</span></div>
                    <div><span class="label">Revision Date:</span> </div>
                </div>
            </div>

        </div>

        <table class="table table-bordered table-sm mb-0 align-middle">

            <thead class="table-dark text-center">
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:10%;">Source No</th>
                    <th style="width:22%;">Work Plan</th>
                    <th style="width:15%;">Deliverable</th>
                    <th style="width:15%;">Section / In Charge</th>
                    <th style="width:10%;">Start Date</th>
                    <th style="width:10%;">Finish Date</th>
                    <th style="width:10%;">Last update</th>
                    <th style="width:14%;" class="no-print">Action</th>
                </tr>
            </thead>

            <tbody>

                @if ($workPlans->count())
                    @foreach ($workPlans as $source => $plans)
                        @foreach ($plans as $index => $plan)
                            <tr>

                                @if ($index == 0)
                                    {{-- NO --}}
                                    <td class="text-center fw-semibold" rowspan="{{ count($plans) }}">
                                        {{ $loop->parent->iteration }}
                                    </td>

                                    {{-- SOURCE --}}
                                    <td class="text-center" rowspan="{{ count($plans) }}">
                                        {{ $source ?? '-' }}
                                    </td>
                                @endif

                                {{-- WORK PLAN --}}
                                <td>
                                    <div class="fw-semibold ">{{ $plan->work_plan ?? '-' }} </div>
                                </td>

                                {{-- DELIVERABLE --}}
                                <td>{{ $plan->deliverable ?? '-' }} </td>

                                {{-- SECTION --}}
                                <td class="text-center">
                                    <small class="text-muted">{{ $plan->in_charge ?? '-' }} </small>
                                </td>

                                {{-- START --}}
                                <td class="text-center">
                                    {{ $plan->start_date ?? '-' }} ⏳
                                </td>

                                {{-- FINISH --}}
                                <td class="text-center">
                                    {{ $plan->finish_date ?? '-' }} 🏁
                                </td>

                                {{-- LAST UPDATE --}}
                                <td class="text-center">
                                    {{ $plan->updated_at?->format('d-m-Y') ?? '-' }} 🕒
                                    <small class="text-muted pointer"><a href="#" data-bs-toggle="modal"
                                            data-bs-target="#dataDetail">Detail</a></small>
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center no-print d-flex justify-content-between gap-2">

                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $plan->id }}">
                                        ✏️ Edit
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('workplan.delete', $plan->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this work plan? 🗑️')">
                                            🗑️ Delete
                                        </button>
                                    </form>

                                </td>

                            </tr>

                            {{-- MODAL EDIT (DI LUAR <tr>) --}}
                            <div class="modal fade" id="editModal{{ $plan->id }}" tabindex="-1"
                                aria-labelledby="editModalLabel{{ $plan->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <form action="{{ route('workplan.update', $plan->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $plan->id }}">Edit
                                                    Work Plan ✏️</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">

                                                <div class="mb-2">
                                                    <label class="form-label">Work Plan 📝</label>
                                                    <input type="text" name="work_plan"
                                                        value="{{ $plan->work_plan }}" class="form-control mb-2"
                                                        required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label">Source No 📄</label>
                                                    <input type="text" name="source" value="{{ $plan->source }}"
                                                        class="form-control mb-2" required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label">Deliverable 🎯</label>
                                                    <input type="text" name="deliverable"
                                                        value="{{ $plan->deliverable }}" class="form-control mb-2"
                                                        required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label">Section 🏢</label>
                                                    <input type="text" name="section" value="{{ $plan->section }}"
                                                        class="form-control mb-2" required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label">In Charge 👨‍💼</label>
                                                    <input type="text" name="in_charge"
                                                        value="{{ $plan->in_charge }}" class="form-control mb-2"
                                                        required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label">Start Date ⏳</label>
                                                    <input type="date" name="start_date"
                                                        value="{{ $plan->start_date }}" class="form-control mb-2"
                                                        required>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label">Finish Date 🏁</label>
                                                    <input type="date" name="finish_date"
                                                        value="{{ $plan->finish_date }}" class="form-control mb-2"
                                                        required>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Close 🚪
                                                </button>
                                                <button class="btn btn-primary">
                                                    Update 📝
                                                </button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">
                            No Work Plan Data Available 😔
                        </td>
                    </tr>
                @endif

            </tbody>

    </div>

    <script>
        function printPage() {
            window.print();
        }
    </script>
    <!-- ADD WORK PLAN MODAL -->
    <div class="modal fade" id="addWorkPlanModal" tabindex="-1" aria-labelledby="addWorkPlanModalLabel"
        aria-hidden="true">
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
                <form action="{{ route('workplan.update', $plan->id) }}" method="POST">
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
                <form action="{{ route('workplan.update', $plan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="workplan_id">

                    <div class="modal-header">
                        <h5 class="modal-title">DATA MONITORING PER BULAN</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Progress : </p>

                        {{-- AWAL TABEL ITEM + PENCAPAIAN --}}

                        {{-- AKHIR TABEL ITEM + PENCAPAIAN --}}

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
</body>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}">
    function editWorkPlan(data) {
        document.getElementById('workplan_id').value = data.id;
        document.querySelector('input[name="work_plan"]').value = data.work_plan;
    }
</script>
