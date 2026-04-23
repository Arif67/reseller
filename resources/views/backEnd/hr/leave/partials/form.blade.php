<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Employee *</label><select class="form-control" name="employee_id" required><option value="">Select..</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected((string) old('employee_id', $leave?->employee_id) === (string) $employee->id)>{{ $employee->name }}</option>@endforeach</select></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Leave Type</label><select class="form-control" name="leave_type"><option value="">Select..</option>@foreach($leaveTypes as $leaveType)<option value="{{ $leaveType->name }}" @selected(old('leave_type', $leave?->leave_type) === $leaveType->name)>{{ $leaveType->name }}</option>@endforeach</select></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Status *</label><select class="form-control" name="status" required>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(old('status', $leave?->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Start Date *</label><input type="date" name="start_date" id="leave-start-date" class="form-control" value="{{ old('start_date', optional($leave?->start_date)->format('Y-m-d')) }}" required></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">End Date *</label><input type="date" name="end_date" id="leave-end-date" class="form-control" value="{{ old('end_date', optional($leave?->end_date)->format('Y-m-d')) }}" required></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Total Days</label><input type="number" min="1" name="total_days" id="leave-total-days" class="form-control" value="{{ old('total_days', $leave?->total_days ?? 1) }}"></div></div>
<div class="col-md-6"><div class="form-group mb-3"><label class="form-label">Reason</label><textarea name="reason" class="form-control" rows="4">{{ old('reason', $leave?->reason) }}</textarea></div></div>
<div class="col-md-6"><div class="form-group mb-3"><label class="form-label">Admin Note</label><textarea name="admin_note" class="form-control" rows="4">{{ old('admin_note', $leave?->admin_note) }}</textarea></div></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('leave-start-date');
    const endInput = document.getElementById('leave-end-date');
    const totalInput = document.getElementById('leave-total-days');

    function updateTotalDays() {
        if (!startInput?.value || !endInput?.value) {
            return;
        }

        const start = new Date(startInput.value + 'T00:00:00');
        const end = new Date(endInput.value + 'T00:00:00');
        const diff = Math.floor((end - start) / 86400000) + 1;

        if (diff > 0) {
            totalInput.value = diff;
        }
    }

    startInput?.addEventListener('change', updateTotalDays);
    endInput?.addEventListener('change', updateTotalDays);
});
</script>
