@once
<style>
    .category-editor-shell {
        display: grid;
        gap: 18px;
        padding-top: 12px;
    }

    .category-editor-hero {
        position: relative;
        overflow: hidden;
        padding: 24px 26px;
        border: 1px solid #d9e5f5;
        border-radius: 24px;
        background: linear-gradient(135deg, #fff8ef 0%, #f7fbff 48%, #ffffff 100%);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .category-editor-hero::before {
        content: "";
        position: absolute;
        top: -46px;
        right: -24px;
        width: 140px;
        height: 140px;
        border-radius: 999px;
        background: rgba(59, 130, 246, 0.08);
    }

    .category-editor-hero::after {
        content: "";
        position: absolute;
        bottom: -54px;
        left: -20px;
        width: 160px;
        height: 160px;
        border-radius: 999px;
        background: rgba(251, 191, 36, 0.12);
    }

    .category-editor-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .category-editor-copy {
        max-width: 560px;
    }

    .category-editor-kicker {
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

    .category-editor-title {
        margin: 12px 0 8px;
        color: #0f172a;
        font-size: 30px;
        font-weight: 800;
        line-height: 1.15;
    }

    .category-editor-link {
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

    .category-editor-link:hover {
        color: #0f172a;
        border-color: #93c5fd;
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
    }

    .category-editor-card {
        border: 1px solid #dbe4f0;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.05);
    }

    .category-editor-card .card-body {
        padding: 26px;
    }

    @media (max-width: 991px) {
        .category-editor-hero-inner {
            flex-direction: column;
            align-items: flex-start;
        }

        .category-editor-title {
            font-size: 26px;
        }
    }

    @media (max-width: 575px) {
        .category-editor-hero {
            padding: 20px;
            border-radius: 20px;
        }

        .category-editor-card {
            border-radius: 20px;
        }

        .category-editor-card .card-body {
            padding: 18px;
        }

        .category-editor-title {
            font-size: 24px;
        }

        .category-editor-link {
            width: 100%;
        }
    }
</style>
@endonce

<div class="category-editor-hero">
    <div class="category-editor-hero-inner">
        <div class="category-editor-copy">
            <span class="category-editor-kicker">{{ $kicker ?? 'Category Workspace' }}</span>
            <h1 class="category-editor-title">{{ $title }}</h1>
        </div>

        <a href="{{ route('categories.index') }}" class="category-editor-link">
            Manage Categories
        </a>
    </div>
</div>
