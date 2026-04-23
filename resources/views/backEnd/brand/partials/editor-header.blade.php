@once
<style>
    .brand-editor-shell {
        display: grid;
        gap: 18px;
        padding-top: 12px;
    }

    .brand-editor-hero {
        position: relative;
        overflow: hidden;
        padding: 24px 26px;
        border: 1px solid #d8e2ef;
        border-radius: 24px;
        background: linear-gradient(135deg, #f8fbff 0%, #f4f7ff 55%, #ffffff 100%);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .brand-editor-hero::before {
        content: "";
        position: absolute;
        top: -40px;
        right: -18px;
        width: 140px;
        height: 140px;
        border-radius: 999px;
        background: rgba(59, 130, 246, 0.08);
    }

    .brand-editor-hero::after {
        content: "";
        position: absolute;
        bottom: -52px;
        left: -22px;
        width: 160px;
        height: 160px;
        border-radius: 999px;
        background: rgba(16, 185, 129, 0.1);
    }

    .brand-editor-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .brand-editor-copy {
        max-width: 560px;
    }

    .brand-editor-kicker {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.06);
        color: #334155;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .brand-editor-title {
        margin: 12px 0 8px;
        color: #0f172a;
        font-size: 30px;
        font-weight: 800;
        line-height: 1.15;
    }

    .brand-editor-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 128px;
        padding: 11px 18px;
        border-radius: 999px;
        border: 1px solid #c7d7ee;
        background: #ffffff;
        color: #0f172a;
        font-weight: 700;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .brand-editor-link:hover {
        color: #0f172a;
        border-color: #93c5fd;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
    }

    .brand-editor-card {
        border: 1px solid #dbe4f0;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.05);
    }

    .brand-editor-card .card-body {
        padding: 26px;
    }

    @media (max-width: 991px) {
        .brand-editor-hero-inner {
            flex-direction: column;
            align-items: flex-start;
        }

        .brand-editor-title {
            font-size: 26px;
        }
    }

    @media (max-width: 575px) {
        .brand-editor-hero {
            padding: 20px;
            border-radius: 20px;
        }

        .brand-editor-card {
            border-radius: 20px;
        }

        .brand-editor-card .card-body {
            padding: 18px;
        }

        .brand-editor-title {
            font-size: 24px;
        }

        .brand-editor-link {
            width: 100%;
        }
    }
</style>
@endonce

<div class="brand-editor-hero">
    <div class="brand-editor-hero-inner">
        <div class="brand-editor-copy">
            <span class="brand-editor-kicker">{{ $kicker ?? 'Brand Workspace' }}</span>
            <h1 class="brand-editor-title">{{ $title }}</h1>
        </div>

        <a href="{{ route('brands.index') }}" class="brand-editor-link">
            Manage Brands
        </a>
    </div>
</div>
