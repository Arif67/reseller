@extends('backEnd.layouts.master')
@section('title','Product Edit')
@section('css')
<style>
  .increment_btn,
  .remove_btn,
  .btn-warning {
    margin-top: -17px;
    margin-bottom: 10px;
  }

  .variation-image-box {
    padding: 16px 18px;
    border: 1px dashed #cbd5e1;
    border-radius: 14px;
    background: #f8fafc;
  }

  .variation-image-box label {
    display: block;
    margin-bottom: 8px;
    color: #0f172a;
    font-weight: 600;
  }

  .variation-image-note {
    display: block;
    margin-top: 8px;
    color: #64748b;
    font-size: 12px;
  }

  .variation-image-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
  }

  .variation-image-preview-item,
  .variation-image-preview img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #dbe4f0;
    background: #fff;
    overflow: hidden;
  }

  .variation-image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .product-gallery-box {
    padding: 16px 18px;
    border: 1px dashed #bfdbfe;
    border-radius: 16px;
    background: linear-gradient(135deg, #f8fbff 0%, #eff6ff 100%);
  }

  .product-gallery-title {
    display: block;
    margin-bottom: 8px;
    color: #0f172a;
    font-weight: 700;
  }

  .product-gallery-note {
    display: block;
    margin-top: 8px;
    color: #64748b;
    font-size: 12px;
  }

  .product-gallery-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(96px, 1fr));
    gap: 12px;
    margin-top: 12px;
  }

  .product-gallery-preview-item {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    border-radius: 14px;
    border: 1px solid #dbe4f0;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
  }

  .product-gallery-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .product-gallery-dropzone {
    position: relative;
    padding: 18px 16px;
    border: 2px dashed #93c5fd;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.75);
    text-align: center;
    transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease;
  }

  .product-gallery-dropzone.is-dragover {
    border-color: #2563eb;
    background: #dbeafe;
    transform: translateY(-1px);
  }

  .product-gallery-dropzone input[type=file] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
  }

  .product-gallery-icon {
    width: 44px;
    height: 44px;
    margin: 0 auto 10px;
    border-radius: 14px;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    box-shadow: 0 10px 22px rgba(37, 99, 235, 0.22);
  }

  .product-gallery-dropzone strong {
    display: block;
    color: #0f172a;
    font-size: 14px;
    font-weight: 700;
  }

  .product-gallery-dropzone span {
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 12px;
  }

  .variation-pricing-mode-wrap {
    display: none;
    margin-bottom: 16px;
  }

  .variation-pricing-mode-hint,
  .shared-variation-pricing-note {
    display: block;
    margin-top: 8px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.5;
  }

  .shared-variation-pricing-note {
    display: none;
    margin-bottom: 12px;
    padding: 10px 12px;
    border: 1px solid #dbeafe;
    border-radius: 12px;
    background: #eff6ff;
    color: #1e3a8a;
  }


  .product-create-shell {
    padding-bottom: 22px;
  }

  .product-create-form {
    display: grid;
    gap: 16px;
  }

  .product-form-card {
    border: 1px solid #e2e8f0;
    border-radius: 22px;
    background: linear-gradient(180deg, #ffffff 0%, #fffaf6 100%);
    box-shadow: 0 22px 50px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }

  .product-form-card-body {
    padding: 20px;
  }

  .product-form-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }

  .product-form-card-title {
    margin: 0;
    color: #0f172a;
    font-size: 19px;
    font-weight: 800;
  }

  .product-form-card-text {
    margin: 4px 0 0;
    color: #64748b;
    font-size: 13px;
    line-height: 1.55;
    max-width: 720px;
  }

  .product-form-card-badge {
    padding: 6px 12px;
    border-radius: 999px;
    background: #fff1e8;
    border: 1px solid #fed7aa;
    color: #c2410c;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
  }

  .product-create-form .form-group,
  .product-create-form .variation-image-box,
  .product-create-form .product-gallery-box,
  .product-create-form .attribute-selector-box {
    height: 100%;
  }

  .product-create-form .form-label {
    color: #0f172a;
    font-weight: 700;
    margin-bottom: 7px;
    font-size: 13px;
  }

  .product-create-form .form-control,
  .product-create-form .select2-container--default .select2-selection--single {
    min-height: 42px;
    border-radius: 12px;
    border-color: #d6deea;
    box-shadow: none;
  }

  .product-create-form .form-control:focus,
  .product-create-form .select2-container--default.select2-container--focus .select2-selection--single,
  .product-create-form .select2-container--default .select2-selection--single:focus {
    border-color: #fb923c;
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.14);
  }

  .product-create-form .select2-container--default .select2-selection--single {
    display: flex;
    align-items: center;
    padding: 0 12px;
  }

  .product-create-form .select2-container {
    width: 100% !important;
  }

  .product-micro-note {
    display: block;
    margin-top: 6px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.5;
  }

  .product-type-panel {
    padding: 14px;
    border: 1px solid #dbe4f0;
    border-radius: 16px;
    background: linear-gradient(135deg, #fff8f1 0%, #fff3e8 100%);
  }

  .product-type-panel .form-label {
    font-size: 16px;
  }

  .product-pricing-grid {
    padding: 14px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: linear-gradient(180deg, #fffdfb 0%, #fff7ef 100%);
  }

  .product-textarea-wrap textarea {
    border-radius: 18px;
  }

  .daraz-accent-text {
    color: #f97316;
  }

  .variable-attribute-selector .attribute-selector-box {
    padding: 22px 24px;
    border: 1px solid #dbe4f0;
    border-radius: 18px;
    background: linear-gradient(135deg, #fbfdff 0%, #f3f8ff 100%);
    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.06);
  }

  .variable-attribute-selector .attribute-selector-kicker {
    display: inline-block;
    margin-bottom: 8px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(37, 99, 235, 0.1);
    color: #ea580c;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  .variable-attribute-selector .attribute-selector-box .form-label {
    display: block;
    margin-bottom: 10px;
    color: #0f172a;
    font-size: 18px;
    font-weight: 700;
  }

  .variable-attribute-selector .attribute-selector-hint {
    display: block;
    margin-bottom: 14px;
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
  }

  .variable-attribute-selector .select2-container--default .select2-selection--multiple {
    min-height: 60px !important;
    padding: 10px 12px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 14px !important;
    background: #fff !important;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
  }

  .media-setup-grid {
    display: grid;
    grid-template-columns: minmax(0, 0.86fr) minmax(0, 1.18fr) minmax(0, 0.68fr);
    gap: 10px;
    align-items: stretch;
  }

  .media-side-panel,
  .media-type-panel {
    height: 100%;
    padding: 14px;
    border: 1px solid #dbe4f0;
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #fff8f1 100%);
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.04);
  }

  .media-panel-kicker {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 999px;
    background: rgba(249, 115, 22, 0.12);
    color: #c2410c;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
  }

  .media-panel-title {
    margin: 8px 0 5px;
    color: #0f172a;
    font-size: 17px;
    font-weight: 800;
    line-height: 1.2;
  }

  .media-panel-copy {
    margin: 0 0 10px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.5;
  }

  .media-video-card {
    padding: 11px;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.9);
  }

  .media-insight-list {
    display: grid;
    gap: 6px;
    margin-top: 9px;
  }

  .media-insight-item {
    padding: 8px 10px;
    border-radius: 12px;
    background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%);
    border: 1px solid #fed7aa;
  }

  .media-insight-item strong {
    display: block;
    margin-bottom: 2px;
    color: #9a3412;
    font-size: 12px;
    font-weight: 800;
  }

  .media-insight-item span {
    display: block;
    color: #7c5a45;
    font-size: 11px;
    line-height: 1.4;
  }

  .product-gallery-box.product-gallery-box--feature {
    padding: 13px;
    border-style: solid;
    border-color: #bfdbfe;
    border-width: 1px;
    border-radius: 17px;
    background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    box-shadow: 0 12px 24px rgba(37, 99, 235, 0.06);
  }

  .product-gallery-topbar {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 9px;
    flex-wrap: wrap;
  }

  .product-gallery-caption {
    max-width: 500px;
  }

  .product-gallery-eyebrow {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 999px;
    background: rgba(37, 99, 235, 0.1);
    color: #1d4ed8;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
  }

  .product-gallery-subtitle {
    margin: 4px 0 0;
    color: #64748b;
    font-size: 11px;
    line-height: 1.45;
  }

  .product-gallery-meta {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  .product-gallery-meta span {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid #dbeafe;
    color: #1e40af;
    font-size: 10px;
    font-weight: 700;
  }

  .media-library-shell {
    margin-top: 9px;
    padding: 9px;
    border-radius: 13px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
  }

  .setup-choice-card {
    padding: 11px;
    border: 1px solid #fed7aa;
    border-radius: 14px;
    background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%);
  }

  .setup-choice-list {
    display: grid;
    gap: 6px;
    margin: 9px 0 0;
  }

  .setup-choice-list div {
    padding: 8px 10px;
    border-radius: 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
  }

  .setup-choice-list strong {
    display: block;
    margin-bottom: 2px;
    color: #0f172a;
    font-size: 12px;
    font-weight: 800;
  }

  .setup-choice-list span {
    display: block;
    color: #64748b;
    font-size: 11px;
    line-height: 1.35;
  }

  .media-setup-tip {
    margin-top: 9px;
    padding: 8px 10px;
    border-radius: 12px;
    background: rgba(37, 99, 235, 0.08);
    color: #1e3a8a;
    font-size: 11px;
    line-height: 1.4;
    border: 1px solid rgba(96, 165, 250, 0.28);
  }

  .product-seo-panel {
    padding: 16px;
    border: 1px solid #dbe4f0;
    border-radius: 18px;
    background: linear-gradient(135deg, #fff1e6 0%, #fff8f1 46%, #ffffff 100%);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
  }

  .product-seo-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
    gap: 20px;
    align-items: start;
  }

  .product-seo-fields,
  .product-seo-side {
    display: grid;
    gap: 16px;
  }

  .product-seo-field-card {
    padding: 18px;
    border-radius: 18px;
    border: 1px solid #fed7aa;
    background: rgba(255, 255, 255, 0.82);
  }

  .product-seo-mini-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    color: #c2410c;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .product-seo-preview {
    margin-top: 0;
    padding: 22px;
    border-radius: 20px;
    background: linear-gradient(180deg, #ffffff 0%, #fffaf5 100%);
    border: 1px solid #fed7aa;
    box-shadow: 0 16px 28px rgba(249, 115, 22, 0.08);
  }

  .product-seo-preview-label {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    background: #eff6ff;
    color: #1d4ed8;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 12px;
  }

  .product-seo-preview-title {
    color: #1a0dab;
    font-size: 24px;
    line-height: 1.3;
    font-weight: 500;
    margin-bottom: 6px;
  }

  .product-seo-preview-url {
    color: #188038;
    font-size: 14px;
    margin-bottom: 8px;
    word-break: break-all;
  }

  .product-seo-preview-desc {
    color: #4b5563;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
  }

  .product-seo-preview-meta {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-top: 14px;
    color: #6b7280;
    font-size: 12px;
    flex-wrap: wrap;
  }

  .product-seo-preview-stat strong {
    color: #111827;
    font-weight: 800;
  }

  .product-seo-checklist {
    padding: 18px;
    border-radius: 18px;
    border: 1px solid #fde68a;
    background: linear-gradient(180deg, #fffdfa 0%, #fff7ed 100%);
  }

  .product-seo-checklist h6 {
    margin: 0 0 12px;
    color: #111827;
    font-size: 15px;
    font-weight: 800;
  }

  .product-seo-checklist ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 10px;
  }

  .product-seo-checklist li {
    position: relative;
    padding-left: 18px;
    color: #4b5563;
    font-size: 13px;
    line-height: 1.5;
  }

  .product-seo-checklist li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 8px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
  }

  .product-switch-panel {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
  }

  .product-switch-card {
    padding: 14px;
    border: 1px solid #dbe4f0;
    border-radius: 16px;
    background: linear-gradient(180deg, #fffdfb 0%, #fff7ef 100%);
  }

  .product-switch-card label:first-child {
    margin-bottom: 8px;
    color: #0f172a;
    font-weight: 700;
  }

  .product-submit-bar {
    position: sticky;
    bottom: 10px;
    z-index: 5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-radius: 16px;
    border: 1px solid rgba(148, 163, 184, 0.22);
    background: linear-gradient(135deg, #2b2f33 0%, #111827 100%);
    box-shadow: 0 22px 45px rgba(15, 23, 42, 0.24);
    backdrop-filter: blur(14px);
  }

  .product-submit-bar h6 {
    margin: 0 0 2px;
    color: #fff;
    font-size: 14px;
    font-weight: 800;
  }

  .product-submit-bar p {
    margin: 0;
    color: rgba(255, 255, 255, 0.72);
    font-size: 12px;
  }

  .product-submit-btn {
    min-width: 156px;
    padding: 11px 18px;
    border: 0;
    border-radius: 999px;
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    box-shadow: 0 16px 32px rgba(249, 115, 22, 0.28);
  }

  .product-submit-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex-wrap: wrap;
  }

  .product-copy-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 150px;
    padding: 11px 18px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.28);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.08);
  }

  .product-copy-btn:hover {
    color: #fff;
    text-decoration: none;
    background: rgba(255, 255, 255, 0.14);
  }

  @media (max-width: 991px) {
    .media-setup-grid,
    .product-seo-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 767px) {
    .product-form-card-body {
      padding: 16px;
    }

    .product-submit-bar {
      flex-direction: column;
      align-items: stretch;
    }

    .product-submit-actions {
      width: 100%;
      flex-direction: column;
      align-items: stretch;
    }

    .product-copy-btn,
    .product-submit-btn {
      width: 100%;
    }
  }

</style>
<link href="{{asset('backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('backEnd')}}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<div class="container-fluid product-create-shell">
  <div class="row justify-content-center p-3">
    <div class="col-lg-12">
      <form action="{{route('products.update')}}" method="POST" class="row product-create-form" data-parsley-validate="" enctype="multipart/form-data" name="editForm">
        @csrf
        <input type="hidden" value="{{$edit_data->id}}" name="id" />

        <div class="product-form-card">
          <div class="product-form-card-body">
            <div class="product-form-card-header">
              <div>
                <h3 class="product-form-card-title">Basic Information</h3>
                <p class="product-form-card-text">Set the product name, category, brand, and related taxonomy details here.</p>
              </div>
              <span class="product-form-card-badge">Step 1</span>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group mb-3">
                  <label for="name" class="form-label">Product Name *</label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{$edit_data->name}}" id="name" required />
                  @error('name')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group mb-3">
                  <label for="primary_category_id" class="form-label">Primary Category *</label>
                  <select class="form-control select2 @error('primary_category_id') is-invalid @enderror" name="primary_category_id" id="primary_category_id" required>
                    <option value="">Select...</option>
                    @foreach($categories as $category)
                    <option value="{{$category->id}}" @selected(($primaryCategoryId ?? $edit_data->category_id) == $category->id)>{{$category->name}}</option>
                    @endforeach
                  </select>
                  @error('primary_category_id')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>
              <div class="col-sm-3">
                <div class="form-group mb-3">
                  <label for="category_ids" class="form-label">Extra Categories</label>
                  <select class="form-control select2 @error('category_ids') is-invalid @enderror @error('category_ids.*') is-invalid @enderror" name="category_ids[]" id="category_ids" multiple>
                    @foreach($categories as $category)
                    <option value="{{$category->id}}" @selected(collect($selectedCategoryIds ?? [])->contains($category->id))>{{$category->name}}</option>
                    @endforeach
                  </select>
                  <small class="text-muted">Primary category ছাড়াও এই product যেসব category-তে দেখাতে চান সেগুলো এখানে দিন।</small>
                  @error('category_ids')
                  <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                  @error('category_ids.*')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group mb-3">
                  <label for="subcategory_id" class="form-label">Subcategories (Optional)</label>
                  <select class="form-control select2 @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id">
                    <option value="">Select...</option>
                    @foreach($subcategory as $value)
                    <option value="{{$value->id}}" @selected($edit_data->subcategory_id == $value->id)>{{$value->subcategoryName}}</option>
                    @endforeach
                  </select>
                  @error('subcategory_id')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group mb-3">
                  <label for="childcategory_id" class="form-label">Child Categories (Optional)</label>
                  <select class="form-control select2 @error('childcategory_id') is-invalid @enderror" id="childcategory_id" name="childcategory_id">
                    <option value="">Select...</option>
                    @foreach($childcategory as $value)
                    <option value="{{$value->id}}" @selected($edit_data->childcategory_id == $value->id)>{{$value->childcategoryName}}</option>
                    @endforeach
                  </select>
                  @error('childcategory_id')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group mb-3">
                  <label for="brand_id" class="form-label">Brand</label>
                  <select class="form-control select2 @error('brand_id') is-invalid @enderror" name="brand_id" id="brand_id">
                    <option value="">Select...</option>
                    @foreach($brands as $value)
                    <option value="{{$value->id}}" @selected($edit_data->brand_id == $value->id)>{{$value->name}}</option>
                    @endforeach
                  </select>
                  @error('brand_id')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group mb-3">
                  <label for="vendor_id" class="form-label">Vendor (Optional)</label>
                  <select class="form-control select2 @error('vendor_id') is-invalid @enderror" name="vendor_id" id="vendor_id">
                    <option value="">Platform (own product)</option>
                    @foreach(($vendors ?? []) as $vendor)
                    <option value="{{$vendor->id}}" @selected($edit_data->vendor_id == $vendor->id)>{{$vendor->shop_name}}</option>
                    @endforeach
                  </select>
                  <small class="text-muted">Kon vendor-er product. Khali = platform-er nijer.</small>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group mb-3">
                  <label for="pro_barcode" class="form-label">Product Barcode (Optional)</label>
                  <div class="input-group">
                    <input type="text" class="form-control @error('pro_barcode') is-invalid @enderror barcode-input" name="pro_barcode" value="{{ old('pro_barcode', $edit_data->pro_barcode) }}" id="pro_barcode" placeholder="Scan or enter product barcode" />
                    <button type="button" class="btn btn-outline-secondary barcode-generate-btn" data-target="#pro_barcode">Generate</button>
                  </div>
                  <small class="text-muted">This barcode will be used for POS scanning on standard products. Use a unique value.</small>
                  @error('pro_barcode')
                  <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        @php
          $variationPricingMode = old('variation_pricing_mode', $edit_data->type == 0 ? ($edit_data->variation_pricing_mode ?: 'different') : 'same');
          $nextVariableIndex = $variables->count();
        @endphp

        <div class="product-form-card">
          <div class="product-form-card-body">
            <div class="product-form-card-header">
              <div>
                <h3 class="product-form-card-title">Media & Product Setup</h3>
                <p class="product-form-card-text">Video, gallery upload, media library selection, and product type are grouped here.</p>
              </div>
              <span class="product-form-card-badge">Step 2</span>
            </div>
            <div class="media-setup-grid">
              <div class="media-side-panel">
                <span class="media-panel-kicker">Story Layer</span>
                <h4 class="media-panel-title">Present the product with strong visuals</h4>
                <p class="media-panel-copy">Keeping video, gallery, and product type together makes the upload flow easier to manage.</p>

                <div class="media-video-card">
                  <div class="form-group mb-0">
                    <label for="pro_video" class="form-label">Product Video (Optional)</label>
                    <small class="product-micro-note">Add a YouTube link or a short demo video URL.</small>
                    <input type="url" class="form-control @error('pro_video') is-invalid @enderror" name="pro_video" value="{{ $edit_data->pro_video }}" id="pro_video" placeholder="https://www.youtube.com/watch?v=..." />
                    @error('pro_video')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>
                </div>

                <div class="media-insight-list">
                  <div class="media-insight-item">
                    <strong>Video tip</strong>
                    <span>A short demo helps customers understand the product faster.</span>
                  </div>
                  <div class="media-insight-item">
                    <strong>Gallery tip</strong>
                    <span>Front, side, and detail shots usually provide enough coverage.</span>
                  </div>
                </div>
              </div>

              <div class="product-gallery-box product-gallery-box--feature">
                <div class="product-gallery-topbar">
                  <div class="product-gallery-caption">
                    <span class="product-gallery-eyebrow">Gallery Upload</span>
                    <label for="image" class="product-gallery-title mb-0">Product Gallery Images *</label>
                    <p class="product-gallery-subtitle">Manage uploads and media library selection from this section.</p>
                  </div>
                  <div class="product-gallery-meta">
                    <span>Multi image</span>
                    <span>Live preview</span>
                    <span>Library select</span>
                  </div>
                </div>

                <div class="product-gallery-dropzone">
                  <div class="product-gallery-icon">+</div>
                  <strong>Drop image here or click to upload</strong>
                  <span>You can select multiple JPG, PNG, or WEBP images</span>
                  <small>Best: same ratio, clean background</small>
                  <input type="file" name="image[]" multiple accept="image/*" class="form-control @error('image') is-invalid @enderror" />
                </div>
                @error('image')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <small class="product-gallery-note">Selected images will appear in the preview below. A 4:5 ratio works best for the main catalog card.</small>

                <div class="media-library-shell">
                  @include('backEnd.product.partials.media-selector', [
                    'title' => 'Select Product Media',
                    'inputName' => 'selected_media_ids',
                    'selectedIds' => $selectedMediaIds ?? [],
                    'mediaLibrary' => $mediaLibrary,
                  ])
                </div>

                <div class="product-gallery-preview">
                  @foreach($edit_data->media as $mediaItem)
                  @php($mediaUrl = \Illuminate\Support\Str::startsWith($mediaItem->path, ['http://', 'https://']) ? $mediaItem->path : asset($mediaItem->path))
                  <div class="product-gallery-preview-item">
                    <img src="{{ $mediaUrl }}" class="edit-image" alt="{{ $mediaItem->alt ?? $mediaItem->name ?? 'Product image' }}" />
                  </div>
                  @endforeach
                </div>
              </div>

              <div class="media-type-panel">
                <span class="media-panel-kicker">Setup Choice</span>
                <h4 class="media-panel-title">Set the product structure first</h4>
                <p class="media-panel-copy">Selecting a type will show the relevant pricing section below.</p>

                <div class="setup-choice-card">
                  <div class="form-group mb-0">
                    <label for="type" class="form-label">Product Type</label>
                    <select class="form-control select2 @error('type') is-invalid @enderror" id="product_type" name="type">
                      <option value="1" @selected($edit_data->type == 1)>Normal Product</option>
                      <option value="0" @selected($edit_data->type == 0)>Variation Product</option>
                    </select>
                    @error('type')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                  </div>
                </div>

                <div class="setup-choice-list">
                  <div>
                    <strong>Normal Product</strong>
                    <span>Single stock and price.</span>
                  </div>
                  <div>
                    <strong>Variation Product</strong>
                    <span>Manage color and size variations.</span>
                  </div>
                </div>

                <div class="media-setup-tip">Selecting the product type first keeps the pricing section below cleaner.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="product-form-card">
          <div class="product-form-card-body">
            <div class="product-form-card-header">
              <div>
                <h3 class="product-form-card-title">Pricing & Variation Setup</h3>
                <p class="product-form-card-text">For a standard product, enter the price and stock directly. For a variation product, choose same-price or per-variation pricing mode.</p>
              </div>
              <span class="product-form-card-badge">Step 3</span>
            </div>
            <div class="product-type-panel mb-3">
              <small class="product-micro-note">Changing the product type will automatically show or hide the relevant pricing section below.</small>
              <div class="variation-pricing-mode-wrap">
                <label for="variation_pricing_mode" class="form-label">Variation Pricing Mode</label>
                <select class="form-control select2" id="variation_pricing_mode" name="variation_pricing_mode">
                  <option value="same" @selected($variationPricingMode === 'same')>Same price for all variations</option>
                  <option value="different" @selected($variationPricingMode === 'different')>Different price per variation</option>
                </select>
                <small class="variation-pricing-mode-hint">In same-price mode, the main product pricing will be used for all variations. In different-price mode, each variation can have its own price.</small>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-12 variable-attribute-selector" @if($edit_data->type != 0) style="display:none" @endif>
                <div class="form-group mb-3 attribute-selector-box">
                  <span class="attribute-selector-kicker">Variation Setup</span>
                  <label for="selected_attribute_ids" class="form-label">Attributes For This Product</label>
                  <small class="attribute-selector-hint">Select which attributes should appear for product variations here. Example: for a Panjabi, choosing Color and Size will show those two attributes in variations.</small>
                  <select class="form-control select2 @error('selected_attribute_ids') is-invalid @enderror" id="selected_attribute_ids" name="selected_attribute_ids[]" multiple data-placeholder="Select attributes for this product">
                    @foreach($attributes as $attribute)
                    <option value="{{$attribute->id}}" @selected(in_array($attribute->id, old('selected_attribute_ids', $selectedAttributeIds ?? [])))>{{$attribute->title}}</option>
                    @endforeach
                  </select>
                  <small class="text-muted">You can select multiple attributes. Once selected, they will appear as tags.</small>
                  @error('selected_attribute_ids')
                  <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>
              </div>

              <div class="normal_product shared_pricing_panel" @if($edit_data->type == 0 && $variationPricingMode !== 'same') style="display:none" @endif>
                <div class="row product-pricing-grid">
                  <div class="col-sm-3">
                    <div class="form-group mb-3">
                      <label for="purchase_price" class="form-label">Purchase Price *</label>
                      <input type="text" class="form-control @error('purchase_price') is-invalid @enderror" name="purchase_price" value="{{ $edit_data->purchase_price }}" id="purchase_price" />
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group mb-3">
                      <label for="old_price" class="form-label">Old Price</label>
                      <input type="text" class="form-control @error('old_price') is-invalid @enderror" name="old_price" value="{{$edit_data->old_price}}" id="old_price" />
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group mb-3">
                      <label for="new_price" class="form-label">New Price *</label>
                      <input type="text" class="form-control @error('new_price') is-invalid @enderror" name="new_price" value="{{ $edit_data->new_price }}" id="new_price" />
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group mb-3">
                      <label for="wholesale_price" class="form-label">Wholesale Price (Reseller)</label>
                      <input type="text" class="form-control @error('wholesale_price') is-invalid @enderror" name="wholesale_price" value="{{ $edit_data->wholesale_price }}" id="wholesale_price" />
                      <small class="text-muted">Reseller panel e ei price dekhabe.</small>
                    </div>
                  </div>
                  <div class="col-sm-3 normal-stock-field">
                    <div class="form-group mb-3">
                      <label for="stock" class="form-label">Stock *</label>
                      <input type="text" class="form-control @error('stock') is-invalid @enderror" name="stock" value="{{$edit_data->stock}}" id="stock" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="variable_product product-pricing-grid" @if($edit_data->type != 0) style="display:none" @endif>
                <div class="shared-variation-pricing-note">The main product price will apply to all variations. Use the variation rows to manage stock and options.</div>
                @foreach($variables as $variable)
                <input type="hidden" value="{{$variable->id}}" name="up_id[]">
                <div class="row mb-2 variable-row" data-row-index="{{$loop->index}}">
                  @include('backEnd.product.partials.attribute-selects', [
                    'attributes' => $attributes,
                    'inputNamePrefix' => 'up_attribute_values[' . $loop->index . ']',
                    'selectedValues' => $variableSelections[$variable->id] ?? [],
                    'selectedAttributeIds' => old('selected_attribute_ids', $selectedAttributeIds ?? []),
                  ])
                  <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields">
                    <div class="form-group"><label class="form-label">Purchase Price *</label><input type="text" class="form-control" name="up_purchase_prices[]" value="{{$variable->purchase_price}}" /></div>
                  </div>
                  <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields">
                    <div class="form-group"><label class="form-label">Old Price</label><input type="text" class="form-control" name="up_old_prices[]" value="{{$variable->old_price}}" /></div>
                  </div>
                  <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields">
                    <div class="form-group"><label class="form-label">New Price *</label><input type="text" class="form-control" name="up_new_prices[]" value="{{$variable->new_price}}" /></div>
                  </div>
                  <div class="col-lg-2 col-md-3 col-sm-4">
                    <div class="form-group"><label class="form-label">Stock *</label><input type="text" class="form-control" name="up_stocks[]" value="{{$variable->stock}}"></div>
                  </div>
                  <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="form-group"><label class="form-label">Variation Barcode</label><div class="input-group"><input type="text" class="form-control @error('up_barcodes.' . $loop->index) is-invalid @enderror barcode-input" name="up_barcodes[]" value="{{ old('up_barcodes.' . $loop->index, $variable->barcode) }}" placeholder="Scan or enter variation barcode"><button type="button" class="btn btn-outline-secondary barcode-generate-btn">Generate</button></div></div>
                  </div>
                  <div class="col-sm-12 mb-3">
                    <div class="variation-image-box">
                      <label>Variation Images (optional)</label>
                      <div class="input-group control-group"><input type="file" name="up_images[{{$loop->index}}][]" multiple accept="image/*" class="form-control" /></div>
                      <small class="variation-image-note">Uploading new images will replace the existing images for this variation. Maximum 2 images. Recommended size: 1200 x 1500px (4:5).</small>
                      @include('backEnd.product.partials.media-selector', [
                        'title' => 'Select Variation Media',
                        'inputName' => 'up_selected_variable_media_ids[' . $loop->index . ']',
                        'selectedIds' => $existingVariableMediaIds[$loop->index] ?? [],
                        'mediaLibrary' => $mediaLibrary,
                      ])
                      @if($variable->media->count() > 0)
                      <div class="variation-image-preview">
                        @foreach($variable->media as $mediaItem)
                        @php($mediaUrl = \Illuminate\Support\Str::startsWith($mediaItem->path, ['http://', 'https://']) ? $mediaItem->path : asset($mediaItem->path))
                        <img src="{{ $mediaUrl }}" alt="Variation image">
                        @endforeach
                      </div>
                      @endif
                    </div>
                  </div>
                  <div class="input-group-btn"><a href="{{route('products.price.destroy',['id'=>$variable->id])}}" class="btn btn-danger btn-xs text-white" onclick="return confirm('Are you want delete this?')" type="button"><i class="mdi mdi-close"></i></a></div>
                </div>
                @endforeach

                <div class="row mt-3 variable-row" data-row-index="{{$nextVariableIndex}}">
                  @include('backEnd.product.partials.attribute-selects', [
                    'attributes' => $attributes,
                    'inputNamePrefix' => 'attribute_values[' . $nextVariableIndex . ']',
                    'selectedValues' => [],
                    'selectedAttributeIds' => old('selected_attribute_ids', $selectedAttributeIds ?? []),
                  ])
                  <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields"><div class="form-group"><label class="form-label">Purchase Price *</label><input type="text" class="form-control" name="purchase_prices[{{$nextVariableIndex}}]" value="{{ old('purchase_prices.' . $nextVariableIndex) }}" /></div></div>
                  <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields"><div class="form-group"><label class="form-label">Old Price</label><input type="text" class="form-control" name="old_prices[{{$nextVariableIndex}}]" value="{{ old('old_prices.' . $nextVariableIndex) }}" /></div></div>
                  <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields"><div class="form-group"><label class="form-label">New Price *</label><input type="text" class="form-control" name="new_prices[{{$nextVariableIndex}}]" value="{{ old('new_prices.' . $nextVariableIndex) }}" /></div></div>
                  <div class="col-lg-2 col-md-3 col-sm-4"><div class="form-group"><label class="form-label">Stock *</label><input type="text" class="form-control" name="stocks[{{$nextVariableIndex}}]" value="{{ old('stocks.' . $nextVariableIndex) }}"></div></div>
                  <div class="col-lg-3 col-md-4 col-sm-6"><div class="form-group"><label class="form-label">Variation Barcode</label><div class="input-group"><input type="text" class="form-control @error('barcodes.' . $nextVariableIndex) is-invalid @enderror barcode-input" name="barcodes[{{$nextVariableIndex}}]" value="{{ old('barcodes.' . $nextVariableIndex) }}" placeholder="Scan or enter variation barcode"><button type="button" class="btn btn-outline-secondary barcode-generate-btn">Generate</button></div></div></div>
                  <div class="col-sm-12 mb-3">
                    <div class="variation-image-box">
                      <label>Variation Images (optional)</label>
                      <div class="input-group control-group"><input type="file" name="images[{{$nextVariableIndex}}][]" multiple accept="image/*" class="form-control" /></div>
                      <small class="variation-image-note">You can upload up to 2 images for each variation. Recommended size: 1200 x 1500px (4:5).</small>
                      @include('backEnd.product.partials.media-selector', [
                        'title' => 'Select Variation Media',
                        'inputName' => 'selected_variable_media_ids[' . ($nextVariableIndex ?? 0) . ']',
                        'selectedIds' => $selectedVariableMediaIds[$nextVariableIndex ?? 0] ?? [],
                        'mediaLibrary' => $mediaLibrary,
                      ])
                      <div class="variation-image-preview"></div>
                    </div>
                  </div>
                  <div class="input-group-btn"><button class="btn btn-success increment_btn btn-xs text-white" type="button"><i class="fa fa-plus"></i></button></div>
                </div>

                <div class="clone_variable" style="display:none">
                  <div class="row variable-row increment_control mt-3" data-row-index="__INDEX__">
                    @include('backEnd.product.partials.attribute-selects', [
                      'attributes' => $attributes,
                      'inputNamePrefix' => 'attribute_values[__INDEX__]',
                      'selectedValues' => [],
                    ])
                    <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields"><div class="form-group"><label class="form-label">Purchase Price *</label><input type="text" class="form-control" name="purchase_prices[__INDEX__]" /></div></div>
                    <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields"><div class="form-group"><label class="form-label">Old Price</label><input type="text" class="form-control" name="old_prices[__INDEX__]" /></div></div>
                    <div class="col-lg-2 col-md-3 col-sm-4 variable-price-fields"><div class="form-group"><label class="form-label">New Price *</label><input type="text" class="form-control" name="new_prices[__INDEX__]" /></div></div>
                    <div class="col-lg-2 col-md-3 col-sm-4"><div class="form-group"><label class="form-label">Stock *</label><input type="text" class="form-control" name="stocks[__INDEX__]" /></div></div>
                    <div class="col-lg-3 col-md-4 col-sm-6"><div class="form-group"><label class="form-label">Variation Barcode</label><div class="input-group"><input type="text" class="form-control barcode-input" name="barcodes[__INDEX__]" placeholder="Scan or enter variation barcode" /><button type="button" class="btn btn-outline-secondary barcode-generate-btn">Generate</button></div></div></div>
                    <div class="col-sm-12 mb-3">
                      <div class="variation-image-box">
                        <label>Variation Images (optional)</label>
                        <div class="input-group control-group"><input type="file" name="images[__INDEX__][]" multiple accept="image/*" class="form-control" /></div>
                        <small class="variation-image-note">You can upload up to 2 images for each variation. Recommended size: 1200 x 1500px (4:5).</small>
                        @include('backEnd.product.partials.media-selector', [
                          'title' => 'Select Variation Media',
                          'inputName' => 'selected_variable_media_ids[__INDEX__]',
                          'selectedIds' => [],
                          'mediaLibrary' => $mediaLibrary,
                        ])
                        <div class="variation-image-preview"></div>
                      </div>
                    </div>
                    <div class="input-group-btn"><button class="btn btn-danger remove_btn btn-xs text-white" type="button"><i class="fa fa-trash"></i></button></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="product-form-card">
          <div class="product-form-card-body">
            <div class="product-form-card-header">
              <div>
                <h3 class="product-form-card-title">Description & Care</h3>
                <p class="product-form-card-text">Maintain clean customer-facing content here.</p>
              </div>
              <span class="product-form-card-badge">Step 4</span>
            </div>
            <div class="row">
              <div class="col-sm-12 mb-3 product-textarea-wrap">
                <div class="form-group">
                  <label for="description" class="form-label">Description</label>
                  <textarea name="description" rows="6" class="summernote form-control @error('description') is-invalid @enderror">{{$edit_data->description}}</textarea>
                  @error('description')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                </div>
              </div>
              <div class="col-sm-12 mb-3">
                <div class="form-group">
                  <label for="care_tips" class="form-label">Care Tips</label>
                  <textarea name="care_tips" rows="6" class="summernote form-control @error('care_tips') is-invalid @enderror">{{$edit_data->care_tips}}</textarea>
                  @error('care_tips')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="product-form-card">
          <div class="product-form-card-body">
            <div class="product-form-card-header">
              <div>
                <h3 class="product-form-card-title">SEO Preview & Search Snippet</h3>
                <p class="product-form-card-text">Use the live search preview to manage the SEO title, description, and keywords/tags.</p>
              </div>
              <span class="product-form-card-badge">Step 5</span>
            </div>
            <div class="product-seo-panel">
              <div class="product-seo-grid">
                <div class="product-seo-fields">
                  <div class="product-seo-field-card">
                    <span class="product-seo-mini-label">Search Title</span>
                    <div class="form-group mb-0">
                      <label for="meta_title" class="form-label">SEO Title</label>
                      <input type="text" class="form-control @error('meta_title') is-invalid @enderror" name="meta_title" id="meta_title" value="{{ old('meta_title', $edit_data->meta_title) }}" maxlength="255" placeholder="Write an SEO title">
                      <small class="product-micro-note">If left empty, the product name will be used automatically. A good range is 50 to 60 characters.</small>
                      @error('meta_title')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                    </div>
                  </div>
                  <div class="product-seo-field-card">
                    <span class="product-seo-mini-label">Discoverability</span>
                    <div class="form-group mb-3">
                      <label for="meta_tag" class="form-label">SEO Keywords / Tags</label>
                      <input type="text" class="form-control @error('meta_tag') is-invalid @enderror" name="meta_tag" id="meta_tag" value="{{ old('meta_tag', $edit_data->meta_tag) }}" placeholder="panjabi, cotton, men fashion">
                      <small class="product-micro-note">Separate keywords or tags with commas.</small>
                      @error('meta_tag')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                    </div>
                    <div class="form-group mb-0">
                      <label for="meta_description" class="form-label">SEO Description</label>
                      <textarea class="form-control @error('meta_description') is-invalid @enderror" name="meta_description" id="meta_description" rows="5" placeholder="Write the short description shown in search results">{{ old('meta_description', $edit_data->meta_description) }}</textarea>
                      <small class="product-micro-note">Short, meaningful, and compelling descriptions usually perform better.</small>
                      @error('meta_description')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                    </div>
                  </div>
                </div>
                <div class="product-seo-side">
                  <div class="product-seo-preview">
                    <span class="product-seo-preview-label">Search Preview</span>
                    <div class="product-seo-preview-title" id="seoPreviewTitle">{{ old('meta_title', $edit_data->meta_title ?: $edit_data->name) }}</div>
                    <div class="product-seo-preview-url" id="seoPreviewUrl">{{ url('/product') }}/<span id="seoPreviewSlug">{{ $edit_data->slug }}</span></div>
                    <p class="product-seo-preview-desc" id="seoPreviewDescription">{{ old('meta_description', $edit_data->meta_description ?: 'A concise SEO description will help customers understand the product from search results.') }}</p>
                    <div class="product-seo-preview-meta">
                      <span class="product-seo-preview-stat"><strong id="seoTitleCount">0</strong> title chars</span>
                      <span class="product-seo-preview-stat"><strong id="seoDescriptionCount">0</strong> description chars</span>
                    </div>
                  </div>
                  <div class="product-seo-checklist">
                    <h6>Quick Optimization Checklist</h6>
                    <ul>
                      <li>Include the main product keyword in the title</li>
                      <li>Mention the product material, use case, and value in the description</li>
                      <li>Use natural phrases instead of keyword stuffing</li>
                      <li>Keep the product name and SEO title closely aligned</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="product-form-card">
          <div class="product-form-card-body">
            <div class="product-form-card-header">
              <div>
                <h3 class="product-form-card-title">Publish Settings</h3>
                <p class="product-form-card-text">Visibility and feature toggles are grouped here.</p>
              </div>
              <span class="product-form-card-badge">Step 6</span>
            </div>
            <div class="product-switch-panel">
              <div class="col-sm-3 mb-3 product-switch-card">
                <div class="form-group">
                  <label for="status" class="d-block">Status</label>
                  <label class="switch"><input type="checkbox" value="1" name="status" @if($edit_data->status==1) checked @endif><span class="slider round"></span></label>
                </div>
              </div>
              <div class="col-sm-3 mb-3 product-switch-card">
                <div class="form-group">
                  <label for="is_catalog" class="d-block">Facebook Catalog</label>
                  <label class="switch"><input type="checkbox" value="1" name="is_catalog" @if($edit_data->is_catalog==1) checked @endif><span class="slider round"></span></label>
                </div>
              </div>
              <div class="col-sm-3 mb-3 product-switch-card">
                <div class="form-group">
                  <label for="topsale" class="d-block">Hot Deals</label>
                  <label class="switch"><input type="checkbox" value="1" name="topsale" @if($edit_data->topsale==1) checked @endif><span class="slider round"></span></label>
                </div>
              </div>
              <div class="col-sm-3 mb-3 product-switch-card">
                <div class="form-group">
                  <label for="feature_product" class="d-block">Featured Product</label>
                  <label class="switch"><input type="checkbox" value="1" name="feature_product" @if($edit_data->feature_product==1) checked @endif><span class="slider round"></span></label>
                </div>
              </div>
              <div class="col-sm-3 mb-3 product-switch-card">
                <div class="form-group">
                  <label for="free_shipping" class="d-block">Digital Product</label>
                  <label class="switch"><input type="checkbox" value="1" name="free_shipping" @if($edit_data->free_shipping==1) checked @endif><span class="slider round"></span></label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="product-submit-bar">
          <div>
            <h6>Ready to update?</h6>
            <p>Review the form and save to update the product information.</p>
          </div>
          <div class="product-submit-actions">
            <a href="{{route('products.barcode_labels',$edit_data->id)}}" class="product-copy-btn" target="_blank">Print Barcodes</a>
            <a href="{{route('products.copy',$edit_data->id)}}" class="product-copy-btn" onclick="return confirm('Create a copy of this product?')">Copy Product</a>
            <input type="submit" class="product-submit-btn" value="Update Product" />
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@include('backEnd.product.partials.media-picker-modal')
@endsection
@section('script')
<script src="{{asset('backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('backEnd/')}}/assets/libs//summernote/summernote-lite.min.js"></script>
<script>
  $(".summernote").summernote({
    placeholder: "Enter Your Text Here",
  });
</script>
<script>
  function toggleVariableAttributeFields() {
    var selectedAttributes = ($('#selected_attribute_ids').val() || []).map(String);
    $('.variable-attribute-field').each(function() {
      var attributeId = String($(this).data('attribute-id'));
      var shouldShow = selectedAttributes.includes(attributeId);
      $(this).toggle(shouldShow);
      $(this).find('select').prop('disabled', !shouldShow);
      if (!shouldShow) {
        $(this).find('select').val('');
      }
    });
  }

  function toggleVariationPricingMode() {
    var isVariable = $('#product_type').val() == 0;
    var pricingMode = $('#variation_pricing_mode').val() || 'different';
    var showSharedPricing = !isVariable || pricingMode === 'same';
    var showVariablePricing = isVariable && pricingMode === 'different';

    $('.variation-pricing-mode-wrap').toggle(isVariable);
    $('.shared_pricing_panel').toggle(showSharedPricing);
    $('.normal-stock-field').toggle(!isVariable);
    $('.shared-variation-pricing-note').toggle(isVariable && pricingMode === 'same');
    $('.variable-price-fields').toggle(showVariablePricing);
    $('.variable-price-fields').find('input').prop('disabled', !showVariablePricing);
    $('.shared_pricing_panel').find('input[name="purchase_price"], input[name="old_price"], input[name="new_price"]').prop('disabled', !showSharedPricing);
    $('.normal-stock-field').find('input').prop('disabled', isVariable);
  }

  function toggleProductTypeSections() {
    if ($('#product_type').val() == 1) {
      $('.variable_product').hide();
      $('.variable-attribute-selector').hide();
    } else {
      $('.variable_product').show();
      $('.variable-attribute-selector').show();
    }
    toggleVariationPricingMode();
    toggleVariableAttributeFields();
  }


  function generateBarcodeValue(prefix) {
    prefix = String(prefix || '20').replace(/[^0-9A-Za-z]/g, '').slice(0, 4) || '20';
    var timePart = String(Date.now()).slice(-8);
    var randomPart = String(Math.floor(1000 + Math.random() * 9000));
    return (prefix + timePart + randomPart).slice(0, 20);
  }

  function bindBarcodeGenerator(context) {
    $(context || document).find('.barcode-generate-btn').off('click.barcode').on('click.barcode', function() {
      var $input = $(this).closest('.input-group').find('.barcode-input');
      if (!$input.length && $(this).data('target')) {
        $input = $($(this).data('target'));
      }
      if (!$input.length) {
        return;
      }
      var prefix = $('#product_type').val() == 0 ? 'VAR' : 'PRD';
      $input.val(generateBarcodeValue(prefix)).trigger('input');
    });
  }

  function slugifyText(value) {
    return String(value || '')
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-');
  }

  function updateSeoPreview() {
    var productName = $('#name').val() || 'Your product title will appear here';
    var seoTitle = $('#meta_title').val() || productName;
    var seoDescription = $('#meta_description').val() || 'A concise SEO description will help customers understand the product from search results.';
    var slug = slugifyText($('#name').val()) || 'your-product-slug';

    $('#seoPreviewTitle').text(seoTitle);
    $('#seoPreviewDescription').text(seoDescription);
    $('#seoPreviewSlug').text(slug);
    $('#seoTitleCount').text(seoTitle.length);
    $('#seoDescriptionCount').text(seoDescription.length);
  }

  function bindGalleryDropzone() {
    $(document).on('dragenter dragover', '.product-gallery-dropzone', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).addClass('is-dragover');
    });

    $(document).on('dragleave dragend drop', '.product-gallery-dropzone', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).removeClass('is-dragover');
    });
  }

  function renderMainGalleryPreview(input) {
    var $input = $(input);
    var $preview = $input.closest('.product-gallery-box').find('.product-gallery-preview');
    var files = Array.from(input.files || []);

    if (!files.length) {
      return;
    }

    $preview.empty();

    files.forEach(function(file) {
      if (!file.type.match(/^image\//)) {
        return;
      }

      var reader = new FileReader();
      reader.onload = function(e) {
        $preview.append(
          '<div class="product-gallery-preview-item"><img src="' + e.target.result + '" alt="Preview"></div>'
        );
      };
      reader.readAsDataURL(file);
    });
  }

  function renderVariationImagePreview(input) {
    var $input = $(input);
    var $preview = $input.closest('.variation-image-box').find('.variation-image-preview');
    var files = Array.from(input.files || []);

    $preview.empty();

    if (!files.length) {
      return;
    }

    if (files.length > 2) {
      input.value = '';
      alert('You can upload a maximum of 2 images for each variation.');
      return;
    }

    files.forEach(function(file) {
      if (!file.type.match(/^image\//)) {
        return;
      }

      var reader = new FileReader();
      reader.onload = function(e) {
        $preview.append(
          '<div class="variation-image-preview-item"><img src="' + e.target.result + '" alt="Preview"></div>'
        );
      };
      reader.readAsDataURL(file);
    });
  }

  $(document).ready(function() {
    $('#product_type').change(toggleProductTypeSections);
    $('#variation_pricing_mode').change(toggleVariationPricingMode);
    $('#selected_attribute_ids').change(toggleVariableAttributeFields);
    $('#name, #meta_title, #meta_description').on('input', updateSeoPreview);
    bindBarcodeGenerator(document);

    var serialNumber = {{ $variables->count() + 1 }};
    $(document).on('click', '.increment_btn', function() {
      var html = $('.clone_variable').html().replace(/__INDEX__/g, serialNumber);
      $('.variable_product').append(html);
      bindBarcodeGenerator($('.variable_product'));
      toggleVariableAttributeFields();
      toggleVariationPricingMode();
      serialNumber++;
    });

    $(document).on('click', '.remove_btn', function() {
      $(this).closest('.increment_control').remove();
    });

    $(document).on('change', 'input[name="image[]"]', function() {
      renderMainGalleryPreview(this);
    });

    bindGalleryDropzone();

    $(document).on('change', 'input[name^="images["], input[name^="up_images["]', function() {
      renderVariationImagePreview(this);
    });

    toggleProductTypeSections();
    updateSeoPreview();
  });
</script>
<script>
  $(document).ready(function() {
    $('.select2').select2();
  });

  $('#primary_category_id').on('change', function() {
    var ajaxId = $(this).val();
    if (ajaxId) {
      $.ajax({
        type: 'GET',
        url: "{{url('ajax-product-subcategory')}}?category_id=" + ajaxId,
        success: function(res) {
          if (res) {
            $('#subcategory_id').empty();
            $('#subcategory_id').append('<option value=\"\">Select...</option>');
            $.each(res, function(key, value) {
              $('#subcategory_id').append('<option value="' + key + '">' + value + '</option>');
            });
          } else {
            $('#subcategory_id').empty();
          }
        },
      });
    } else {
      $('#subcategory_id').empty();
    }
  });

  $('#subcategory_id').on('change', function() {
    var ajaxId = $(this).val();
    if (ajaxId) {
      $.ajax({
        type: 'GET',
        url: "{{url('ajax-product-childcategory')}}?subcategory_id=" + ajaxId,
        success: function(res) {
          if (res) {
            $('#childcategory_id').empty();
            $('#childcategory_id').append('<option value=\"\">Select...</option>');
            $.each(res, function(key, value) {
              $('#childcategory_id').append('<option value="' + key + '">' + value + '</option>');
            });
          } else {
            $('#childcategory_id').empty();
          }
        },
      });
    } else {
      $('#childcategory_id').empty();
    }
  });
</script>
@endsection
