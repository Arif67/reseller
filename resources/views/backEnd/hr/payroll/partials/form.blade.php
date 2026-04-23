<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Employee *</label><select class="form-control" name="employee_id" id="payroll-employee-id" required><option value="">Select..</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected((string) old('employee_id', $payroll?->employee_id) === (string) $employee->id)>{{ $employee->name }}</option>@endforeach</select></div></div>
<div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Month *</label><input type="number" min="1" max="12" name="month" id="payroll-month" class="form-control" value="{{ old('month', $payroll?->month) }}" required></div></div>
<div class="col-md-2"><div class="form-group mb-3"><label class="form-label">Year *</label><input type="number" min="2000" max="2100" name="year" id="payroll-year" class="form-control" value="{{ old('year', $payroll?->year ?? now()->year) }}" required></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label class="form-label">Payment Date</label><input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', optional($payroll?->payment_date)->format('Y-m-d')) }}"></div></div>
<div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Basic Salary</label><input type="number" step="0.01" min="0" name="basic_salary" id="payroll-basic-salary" class="form-control" value="{{ old('basic_salary', $payroll?->basic_salary) }}"></div></div>
<div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Bonus</label><input type="number" step="0.01" min="0" name="bonus" id="payroll-bonus" class="form-control" value="{{ old('bonus', $payroll?->bonus) }}"></div></div>
<div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Deduction</label><input type="number" step="0.01" min="0" name="deduction" id="payroll-deduction" class="form-control" value="{{ old('deduction', $payroll?->deduction) }}"></div></div>
<div class="col-md-3"><div class="form-group mb-3"><label class="form-label">Status *</label><select class="form-control" name="status" required>@foreach(['pending','paid','hold'] as $status)<option value="{{ $status }}" @selected(old('status', $payroll?->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div></div>
<div class="col-md-12"><div class="alert alert-info py-2">Basic salary blank rakhle latest active salary structure theke nibe. Deduction blank rakhle approved/running advance or loan installment theke auto deduction hobe.</div></div>
<div class="col-md-12">
    <div class="card border-0 bg-light mb-3">
        <div class="card-body py-3">
            <div class="row g-3 text-center">
                <div class="col-md-3"><div class="small text-muted">Preview Basic</div><div class="fw-bold" id="payroll-preview-basic">0.00</div></div>
                <div class="col-md-3"><div class="small text-muted">Preview Bonus</div><div class="fw-bold" id="payroll-preview-bonus">0.00</div></div>
                <div class="col-md-3"><div class="small text-muted">Preview Deduction</div><div class="fw-bold" id="payroll-preview-deduction">0.00</div></div>
                <div class="col-md-3"><div class="small text-muted">Preview Net Salary</div><div class="fw-bold text-success" id="payroll-preview-net">0.00</div></div>
            </div>
            <div class="small text-muted mt-3" id="payroll-auto-fetch-message">Employee, month, and year select korle latest salary structure and active advance deduction preview dekhabe.</div>
        </div>
    </div>
</div>
<div class="col-md-12"><div class="form-group mb-3"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="4">{{ old('notes', $payroll?->notes) }}</textarea></div></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const basicInput = document.querySelector('input[name="basic_salary"]');
    const bonusInput = document.querySelector('input[name="bonus"]');
    const deductionInput = document.querySelector('input[name="deduction"]');
    const employeeInput = document.getElementById('payroll-employee-id');
    const monthInput = document.getElementById('payroll-month');
    const yearInput = document.getElementById('payroll-year');
    const basicPreview = document.getElementById('payroll-preview-basic');
    const bonusPreview = document.getElementById('payroll-preview-bonus');
    const deductionPreview = document.getElementById('payroll-preview-deduction');
    const netPreview = document.getElementById('payroll-preview-net');
    const autoFetchMessage = document.getElementById('payroll-auto-fetch-message');

    function asNumber(value) {
        const parsed = parseFloat(value || '0');
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function renderPreview() {
        const basic = asNumber(basicInput?.value);
        const bonus = asNumber(bonusInput?.value);
        const deduction = asNumber(deductionInput?.value);
        const net = (basic + bonus) - deduction;

        basicPreview.textContent = basic.toFixed(2);
        bonusPreview.textContent = bonus.toFixed(2);
        deductionPreview.textContent = deduction.toFixed(2);
        netPreview.textContent = net.toFixed(2);
        netPreview.classList.toggle('text-danger', net < 0);
        netPreview.classList.toggle('text-success', net >= 0);
    }

    function fetchPreview() {
        if (!employeeInput?.value || !monthInput?.value || !yearInput?.value) {
            return;
        }

        const params = new URLSearchParams({
            employee_id: employeeInput.value,
            month: monthInput.value,
            year: yearInput.value
        });

        fetch('{{ route('hr.payroll.preview') }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!basicInput.value) {
                    basicInput.value = Number(data.basic_salary || 0).toFixed(2);
                }
                if (!deductionInput.value) {
                    deductionInput.value = Number(data.deduction || 0).toFixed(2);
                }
                autoFetchMessage.textContent = data.salary_structure_found
                    ? 'Salary structure found. Active advance deduction preview updated.'
                    : 'Salary structure not found. Advance deduction preview updated if available.';
                renderPreview();
            })
            .catch(function () {
                autoFetchMessage.textContent = 'Auto preview fetch failed.';
            });
    }

    [basicInput, bonusInput, deductionInput].forEach(function (input) {
        input?.addEventListener('input', renderPreview);
    });
    [employeeInput, monthInput, yearInput].forEach(function (input) {
        input?.addEventListener('change', fetchPreview);
    });

    renderPreview();
    fetchPreview();
});
</script>
