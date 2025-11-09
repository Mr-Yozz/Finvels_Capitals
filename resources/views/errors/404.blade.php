<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invalid Page</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #0f1724;
            --card: #0b1220;
            --accent: #7c3aed;
            --muted: #94a3b8;
        }

        html,
        body {
            height: 100%;
        }

        body {
            background: radial-gradient(1200px 600px at 10% 10%, rgba(124, 58, 237, 0.08), transparent),
                linear-gradient(180deg, #071028 0%, #071226 100%);
            color: #e6eef8;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card-custom {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
            border: 1px solid rgba(255, 255, 255, 0.04);
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.6);
            border-radius: 14px;
            max-width: 1000px;
            width: 100%;
            overflow: hidden;
        }

        .illustration {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.14), rgba(59, 130, 246, 0.06));
            min-height: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
        }

        .ill-svg {
            width: 100%;
            max-width: 420px
        }

        .content {
            padding: 2.25rem 2.5rem;
        }

        h1 {
            font-size: 2.25rem;
            margin-bottom: .25rem;
            letter-spacing: -0.02em
        }

        p.lead {
            color: var(--muted);
            margin-bottom: 1.25rem
        }

        .btn-outline-glow {
            border: 1px solid rgba(124, 58, 237, 0.35);
            color: var(--accent);
            background: transparent;
            box-shadow: 0 6px 18px rgba(124, 58, 237, 0.06);
            transition: all .18s ease-in-out;
        }

        .btn-outline-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(124, 58, 237, 0.12);
            background: rgba(124, 58, 237, 0.02);
            color: #fff;
        }

        .code-hint {
            background: rgba(255, 255, 255, 0.02);
            padding: .55rem .75rem;
            border-radius: 8px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", monospace;
            color: #c7d2fe;
            display: inline-block;
            font-size: .95rem;
        }

        @media (min-width: 992px) {
            .layout-grid {
                display: grid;
                grid-template-columns: 1fr 1fr
            }

            .illustration {
                min-height: 420px
            }
        }
    </style>
</head>

<body>
    <div class="card-custom">
        <div class="layout-grid">
            <div class="illustration">
                <!-- Simple decorative SVG illustration -->
                <svg class="ill-svg" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="g1" x1="0" x2="1">
                            <stop offset="0" stop-color="#7c3aed" stop-opacity="0.95" />
                            <stop offset="1" stop-color="#3b82f6" stop-opacity="0.9" />
                        </linearGradient>
                    </defs>
                    <rect x="20" y="30" width="560" height="320" rx="18" fill="url(#g1)" opacity="0.12" />
                    <g transform="translate(70,60)">
                        <circle cx="120" cy="80" r="56" fill="#0b1220" opacity="0.6" />
                        <path d="M40 200 Q120 50 220 200 T400 200" fill="none" stroke="#94a3b8" stroke-width="4" stroke-opacity="0.14" />
                        <g transform="translate(140,60)">
                            <rect x="-70" y="-20" width="160" height="120" rx="14" fill="#071226" opacity="0.9" />
                            <text x="10" y="20" fill="#c7d2fe" font-size="18" font-family="sans-serif">404</text>
                            <text x="10" y="54" fill="#94a3b8" font-size="12" font-family="sans-serif">Invalid Page</text>
                        </g>
                    </g>
                </svg>
            </div>

            <div class="content d-flex flex-column justify-content-center">
                <h1>Invalid Page</h1>
                <p class="lead">The page you are looking for doesn't exist, Development process going on Stay Wait and you typed an incorrect URL.</p>

                <div class="mb-3">
                    <a href="/" class="btn btn-primary btn-lg me-2">Go to Home</a>
                    <a href="javascript:history.back()" class="btn btn-outline-glow btn-lg">Go Back</a>
                </div>

                <div class="mb-3">
                    <form class="d-flex" role="search" onsubmit="event.preventDefault(); alert('Search not implemented in demo.');">
                        <input class="form-control me-2" type="search" placeholder="Search the site" aria-label="Search" />
                        <button class="btn btn-light" type="submit">Search</button>
                    </form>
                </div>

                <div class="mt-2">
                    <span class="code-hint">URL:</span>
                    <small class="ms-2 text-muted">/invalid-page</small>
                </div>

                <div class="mt-4 text-muted small">If you believe this is an error, please contact<a href="mailto:support@yourdomain.com">support@yourdomain.com</a>.</div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>