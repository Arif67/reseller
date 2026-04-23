@extends('backEnd.layouts.master')
@section('title','Landing Page Edit')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('public/backEnd')}}/assets/libs/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('backEnd/assets/libs/grapesjs/grapes.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .campaign-editor-help {
        margin-top: 8px;
        color: #64748b;
        font-size: 12px;
    }

    .campaign-builder-shell {
        margin-top: 12px;
        border: 1px solid #dbe3ef;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.05);
    }

    .campaign-builder-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        border-bottom: 1px solid #dbe3ef;
    }

    .campaign-builder-head strong {
        color: #0f172a;
    }

    .campaign-builder-head span {
        color: #64748b;
        font-size: 12px;
    }

    .campaign-builder-canvas {
        min-height: 520px;
    }

    .campaign-builder-source {
        display: none;
    }

    .campaign-builder-shell .gjs-one-bg {
        background: #0f172a;
    }

    .campaign-builder-shell .gjs-two-color {
        color: #e2e8f0;
    }

    .campaign-builder-shell .gjs-three-bg {
        background: #1e293b;
        color: #e2e8f0;
    }

    .campaign-builder-shell .gjs-four-color,
    .campaign-builder-shell .gjs-color-warn {
        color: #f8fafc;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('campaign.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Landing Page Edit</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <form action="{{route('campaign.update')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data" name="editForm">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="hidden_id">

                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="product_id" class="form-label">Products *</label>
                             <select class="select2 form-control  @error('product_id') is-invalid @enderror" value="{{ old('product_id') }}" name="product_id" data-placeholder="Choose ...">
                                <option value="">Select..</option>
                                @foreach($products as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->


                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Campaign Title *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $edit_data->name}}"  id="name" required="">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->

                    
                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="banner" class="form-label">Banner Image *</label>
                            <input type="file" class="form-control @error('banner') is-invalid @enderror" name="banner" value="{{ $edit_data->banner }}"  id="banner" >
                            <img src="{{asset($edit_data->banner)}}" alt="" class="edit-image" required>
                            @error('banner')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                   <div class="col-sm-12 my-3">
                        <div class="form-group">
                            <label for="short_description" class="form-label">Short Description *</label>
                            <textarea name="short_description" rows="6" class="campaign-builder-source form-control @error('short_description') is-invalid @enderror" data-builder-label="Short Description">{!! $edit_data->short_description !!}</textarea>
                            <div class="campaign-editor-help">Image, bold text, button, row/column block sob ekhanei insert korte parben.</div>
                            @error('short_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="video" class="form-label">Video (optional)</label>
                            <input type="text" class="form-control @error('video') is-invalid @enderror" name="video" value="{{ $edit_data->video }}"  id="video">
                            @error('video')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6 mb-3">
                        <label for="image">Review Image (Optional)</label>
                        <div class="input-group control-group increment">
                            <input type="file" name="image[]" class="form-control @error('image') is-invalid @enderror" />
                            <div class="input-group-btn">
                                <button class="btn btn-success btn-increment" type="button"><i class="fa fa-plus"></i></button>
                            </div>
                            @error('image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="clone hide" style="display: none;">
                            <div class="control-group input-group">
                                <input type="file" name="image[]" class="form-control" />
                                <div class="input-group-btn">
                                    <button class="btn btn-danger" type="button"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="product_img">
                            @foreach($edit_data->images as $image)
                            <img src="{{asset($image->image)}}" class="edit-image border" alt="" />
                            <a href="{{route('campaign.image.destroy',['id'=>$image->id])}}" class="btn btn-xs btn-danger waves-effect waves-light"><i class="mdi mdi-close"></i></a>
                            @endforeach
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-12">
                        <div class="form-group mb-3">
                            <label for="description_title" class="form-label">Description Title *</label>
                            <input type="text" class="form-control @error('description_title') is-invalid @enderror" name="description_title" value="{{$edit_data->description_title}}"  id="description_title" required>
                            @error('description_title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="description" class="form-label"> Description *</label>
                            <textarea name="description" rows="6" class="campaign-builder-source form-control @error('description') is-invalid @enderror" data-builder-label="Description">{{$edit_data->description}}</textarea>
                            <div class="campaign-editor-help">2 column section, CTA row, image block use kore Elementor-style content banate parben.</div>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-12 mb-3">
                        <div class="form-group">
                            <label for="why_chooseus" class="form-label">Why Choose Us *</label>
                            <textarea name="why_chooseus" rows="6" class="campaign-builder-source form-control @error('why_chooseus') is-invalid @enderror" data-builder-label="Why Choose Us">{{$edit_data->why_chooseus}}</textarea>
                            <div class="campaign-editor-help">Feature list, icon text, row-column layout and styled blocks add kora jabe.</div>
                            @error('why_chooseus')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->

                    <div class="col-sm-6 mb-3">
                        <div class="form-group">
                            <label for="status" class="d-block">Status</label>
                            <label class="switch">
                                <input type="checkbox" value="1" name="status" @if($edit_data->status==1)checked @endif>
                                <span class="slider round"></span>
                            </label>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>

                </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
@endsection



@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-pickers.init.js"></script>
<!-- Plugins js -->
<script src="{{ asset('backEnd/assets/libs/grapesjs/grapes.min.js') }}"></script>
<script>
    function initCampaignBuilders() {
        if (typeof grapesjs === 'undefined') {
            return;
        }

        var builderDefaults = {
            'Short Description': '<section style="padding:24px;border:1px solid #e2e8f0;border-radius:18px;background:#fff7ed;"><h2 style="margin:0 0 12px;font-size:28px;font-weight:700;color:#0f172a;">Short Description</h2><p style="margin:0;font-size:16px;line-height:1.7;color:#475569;">Ei section e campaign er short summary, bold highlight, image and CTA add korun.</p></section>',
            'Description': '<section style="padding:28px;border:1px solid #e2e8f0;border-radius:18px;background:#ffffff;"><h2 style="margin:0 0 12px;font-size:30px;font-weight:700;color:#0f172a;">Description Section</h2><p style="margin:0;font-size:16px;line-height:1.8;color:#475569;">Drag block diye section build korun, image din, row-column arrange korun.</p></section>',
            'Why Choose Us': '<section style="padding:28px;border:1px solid #dbeafe;border-radius:18px;background:linear-gradient(135deg,#eff6ff 0%,#f8fafc 100%);"><h2 style="margin:0 0 18px;font-size:30px;font-weight:700;color:#0f172a;">Why Choose Us</h2><div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;"><div style="padding:18px;border-radius:14px;background:#fff;"><h4 style="margin:0 0 8px;color:#0f172a;">Fast Delivery</h4><p style="margin:0;color:#475569;">Quick shipping message.</p></div><div style="padding:18px;border-radius:14px;background:#fff;"><h4 style="margin:0 0 8px;color:#0f172a;">Best Price</h4><p style="margin:0;color:#475569;">Value proposition message.</p></div><div style="padding:18px;border-radius:14px;background:#fff;"><h4 style="margin:0 0 8px;color:#0f172a;">Trusted Quality</h4><p style="margin:0;color:#475569;">Trust building message.</p></div></div></section>'
        };

        function editorMarkup(editor) {
            var html = editor.getHtml().trim();
            var css = editor.getCss().trim();
            return css ? '<style>' + css + '</style>' + html : html;
        }

        function addImageUploadButton(editor, index) {
            editor.Panels.addButton('options', {
                id: 'upload-image-' + index,
                className: 'fa fa-image',
                command: function () {
                    var input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/*';
                    input.multiple = true;
                    input.onchange = function (event) {
                        Array.from(event.target.files || []).forEach(function (file) {
                            var reader = new FileReader();
                            reader.onload = function (loadEvent) {
                                editor.AssetManager.add({
                                    src: loadEvent.target.result,
                                    name: file.name
                                });
                            };
                            reader.readAsDataURL(file);
                        });
                    };
                    input.click();
                },
                attributes: { title: 'Upload Image' }
            });
        }

        function registerBlocks(editor) {
            var blockManager = editor.BlockManager;
            blockManager.getAll().reset();

            [
                {
                    id: 'headline',
                    label: 'Headline',
                    category: 'Basic',
                    content: '<section style="padding:24px 20px;"><h2 style="margin:0 0 10px;font-size:34px;font-weight:700;color:#0f172a;">Powerful headline</h2><p style="margin:0;font-size:16px;line-height:1.7;color:#475569;">Short supporting text here.</p></section>'
                },
                {
                    id: 'rich-text',
                    label: 'Text',
                    category: 'Basic',
                    content: '<div style="padding:20px;"><p style="margin:0;font-size:16px;line-height:1.8;color:#475569;">Write text here. <strong>Bold highlight</strong> add korte parben.</p></div>'
                },
                {
                    id: 'image',
                    label: 'Image',
                    category: 'Basic',
                    content: '<div style="min-height:260px;border:2px dashed #cbd5e1;border-radius:18px;background:#f8fafc;"></div>'
                },
                {
                    id: 'button',
                    label: 'Button',
                    category: 'Basic',
                    content: '<div style="padding:20px;"><a href="#order_form" style="display:inline-block;padding:14px 28px;border-radius:999px;background:#f97316;color:#fff;text-decoration:none;font-weight:700;">Order Now</a></div>'
                },
                {
                    id: 'two-col',
                    label: '2 Columns',
                    category: 'Layout',
                    content: '<section style="padding:24px 0;"><div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;"><div style="padding:22px;border:1px solid #e2e8f0;border-radius:18px;background:#fff;"><h3 style="margin:0 0 10px;color:#0f172a;">Left Title</h3><p style="margin:0;color:#475569;line-height:1.7;">Left side content.</p></div><div style="padding:22px;border:1px solid #e2e8f0;border-radius:18px;background:#fff;"><h3 style="margin:0 0 10px;color:#0f172a;">Right Title</h3><p style="margin:0;color:#475569;line-height:1.7;">Right side content.</p></div></div></section>'
                },
                {
                    id: 'three-col',
                    label: '3 Columns',
                    category: 'Layout',
                    content: '<section style="padding:24px 0;"><div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;"><div style="padding:22px;border-radius:18px;background:#eff6ff;"><h4 style="margin:0 0 10px;color:#0f172a;">One</h4><p style="margin:0;color:#475569;line-height:1.7;">Feature content.</p></div><div style="padding:22px;border-radius:18px;background:#fff7ed;"><h4 style="margin:0 0 10px;color:#0f172a;">Two</h4><p style="margin:0;color:#475569;line-height:1.7;">Feature content.</p></div><div style="padding:22px;border-radius:18px;background:#f1f5f9;"><h4 style="margin:0 0 10px;color:#0f172a;">Three</h4><p style="margin:0;color:#475569;line-height:1.7;">Feature content.</p></div></div></section>'
                },
                {
                    id: 'feature-box',
                    label: 'Feature Box',
                    category: 'Sections',
                    content: '<section style="padding:26px;border-radius:20px;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);"><h3 style="margin:0 0 12px;color:#fff;font-size:30px;">Why people buy this</h3><p style="margin:0;color:#cbd5e1;font-size:16px;line-height:1.8;">Strong benefit statement with premium background.</p></section>'
                },
                {
                    id: 'cta-banner',
                    label: 'CTA Banner',
                    category: 'Sections',
                    content: '<section style="padding:28px;border-radius:22px;background:linear-gradient(135deg,#f97316 0%,#fb923c 100%);text-align:center;"><h2 style="margin:0 0 10px;color:#fff;font-size:34px;">Limited Time Offer</h2><p style="margin:0 0 18px;color:#ffedd5;font-size:16px;">Add urgency and push the order action.</p><a href="#order_form" style="display:inline-block;padding:13px 26px;border-radius:999px;background:#fff;color:#ea580c;text-decoration:none;font-weight:700;">Claim Offer</a></section>'
                }
            ].forEach(function (block) {
                blockManager.add(block.id, block);
            });
        }

        document.querySelectorAll('.campaign-builder-source').forEach(function (textarea, index) {
            var label = textarea.dataset.builderLabel || 'Campaign Content';
            var shell = document.createElement('div');
            shell.className = 'campaign-builder-shell';
            shell.innerHTML = '<div class="campaign-builder-head"><div><strong>' + label + ' Builder</strong><span class="d-block">Drag blocks, edit text, upload image, row/column use korun.</span></div></div><div class="campaign-builder-canvas" id="campaign-builder-' + index + '"></div>';
            textarea.insertAdjacentElement('afterend', shell);

            var editor = grapesjs.init({
                container: '#campaign-builder-' + index,
                height: '520px',
                fromElement: false,
                storageManager: false,
                noticeOnUnload: false,
                selectorManager: { componentFirst: true },
                components: textarea.value || builderDefaults[label] || builderDefaults['Description'],
                canvas: {
                    styles: []
                }
            });

            editor.Panels.removePanel('devices-c');
            registerBlocks(editor);
            addImageUploadButton(editor, index);

            var form = textarea.closest('form');
            if (form) {
                form.addEventListener('submit', function () {
                    textarea.value = editorMarkup(editor);
                });
            }
        });
    }

    initCampaignBuilders();
</script>
<script type="text/javascript">
    document.forms['editForm'].elements['product_id'].value="{{$edit_data->product_id}}"
    $('.select2').select2();
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".btn-increment").click(function () {
            var html = $(".clone").html();
            $(".increment").after(html);
        });
        $("body").on("click", ".btn-danger", function () {
            $(this).parents(".control-group").remove();
        });
        $('.select2').select2();
    });
</script>
@endsection
