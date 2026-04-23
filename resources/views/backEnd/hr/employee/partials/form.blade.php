<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Employee Name *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee?->name) }}" required>
        @error('name')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Employee ID</label>
        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $employee?->employee_id) }}">
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Department</label>
        <select class="form-control" name="department_id">
            <option value="">Select..</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" @selected((string) old('department_id', $employee?->department_id) === (string) $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Linked User Login</label>
        <select class="form-control" name="user_id">
            <option value="">Select..</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) old('user_id', $employee?->user_id) === (string) $user->id)>{{ $user->name }}{{ $user->email ? ' (' . $user->email . ')' : '' }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Shift</label>
        <select class="form-control" name="shift_id">
            <option value="">Select..</option>
            @foreach($shifts as $shift)
                <option value="{{ $shift->id }}" @selected((string) old('shift_id', $employee?->shift_id) === (string) $shift->id)>{{ $shift->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee?->phone) }}">
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $employee?->email) }}">
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Designation</label>
        <select class="form-control" name="designation_id">
            <option value="">Select..</option>
            @foreach($designations as $designation)
                <option value="{{ $designation->id }}" @selected((string) old('designation_id', $employee?->designation_id) === (string) $designation->id)>{{ $designation->name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="designation" value="{{ old('designation', $employee?->designation) }}">
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Joining Date</label>
        <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', optional($employee?->joining_date)->format('Y-m-d')) }}">
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Salary</label>
        <input type="number" step="0.01" min="0" name="salary" class="form-control" value="{{ old('salary', $employee?->salary) }}">
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Annual Leave Days</label>
        <input type="number" min="0" name="annual_leave_days" class="form-control" value="{{ old('annual_leave_days', $employee?->annual_leave_days ?? 0) }}">
    </div>
</div>
<div class="col-md-4">
    <div class="form-group mb-3">
        <label class="form-label">Employment Type</label>
        <select class="form-control" name="employment_type">
            <option value="">Select..</option>
            <option value="full-time" @selected(old('employment_type', $employee?->employment_type) === 'full-time')>Full Time</option>
            <option value="part-time" @selected(old('employment_type', $employee?->employment_type) === 'part-time')>Part Time</option>
            <option value="contract" @selected(old('employment_type', $employee?->employment_type) === 'contract')>Contract</option>
            <option value="intern" @selected(old('employment_type', $employee?->employment_type) === 'intern')>Intern</option>
        </select>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="4">{{ old('address', $employee?->address) }}</textarea>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $employee?->notes) }}</textarea>
    </div>
</div>
<div class="col-md-3">
    <div class="form-group mb-3">
        <label class="d-block form-label">Status</label>
        <label class="switch">
            <input type="checkbox" name="status" value="1" @if(old('status', $employee?->status ?? 1)) checked @endif>
            <span class="slider round"></span>
        </label>
    </div>
</div>
