@extends('backEnd.layouts.master')
@section('title', 'Users Create')
@section('css')
    <link href="{{ asset('public/backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/backEnd') }}/assets/css/switchery.min.css" rel="stylesheet" type="text/css" />
    <style>
        .user-create-shell {
            padding-bottom: 28px;
        }

        .user-create-hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 22px;
            padding: 26px 28px;
            border: 1px solid #dbe7ff;
            border-radius: 24px;
            background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 42%, #fff8f1 100%);
            box-shadow: 0 26px 60px rgba(15, 23, 42, 0.08);
        }

        .user-create-hero::after {
            content: '';
            position: absolute;
            inset: auto -60px -80px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.18) 0%, rgba(59, 130, 246, 0) 72%);
            pointer-events: none;
        }

        .user-create-kicker {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.08);
            color: #1d4ed8;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .user-create-hero h3 {
            margin: 14px 0 8px;
            color: #0f172a;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .user-create-hero p {
            max-width: 760px;
            margin: 0;
            color: #475569;
            font-size: 0.96rem;
            line-height: 1.7;
        }

        .user-create-grid {
            display: grid;
            gap: 18px;
        }

        .user-card {
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .user-card-body {
            padding: 22px;
        }

        .user-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .user-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .user-card-copy {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.6;
            max-width: 720px;
        }

        .user-card-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: #fff4e8;
            border: 1px solid #fed7aa;
            color: #c2410c;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .user-form-card {
            height: 100%;
            padding: 16px;
            border: 1px solid #dbe4f0;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .user-form-card .form-label {
            color: #0f172a;
            font-weight: 700;
        }

        .user-form-card .form-control,
        .user-form-card .select2-container--default .select2-selection--single,
        .user-form-card .select2-container--default .select2-selection--multiple {
            border-color: #d6deea !important;
            border-radius: 14px !important;
            min-height: 44px;
            box-shadow: none;
        }

        .user-form-card .form-control:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14);
        }

        .user-form-note {
            display: block;
            margin-top: 8px;
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.6;
        }

        .access-panel {
            height: 100%;
            margin-bottom: 0;
            padding: 1rem 1rem 0.85rem;
            border: 1px solid #dbe4f0;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .access-panel-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .access-panel-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .access-panel-copy {
            margin: 0.25rem 0 0;
            color: #64748b;
            font-size: 0.875rem;
        }

        .access-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .access-note {
            margin-top: 0.8rem;
            padding: 0.75rem 0.9rem;
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            background: #fff;
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.55;
        }

        .permission-groups {
            display: grid;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .access-panel-scroll .permission-groups {
            max-height: 360px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .access-panel-scroll .permission-groups::-webkit-scrollbar {
            width: 8px;
        }

        .access-panel-scroll .permission-groups::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: #cbd5e1;
        }

        .access-panel-scroll .permission-groups::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .permission-group {
            padding: 0.85rem;
            border: 1px solid #dbe4f0;
            border-radius: 14px;
            background: #fff;
        }

        .permission-group-title {
            margin: 0 0 0.5rem;
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            text-transform: capitalize;
        }

        .permission-tag-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.55rem;
        }

        .permission-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.6rem 0.75rem;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #dbe4f0;
            color: #334155;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .permission-checkbox:hover {
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .permission-checkbox input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: #2563eb;
        }

        .permission-checkbox span {
            line-height: 1.4;
        }

        .permission-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.75rem;
        }

        .role-groups {
            margin-top: 0.75rem;
        }

        .user-status-card {
            padding: 16px;
            border: 1px solid #dbe4f0;
            border-radius: 18px;
            background: linear-gradient(180deg, #fffdf7 0%, #ffffff 100%);
        }

        .user-status-copy {
            margin: 0 0 10px;
            color: #64748b;
            font-size: 0.84rem;
            line-height: 1.6;
        }

        .user-submit-bar {
            position: sticky;
            bottom: 12px;
            z-index: 5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-top: 20px;
            padding: 16px 18px;
            border-radius: 18px;
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            box-shadow: 0 24px 45px rgba(15, 23, 42, 0.22);
        }

        .user-submit-bar h6 {
            margin: 0 0 4px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .user-submit-bar p {
            margin: 0;
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.84rem;
        }

        .user-submit-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .user-submit-btn {
            min-width: 150px;
            padding: 11px 18px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .user-secondary-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #fff;
            font-weight: 700;
            text-decoration: none;
        }

        @media (max-width: 767.98px) {
            .user-create-hero {
                padding: 22px 18px;
            }

            .user-create-hero h3 {
                font-size: 1.6rem;
            }

            .user-card-body {
                padding: 18px;
            }

            .user-submit-bar {
                position: static;
                flex-direction: column;
                align-items: stretch;
            }

            .user-submit-actions {
                width: 100%;
                justify-content: stretch;
            }

            .user-submit-actions>* {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
        }
    </style>
@endsection
@section('content')
    @php
        $permissionGroups = $permissions->groupBy(function ($permission) {
            return explode('-', $permission->name)[0];
        });
    @endphp
    <div class="container-fluid user-create-shell">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <a href="{{ route('users.index') }}" class="btn btn-primary rounded-pill">Manage</a>
                    </div>
                    <h4 class="page-title">Users Create</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="user-create-hero">
            <span class="user-create-kicker">Admin Access Setup</span>
            <h3>Create a New Admin User</h3>
            <p>Set up login credentials, assign the right roles, and use direct permissions only when this user needs access
                beyond the default role policy.</p>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('users.store') }}" method="POST" class="row user-create-grid"
                    data-parsley-validate="" enctype="multipart/form-data">
                    @csrf
                    <div class="col-12">
                        <div class="user-card">
                            <div class="user-card-body">
                                <div class="user-card-header">
                                    <div>
                                        <h5 class="user-card-title">Profile Details</h5>
                                        <p class="user-card-copy">Add the primary account information and secure credentials
                                            for the new user.</p>
                                    </div>
                                    <span class="user-card-badge">Step 1</span>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="user-form-card">
                                            <label for="name" class="form-label">Name *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                name="name" value="{{ old('name') }}" id="name" required="">
                                            <small class="user-form-note">Use the staff or admin display name.</small>
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="user-form-card">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="text" class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" id="email" required="">
                                            <small class="user-form-note">This email will be used as the login
                                                identifier.</small>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="user-form-card">
                                            <label for="password" class="form-label">Password *</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                value="{{ old('password') }}" id="password" required="">
                                            <small class="user-form-note">Choose a strong password for secure admin
                                                access.</small>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="user-form-card">
                                            <label for="confirm-password" class="form-label">Confirm Password *</label>
                                            <input type="password"
                                                class="form-control @error('confirm-password') is-invalid @enderror"
                                                name="confirm-password" value="{{ old('confirm-password') }}"
                                                id="confirm-password" required="">
                                            <small class="user-form-note">Re-enter the password to avoid setup
                                                mistakes.</small>
                                            @error('confirm-password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="user-card">
                            <div class="user-card-body">
                                <div class="user-card-header">
                                    <div>
                                        <h5 class="user-card-title">Access Control</h5>
                                        <p class="user-card-copy">Keep access organized through roles first, then apply
                                            direct permissions only when a special exception is required.</p>
                                    </div>
                                    <span class="user-card-badge">Step 2</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <div class="d-grid gap-3">
                            <div class="access-panel">
                                                <div class="access-panel-header">
                                                    <div>
                                                        <h5 class="access-panel-title">Role Assignment</h5>
                                                        <p class="access-panel-copy">Assign one or more roles. Role-based
                                                            permissions will be inherited automatically.</p>
                                                    </div>
                                                    <span class="access-chip">{{ $roles->count() }} roles</span>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label d-block">Role *</label>
                                                    <div class="permission-actions">
                                                        <button type="button"
                                                            class="btn btn-soft-primary btn-sm js-select-all-roles">Select
                                                            All</button>
                                                        <button type="button"
                                                            class="btn btn-soft-secondary btn-sm js-clear-roles">Clear</button>
                                                    </div>
                                                    <div class="permission-group role-groups">
                                                        <div class="permission-tag-list">
                                                            @foreach ($roles as $role)
                                                                <label class="permission-checkbox">
                                                                    <input type="checkbox" class="js-role-checkbox"
                                                                        name="roles[]" value="{{ $role->name }}"
                                                                        @checked(in_array($role->name, old('roles', [])))>
                                                                    <span>{{ $role->name }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @error('roles')
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="access-note">
                                                    Use roles as the primary access layer. You can assign multiple roles if
                                                    this user needs access across more than one responsibility.
                                                </div>
                                            </div>
                                            <div class="access-panel access-panel-scroll">
                                                <div class="access-panel-header">
                                                    <div>
                                                        <h5 class="access-panel-title">Direct Permissions</h5>
                                                        <p class="access-panel-copy">Optional. Use direct permissions only
                                                            for special exceptions beyond the assigned roles.</p>
                                                    </div>
                                                    <span class="access-chip">{{ $permissions->count() }}
                                                        permissions</span>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="form-label d-block">Permission Override</label>
                                                    <div class="permission-actions">
                                                        <button type="button"
                                                            class="btn btn-soft-primary btn-sm js-select-all-permissions">Select
                                                            All</button>
                                                        <button type="button"
                                                            class="btn btn-soft-secondary btn-sm js-clear-permissions">Clear</button>
                                                    </div>
                                                    <div class="permission-groups">
                                                        @foreach ($permissionGroups as $group => $groupPermissions)
                                                            <div class="permission-group">
                                                                <h6 class="permission-group-title">
                                                                    {{ str_replace('_', ' ', $group) }}</h6>
                                                                <div class="permission-tag-list">
                                                                    @foreach ($groupPermissions as $permission)
                                                                        <label class="permission-checkbox">
                                                                            <input type="checkbox"
                                                                                class="js-permission-checkbox"
                                                                                name="permissions[]"
                                                                                value="{{ $permission->name }}"
                                                                                @checked(in_array($permission->name, old('permissions', [])))>
                                                                            <span>{{ $permission->name }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @error('permissions')
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                    @error('permissions.*')
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="d-grid gap-3">
                                            <div class="user-card h-100">
                                                <div class="user-card-body">
                                                    <div class="user-card-header">
                                                        <div>
                                                            <h5 class="user-card-title">Media and Status</h5>
                                                            <p class="user-card-copy">Add an avatar if needed and decide
                                                                whether the account should be active immediately.</p>
                                                        </div>
                                                        <span class="user-card-badge">Step 3</span>
                                                    </div>
                                                    <div class="d-grid gap-3">
                                                        <div class="user-form-card">
                                                            @include(
                                                                'backEnd.category.partials.media-field',
                                                                [
                                                                    'field' => 'image',
                                                                    'label' => 'User Image',
                                                                    'selectedMediaId' => old('image_media_id'),
                                                                    'currentUrl' => '',
                                                                    'currentPath' => '',
                                                                ]
                                                            )
                                                        </div>
                                                        <div class="user-status-card h-100">
                                                            <label for="status" class="d-block">Status</label>
                                                            <p class="user-status-copy">Keep this enabled if the user
                                                                should be able to sign in right after creation.</p>
                                                            <label class="switch">
                                                                <input type="checkbox" value="1" name="status"
                                                                    checked>
                                                                <span class="slider round"></span>
                                                            </label>
                                                            @error('status')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="user-submit-bar">
                            <div>
                                <h6>Ready to create this user?</h6>
                                <p>Review roles and direct permissions carefully before saving the account.</p>
                            </div>
                            <div class="user-submit-actions">
                                <a href="{{ route('users.index') }}" class="user-secondary-btn">Back to Manage</a>
                                <input type="submit" class="user-submit-btn" value="Create User">
                            </div>
                        </div>
                    </div>

                </form>

                @include('backEnd.category.partials.media-picker-modal')
            </div> <!-- end col-->
        </div>
    </div>
@endsection


@section('script')
    <script src="{{ asset('public/backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ asset('public/backEnd/') }}/assets/js/switchery.min.js"></script>
    <script>
        $(document).ready(function() {
            var elem = document.querySelector('.js-switch');
            if (elem) {
                new Switchery(elem);
            }

            $('.js-select-all-permissions').on('click', function() {
                var checkboxes = $('.js-permission-checkbox');
                var shouldSelect = checkboxes.filter(':checked').length !== checkboxes.length;
                checkboxes.prop('checked', shouldSelect);
            });

            $('.js-clear-permissions').on('click', function() {
                $('.js-permission-checkbox').prop('checked', false);
            });

            $('.js-select-all-roles').on('click', function() {
                var checkboxes = $('.js-role-checkbox');
                var shouldSelect = checkboxes.filter(':checked').length !== checkboxes.length;
                checkboxes.prop('checked', shouldSelect);
            });

            $('.js-clear-roles').on('click', function() {
                $('.js-role-checkbox').prop('checked', false);
            });
        });
    </script>
@endsection
