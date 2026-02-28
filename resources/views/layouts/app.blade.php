<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SWA')</title>
    @include('partials.pwa-head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #eef4fd;
            --bg-soft: #f8fbff;
            --panel: #ffffff;
            --panel-soft: #fbfdff;
            --text: #10264d;
            --muted: #557097;
            --line: #d5e1f1;
            --line-strong: #b3c7e1;
            --primary: #0b2e66;
            --primary-strong: #08214a;
            --primary-soft: #e8f0ff;
            --accent: #f2c14d;
            --accent-soft: #fff7df;
            --danger: #b42318;
            --danger-bg: #fef3f2;
            --ok: #0a6b57;
            --ok-bg: #e9fbf4;
            --shadow-soft: 0 14px 30px rgba(15, 33, 68, 0.08);
        }
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        html, body { min-height: 100%; }
        body {
            margin: 0;
            position: relative;
            background:
                radial-gradient(circle at 11% -14%, #d5e5ff 0%, rgba(213, 229, 255, 0) 42%),
                radial-gradient(circle at 95% -24%, #fff0cb 0%, rgba(255, 240, 203, 0) 36%),
                linear-gradient(180deg, #ebf3ff 0%, var(--bg) 24%, var(--bg-soft) 100%);
            color: var(--text);
            font-family: "Manrope", "Segoe UI Variable", "Segoe UI", sans-serif;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 80% 80%, rgba(11, 46, 102, 0.05) 0, rgba(11, 46, 102, 0) 35%),
                radial-gradient(circle at 10% 70%, rgba(242, 193, 77, 0.08) 0, rgba(242, 193, 77, 0) 32%);
            z-index: 0;
        }
        a { color: inherit; text-decoration: none; }
        .shell {
            position: relative;
            z-index: 1;
            max-width: 1320px;
            margin: 0 auto;
            padding: 16px clamp(12px, 3vw, 30px) 28px;
        }
        .topbar {
            position: sticky;
            top: 8px;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            padding: 10px 14px;
            border: 1px solid rgba(172, 192, 220, 0.76);
            border-radius: 16px;
            background: rgba(248, 251, 255, 0.9);
            backdrop-filter: blur(12px);
            box-shadow: 0 14px 32px rgba(8, 33, 74, 0.08);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            min-width: 0;
        }
        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(172, 192, 220, 0.82);
            background: #fff;
            box-shadow: 0 4px 10px rgba(11, 46, 102, 0.12);
        }
        .brand-copy {
            display: grid;
            gap: 2px;
            min-width: 0;
        }
        .brand-copy span {
            display: block;
            max-width: 520px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .brand strong {
            font-size: clamp(24px, 4.2vw, 30px);
            line-height: 1;
            letter-spacing: 0.4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 92%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 0 rgba(0, 0, 0, 0);
        }
        .brand span {
            color: var(--muted);
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: min(100%, 720px);
        }
        .btn {
            border: 1px solid var(--line);
            border-radius: 11px;
            background: linear-gradient(180deg, #fff 0%, #f5f9ff 100%);
            min-height: 40px;
            padding: 8px 13px;
            cursor: pointer;
            font-weight: 700;
            color: var(--text);
            transition: all .2s ease;
        }
        .btn:hover {
            border-color: #a6bede;
            background: linear-gradient(180deg, #fff 0%, #ecf3ff 100%);
            transform: translateY(-1px);
        }
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .net-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid #bde5cc;
            background: #ecfdf3;
            color: #0f5132;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .02em;
            white-space: nowrap;
        }
        .net-status.offline {
            border-color: #efd28b;
            background: #fff7e1;
            color: #6f4e00;
        }
        .nav {
            display: flex;
            gap: 8px;
            margin: 10px 0 16px;
            padding-bottom: 4px;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: #b8cae0 transparent;
        }
        .nav::-webkit-scrollbar { height: 6px; }
        .nav::-webkit-scrollbar-thumb {
            background: #b8cae0;
            border-radius: 999px;
        }
        .nav a {
            background: linear-gradient(180deg, var(--panel) 0%, #f5f9ff 100%);
            border: 1px solid var(--line);
            color: var(--muted);
            border-radius: 999px;
            padding: 8px 13px;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            transition: all .2s ease;
        }
        .nav a.active {
            border-color: #d3b05e;
            color: var(--primary-strong);
            background: linear-gradient(180deg, var(--accent-soft) 0%, #fffef8 100%);
            box-shadow: 0 4px 10px rgba(242, 193, 77, 0.2);
        }
        .nav a:hover {
            border-color: #aec2dd;
            color: #214166;
            transform: translateY(-1px);
        }
        .app-frame {
            display: grid;
            grid-template-columns: minmax(210px, 240px) minmax(0, 1fr);
            gap: 16px;
            align-items: start;
        }
        .page-shell {
            min-width: 0;
        }
        .sidebar-nav {
            position: sticky;
            top: 84px;
            display: grid;
            gap: 12px;
        }
        .sidebar-section {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 12px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(247, 251, 255, 0.96) 100%);
            box-shadow: var(--shadow-soft);
        }
        .sidebar-label {
            display: block;
            margin-bottom: 8px;
            color: #6c82a5;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .sidebar-links {
            display: grid;
            gap: 6px;
        }
        .sidebar-links a {
            display: flex;
            align-items: center;
            min-height: 40px;
            padding: 8px 10px;
            border-radius: 12px;
            border: 1px solid transparent;
            color: #476287;
            font-size: 14px;
            font-weight: 700;
            transition: all .18s ease;
        }
        .sidebar-links a:hover {
            border-color: #d4e0f0;
            background: #f6faff;
            color: #173f7a;
        }
        .sidebar-links a.active {
            border-color: #d8c17e;
            background: linear-gradient(180deg, #fff8e5 0%, #fffdf8 100%);
            color: var(--primary-strong);
            box-shadow: inset 0 0 0 1px rgba(216, 193, 126, 0.18);
        }
        .flash {
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .flash.success {
            background: var(--ok-bg);
            color: var(--ok);
            border: 1px solid #a6e7bf;
        }
        .flash.error {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid #f6b5ab;
        }
        .card {
            background: linear-gradient(180deg, var(--panel) 0%, var(--panel-soft) 100%);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: clamp(14px, 2.2vw, 20px);
            box-shadow: var(--shadow-soft);
        }
        .entry-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(260px, .75fr);
            gap: 14px;
            align-items: start;
        }
        .entry-main,
        .entry-side {
            min-width: 0;
        }
        .entry-form-card {
            border-radius: 18px;
            box-shadow: 0 18px 34px rgba(8, 33, 74, 0.08);
        }
        .entry-header {
            display: grid;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e3ecf8;
        }
        .entry-side {
            display: grid;
            gap: 12px;
            position: sticky;
            top: 86px;
        }
        .entry-aside-card {
            border-radius: 18px;
            background:
                radial-gradient(circle at 100% 0%, rgba(242, 193, 77, 0.16) 0%, rgba(242, 193, 77, 0) 34%),
                linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }
        .entry-side-actions {
            display: grid;
            gap: 8px;
        }
        .browse-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(250px, .72fr);
            gap: 14px;
            align-items: start;
        }
        .browse-main,
        .browse-side {
            min-width: 0;
        }
        .browse-list-card {
            border-radius: 18px;
            box-shadow: 0 18px 34px rgba(8, 33, 74, 0.07);
        }
        .browse-header {
            display: grid;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e3ecf8;
        }
        .browse-highlight {
            border: 1px solid #d9e6f6;
            border-radius: 14px;
            padding: 12px 14px;
            background: linear-gradient(180deg, #fbfdff 0%, #f4f9ff 100%);
            color: var(--muted);
        }
        .browse-highlight strong {
            display: block;
            color: var(--text);
            margin-bottom: 4px;
        }
        .browse-side {
            display: grid;
            gap: 12px;
            position: sticky;
            top: 86px;
        }
        .browse-aside-card {
            border-radius: 18px;
            background:
                radial-gradient(circle at 100% 0%, rgba(84, 142, 255, 0.12) 0%, rgba(84, 142, 255, 0) 34%),
                linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }
        .detail-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(280px, .75fr);
            gap: 14px;
            align-items: start;
        }
        .detail-main,
        .detail-side {
            min-width: 0;
        }
        .detail-card {
            border-radius: 18px;
            box-shadow: 0 18px 34px rgba(8, 33, 74, 0.08);
        }
        .detail-header {
            display: grid;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e3ecf8;
        }
        .detail-side {
            display: grid;
            gap: 12px;
            position: sticky;
            top: 86px;
        }
        .detail-aside-card {
            border-radius: 18px;
            background:
                radial-gradient(circle at 100% 0%, rgba(11, 46, 102, 0.1) 0%, rgba(11, 46, 102, 0) 36%),
                linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        }
        .detail-meta-grid {
            display: grid;
            gap: 8px;
        }
        .detail-meta-item {
            border: 1px solid #dde8f6;
            border-radius: 12px;
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.76);
        }
        .detail-meta-item strong {
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .hero-panel {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 100% 0%, rgba(242, 193, 77, 0.2) 0%, rgba(242, 193, 77, 0) 34%),
                radial-gradient(circle at 0% 0%, rgba(11, 46, 102, 0.12) 0%, rgba(11, 46, 102, 0) 38%),
                linear-gradient(135deg, #ffffff 0%, #f7fbff 42%, #eef5ff 100%);
            border: 1px solid #cddcf0;
            border-radius: 20px;
            padding: clamp(18px, 3vw, 28px);
            box-shadow: 0 18px 34px rgba(8, 33, 74, 0.08);
        }
        .hero-panel::after {
            content: "";
            position: absolute;
            right: -40px;
            bottom: -40px;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(11, 46, 102, 0.08) 0%, rgba(11, 46, 102, 0) 70%);
            pointer-events: none;
        }
        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 14px;
            align-items: stretch;
        }
        .hero-copy {
            display: grid;
            gap: 10px;
        }
        .hero-eyebrow {
            display: inline-flex;
            width: fit-content;
            min-height: 28px;
            align-items: center;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid #e7cc86;
            background: #fff7df;
            color: #7a5d0d;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .hero-title {
            margin: 0;
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.06;
            letter-spacing: -0.02em;
            color: #0d2b59;
        }
        .hero-subtitle {
            margin: 0;
            max-width: 760px;
            color: #4f6990;
            font-size: 15px;
            line-height: 1.55;
        }
        .hero-side {
            display: grid;
            gap: 10px;
            align-content: space-between;
        }
        .hero-badge {
            display: grid;
            gap: 6px;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid #d7e4f3;
            background: rgba(255, 255, 255, 0.88);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
        .hero-badge-label {
            color: #5f7aa3;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .hero-badge-value {
            color: #0b2e66;
            font-size: clamp(22px, 3vw, 32px);
            font-weight: 800;
            line-height: 1.05;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .metric-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }
        .metric-card {
            display: grid;
            gap: 8px;
            padding: 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 10px 24px rgba(8, 33, 74, 0.04);
        }
        .metric-label {
            color: #5c769d;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .metric-value {
            color: #0b2e66;
            font-size: clamp(24px, 3vw, 36px);
            font-weight: 800;
            line-height: 1;
        }
        .metric-note {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.45;
        }
        .cockpit-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 12px;
        }
        .panel-title {
            margin: 0 0 10px;
            color: #153762;
            font-size: 18px;
            font-weight: 800;
        }
        .soft-panel {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 12px 24px rgba(8, 33, 74, 0.04);
        }
        .status-rail {
            display: grid;
            gap: 10px;
        }
        .status-item {
            display: grid;
            gap: 6px;
            padding: 10px;
            border-radius: 12px;
            border: 1px solid #dde7f5;
            background: #fcfdff;
        }
        .status-item-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            font-size: 13px;
        }
        .status-item-head strong {
            color: #28466d;
            font-size: 14px;
        }
        .insight-list {
            display: grid;
            gap: 10px;
        }
        .insight-item {
            display: grid;
            gap: 4px;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #dce7f5;
            background: rgba(255, 255, 255, 0.9);
        }
        .insight-item strong {
            color: #163862;
            font-size: 14px;
        }
        .insight-item span {
            color: var(--muted);
            font-size: 12px;
        }
        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 12px;
        }
        .page-head h1 {
            margin: 0;
            font-size: clamp(24px, 3.4vw, 34px);
            letter-spacing: 0.2px;
        }
        .btn-primary {
            border: 0;
            border-radius: 10px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-strong) 100%);
            color: #f8f4e6;
            min-height: 40px;
            padding: 9px 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all .18s ease;
        }
        .btn-primary:hover {
            filter: brightness(1.03);
        }
        .btn-photo {
            border: 1px solid #e9c86d;
            border-radius: 10px;
            background: linear-gradient(180deg, #f8db8b 0%, var(--accent) 100%);
            color: #3a2b08;
            min-height: 42px;
            padding: 9px 14px;
            font-weight: 800;
            cursor: pointer;
            transition: all .16s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .btn-photo:hover {
            filter: brightness(1.02);
        }
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }
        th, td {
            text-align: left;
            padding: 11px 10px;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }
        th {
            color: #456084;
            font-weight: 700;
            background: #f3f8ff;
        }
        tbody tr:nth-child(even) td {
            background: #fcfdff;
        }
        .actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .link-btn {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            min-height: 36px;
            padding: 6px 11px;
            font-size: 14px;
            cursor: pointer;
            transition: all .16s ease;
        }
        .link-btn:hover {
            border-color: var(--line-strong);
            background: #f7fbff;
        }
        .link-strong {
            border-color: var(--primary);
            background: var(--primary);
            color: #f8f4e6;
            font-weight: 700;
        }
        .link-strong:hover {
            background: var(--primary-strong);
            border-color: var(--primary-strong);
            color: #f8f4e6;
        }
        .link-danger {
            border-color: #f6b5ab;
            color: var(--danger);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field label {
            font-size: 14px;
            color: var(--muted);
        }
        .field input,
        .field select,
        .field textarea {
            border: 1px solid var(--line);
            border-radius: 10px;
            min-height: 42px;
            padding: 10px 11px;
            font: inherit;
            background: #fff;
        }
        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            outline: 2px solid rgba(12, 46, 105, 0.16);
            border-color: #95abcf;
        }
        .field textarea { min-height: 90px; resize: vertical; }
        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 6px;
            color: var(--muted);
            font-size: 14px;
        }
        .checkbox input {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }
        .muted { color: var(--muted); font-size: 14px; }
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 12px;
        }
        .item-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            background: #fff;
        }
        .item-card-pending {
            border-color: #f0c86a;
            box-shadow: inset 0 0 0 1px rgba(240, 200, 106, 0.28);
            background: linear-gradient(180deg, #fffdf6 0%, #ffffff 100%);
        }
        .item-card-locked {
            border-color: #d8e3f3;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
        }
        .item-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }
        .item-state-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: flex-end;
        }
        .item-state,
        .item-flag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .item-state {
            border: 1px solid #d8e3f3;
            background: #f5f8fe;
            color: #2f4f7f;
        }
        .item-state-ok {
            border-color: #bfe4ce;
            background: #effcf4;
            color: #17643d;
        }
        .item-state-danificado,
        .item-state-ausente,
        .item-state-improvisado {
            border-color: #efd28b;
            background: #fff7e1;
            color: #6f4e00;
        }
        .item-flag-warning {
            border: 1px solid #f0c86a;
            background: #fff1bf;
            color: #6a4a00;
        }
        .item-alert {
            border: 1px solid #f0d79d;
            border-radius: 10px;
            padding: 9px 10px;
            background: #fff8e7;
            color: #6a4a00;
            font-size: 13px;
            line-height: 1.5;
        }
        .field-action-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .field-chip-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 8px 12px;
            border: 1px solid #d6e2f2;
            border-radius: 999px;
            background: #fff;
            color: #23446f;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: .18s ease;
        }
        .field-chip-btn:hover {
            border-color: #9fb8dc;
            background: #f6faff;
        }
        .field-chip-btn.active {
            border-color: #173f7a;
            background: linear-gradient(135deg, #16356a 0%, #1e4f98 100%);
            color: #fff;
            box-shadow: 0 10px 18px rgba(23, 63, 122, 0.18);
        }
        .field-chip-btn.active[data-value="ok"] {
            border-color: #15915a;
            background: linear-gradient(135deg, #15915a 0%, #1fb877 100%);
        }
        .field-chip-btn.active[data-value="danificado"],
        .field-chip-btn.active[data-value="ausente"],
        .field-chip-btn.active[data-value="improvisado"] {
            border-color: #cc8b00;
            background: linear-gradient(135deg, #b06d00 0%, #d69a17 100%);
        }
        .evidence-panel {
            border: 1px solid #dce7f5;
            border-radius: 14px;
            padding: 14px;
            background: linear-gradient(180deg, #fbfdff 0%, #f6faff 100%);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .evidence-panel.required {
            border-color: #f0c86a;
            background: linear-gradient(180deg, #fffdf6 0%, #fff8ea 100%);
        }
        .evidence-panel.ready {
            border-color: #bfe4ce;
            background: linear-gradient(180deg, #f5fff8 0%, #ffffff 100%);
        }
        .evidence-status-line {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .evidence-status-copy {
            font-size: 13px;
            color: var(--muted);
        }
        .evidence-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
            border: 1px solid #d8e3f3;
            background: #f5f8fe;
            color: #2f4f7f;
        }
        .evidence-panel.required .evidence-status-pill {
            border-color: #f0c86a;
            background: #fff1bf;
            color: #6a4a00;
        }
        .evidence-panel.ready .evidence-status-pill {
            border-color: #bfe4ce;
            background: #ecfdf3;
            color: #0f5132;
        }
        .evidence-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 8px;
        }
        .evidence-step {
            border: 1px dashed #cddced;
            border-radius: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.86);
            font-size: 13px;
            line-height: 1.5;
        }
        .evidence-step strong {
            display: block;
            margin-bottom: 4px;
            color: #173f7a;
        }
        .evidence-panel.required .evidence-step {
            border-color: #edd28f;
        }
        .evidence-panel.ready .evidence-step {
            border-color: #bfe4ce;
        }
        .item-photo {
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--line);
            max-height: 220px;
            object-fit: cover;
            background: #f8fafc;
        }
        .item-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(120px, 1fr));
            gap: 8px;
            margin-top: 10px;
            font-size: 13px;
        }
        .item-meta div span {
            display: block;
            color: var(--muted);
            margin-bottom: 2px;
        }
        .stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .dash-grid {
            display: grid;
            gap: 12px;
        }
        .dash-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 12px;
        }
        .dash-kpi {
            padding: 14px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #fff;
        }
        .dash-kpi-title {
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 6px;
        }
        .dash-kpi-value {
            font-size: clamp(26px, 4vw, 34px);
            line-height: 1.05;
            font-weight: 800;
            color: var(--primary);
        }
        .dash-kpi-note {
            color: var(--muted);
            font-size: 12px;
            margin-top: 6px;
        }
        .dash-panels {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .dash-panel {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px;
            background: #fff;
        }
        .dash-panel h2 {
            margin: 0 0 10px;
            font-size: 18px;
        }
        .dash-progress-list {
            display: grid;
            gap: 10px;
        }
        .dash-progress-row {
            display: grid;
            gap: 6px;
        }
        .dash-progress-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }
        .dash-progress-head strong {
            font-size: 14px;
            color: #28466d;
        }
        .dash-progress-track {
            width: 100%;
            height: 10px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #eef4fb;
            overflow: hidden;
        }
        .dash-progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #8ea8d6 0%, var(--primary) 100%);
        }
        .dash-trend {
            display: grid;
            grid-template-columns: repeat(6, minmax(32px, 1fr));
            gap: 8px;
            align-items: end;
            min-height: 170px;
            padding-top: 8px;
        }
        .dash-trend-col {
            display: grid;
            justify-items: center;
            gap: 6px;
        }
        .dash-trend-bar {
            width: min(26px, 100%);
            border-radius: 8px 8px 4px 4px;
            background: linear-gradient(180deg, #577cb8 0%, var(--primary) 100%);
            min-height: 8px;
        }
        .dash-trend-value {
            font-size: 12px;
            color: #214166;
            font-weight: 700;
        }
        .dash-trend-label {
            font-size: 11px;
            color: var(--muted);
        }
        .dash-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .quick-grid {
            display: grid;
            gap: 12px;
        }
        .quick-primary {
            width: 100%;
            text-align: center;
        }
        .quick-group {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.84);
        }
        .quick-group strong {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-size: 14px;
        }
        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 12px;
        }
        .quick-links a {
            color: #2f4f7f;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }
        .quick-links a:hover {
            text-decoration: underline;
        }
        .dash-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid #b8cae0;
            background: #f4f8fd;
            color: #35557e;
            font-size: 12px;
            font-weight: 700;
            width: fit-content;
        }
        .dash-list {
            display: grid;
            gap: 10px;
        }
        .dash-list-item {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px;
            background: #fcfdff;
        }
        .dash-list-item .title {
            font-size: 14px;
            color: #23456e;
            font-weight: 700;
            margin-bottom: 3px;
        }
        .dash-list-item .meta {
            color: var(--muted);
            font-size: 12px;
        }
        .risk-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 26px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .02em;
        }
        .risk-neutro {
            background: #f1f5f9;
            border-color: #d9e2ec;
            color: #4a5d75;
        }
        .risk-baixo {
            background: #ecfdf3;
            border-color: #bde5cc;
            color: #0f5132;
        }
        .risk-medio {
            background: #fff7e1;
            border-color: #efd28b;
            color: #6f4e00;
        }
        .risk-alto {
            background: #fef3f2;
            border-color: #f2b7b2;
            color: #9f1f15;
        }
        .dash-ring {
            width: 110px;
            aspect-ratio: 1;
            border-radius: 999px;
            display: grid;
            place-items: center;
            margin: 0 auto;
        }
        .dash-ring-inner {
            width: 74px;
            aspect-ratio: 1;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--line);
            display: grid;
            place-items: center;
            font-size: 18px;
            font-weight: 800;
            color: #214166;
        }
        .field-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 8px;
        }
        .field-actions .btn,
        .field-actions .btn-primary {
            width: 100%;
            min-height: 46px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }
        .reopen-form {
            margin-top: 10px;
            display: grid;
            grid-template-columns: minmax(180px, 1fr) auto;
            gap: 8px;
        }
        .reopen-form input {
            min-height: 40px;
            padding: 8px 10px;
        }
        .field-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid #cfddf1;
            border-radius: 18px;
            padding: clamp(16px, 2.8vw, 24px);
            background:
                radial-gradient(circle at 100% 0%, rgba(243, 204, 86, 0.18) 0%, rgba(243, 204, 86, 0) 32%),
                radial-gradient(circle at 0% 0%, rgba(12, 46, 105, 0.12) 0%, rgba(12, 46, 105, 0) 36%),
                linear-gradient(145deg, #ffffff 0%, #f7fbff 48%, #edf5ff 100%);
            box-shadow: 0 16px 30px rgba(8, 33, 74, 0.08);
        }
        .field-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(220px, .75fr);
            gap: 14px;
            align-items: start;
        }
        .field-badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }
        .field-badge {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid #deebf7;
            background: rgba(255, 255, 255, 0.9);
            color: #486389;
            font-size: 12px;
            font-weight: 700;
        }
        .field-hero-title {
            margin: 0;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1;
            color: var(--primary);
            letter-spacing: -.02em;
        }
        .field-hero-subtitle {
            margin: 10px 0 0;
            color: var(--muted);
            line-height: 1.6;
        }
        .field-hero-side {
            display: grid;
            gap: 10px;
        }
        .field-hero-metric {
            border: 1px solid #d9e6f6;
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.86);
        }
        .field-hero-metric strong {
            display: block;
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 4px;
        }
        .field-hero-metric span {
            display: block;
            color: var(--primary-strong);
            font-size: 22px;
            font-weight: 800;
        }
        .field-step-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }
        .field-step-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            display: grid;
            gap: 10px;
        }
        .field-step-top {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .field-step-num {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: var(--accent-soft);
            color: #7b620f;
            font-size: 14px;
            font-weight: 800;
            border: 1px solid #f1da9a;
            flex: 0 0 auto;
        }
        .field-step-title {
            margin: 0;
            font-size: 16px;
            color: var(--primary-strong);
        }
        .field-step-text {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }
        .field-mini-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
        }
        .field-mini-card {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            background: #fff;
            display: grid;
            gap: 6px;
        }
        .field-mini-card strong {
            color: var(--primary-strong);
            font-size: 14px;
        }
        .field-mini-card span {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }
        .field-open-list {
            display: grid;
            gap: 10px;
        }
        .field-open-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px;
            background: #fff;
            display: grid;
            gap: 10px;
        }
        .field-open-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }
        .field-open-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }
        .field-open-meta .meta-box {
            border: 1px solid #e4edf9;
            border-radius: 10px;
            padding: 8px 10px;
            background: #fbfdff;
        }
        .field-open-meta .meta-box strong {
            display: block;
            font-size: 11px;
            color: var(--muted);
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .field-open-meta .meta-box span {
            display: block;
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
        }
        .guide {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px;
            background: linear-gradient(180deg, #fffef8 0%, #fff 100%);
        }
        .guide h2 {
            margin: 0;
            font-size: 20px;
            color: var(--primary-strong);
        }
        .guide p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }
        .guide-steps {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 10px;
        }
        .guide-step {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            padding: 10px;
            display: grid;
            gap: 6px;
        }
        .guide-step .num {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: var(--accent-soft);
            color: #7b620f;
            font-size: 13px;
            font-weight: 800;
            border: 1px solid #f1da9a;
        }
        .guide-step .title {
            font-size: 14px;
            color: var(--primary-strong);
            font-weight: 700;
        }
        .guide-step .text {
            font-size: 13px;
            color: var(--muted);
        }
        .guide-step .link {
            margin-top: 2px;
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
        }
        .sticky-mobile-actions {
            display: none;
        }
        @media (max-width: 900px) {
            .shell { padding-top: 10px; }
            .app-frame {
                grid-template-columns: 1fr;
            }
            .sidebar-nav {
                position: static;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                align-items: start;
            }
            table { min-width: 640px; }
            .card { border-radius: 12px; }
            .dash-panels {
                grid-template-columns: 1fr;
            }
            .hero-grid,
            .cockpit-grid,
            .metric-strip,
            .entry-layout,
            .browse-layout,
            .detail-layout,
            .field-hero-grid {
                grid-template-columns: 1fr;
            }
            .entry-side,
            .browse-side,
            .detail-side {
                position: static;
            }
        }
        @media (max-width: 700px) {
            .shell {
                padding: 10px 10px 20px;
            }
            .topbar {
                top: 4px;
                padding: 8px 12px;
                gap: 10px;
                flex-direction: row;
                align-items: center;
            }
            .entry-form-card,
            .entry-aside-card,
            .browse-list-card,
            .browse-aside-card,
            .detail-card,
            .detail-aside-card {
                border-radius: 14px;
            }
            .brand strong {
                font-size: 22px;
            }
            .brand span {
                font-size: 13px;
                white-space: normal;
            }
            .brand-copy span {
                display: none;
            }
            .brand-mark {
                width: 36px;
                height: 36px;
            }
            .topbar-actions {
                display: none;
            }
            .btn-menu {
                display: flex;
            }
            .sidebar-nav {
                display: none;
            }
            .sidebar-links a {
                min-height: 36px;
                padding: 7px 9px;
                font-size: 13px;
            }
            .hero-panel {
                border-radius: 16px;
            }
            .hero-actions {
                display: grid;
                grid-template-columns: 1fr;
            }
            .nav {
                margin: 8px 0 14px;
                padding-bottom: 8px;
            }
            .page-head {
                gap: 10px;
            }
            .page-head > * {
                width: 100%;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-actions {
                justify-content: stretch;
            }
            .form-actions .btn,
            .form-actions .btn-primary {
                flex: 1 1 100%;
                text-align: center;
            }
            .table-wrap {
                margin: 0 -2px;
                padding: 0 2px 6px;
                border: 1px solid #d9e6f5;
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.72) 0%, rgba(244, 249, 255, 0.96) 100%);
            }
            table {
                min-width: 560px;
                font-size: 13px;
            }
            th, td {
                padding: 10px 8px;
            }
            thead th:first-child,
            tbody td:first-child {
                position: sticky;
                left: 0;
            }
            thead th:first-child {
                z-index: 3;
                background: #f3f8ff;
                box-shadow: 8px 0 12px rgba(8, 33, 74, 0.08);
            }
            tbody td:first-child {
                z-index: 1;
                background: #ffffff;
                box-shadow: 8px 0 12px rgba(8, 33, 74, 0.05);
            }
            tbody tr:nth-child(even) td:first-child {
                background: #fcfdff;
            }
            .item-meta {
                grid-template-columns: 1fr;
            }
            .field-action-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }
            .field-chip-btn {
                width: 100%;
            }
            .evidence-status-line {
                align-items: flex-start;
            }
            .evidence-steps {
                grid-template-columns: 1fr;
            }
            .field-step-grid,
            .field-mini-grid,
            .field-open-meta {
                grid-template-columns: 1fr;
            }
            .field-open-head {
                flex-direction: column;
                align-items: stretch;
            }
            .dash-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .dash-trend {
                gap: 6px;
                min-height: 150px;
            }
            .dash-trend-bar {
                width: min(22px, 100%);
            }
            .actions-mobile {
                display: grid;
                grid-template-columns: 1fr;
                width: 100%;
            }
            .actions-mobile .link-btn,
            .actions-mobile form,
            .actions-mobile button {
                width: 100%;
                text-align: center;
            }
            .reopen-form {
                grid-template-columns: 1fr;
            }
            .sticky-mobile-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                position: sticky;
                bottom: 8px;
                z-index: 20;
                padding: 8px;
                border: 1px solid var(--line);
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.92);
                backdrop-filter: blur(6px);
            }
            .sticky-mobile-actions .btn,
            .sticky-mobile-actions .btn-primary,
            .sticky-mobile-actions .btn-photo {
                width: 100%;
            }
        }
        /* --- Drawer + Hamburger --- */
        .btn-menu {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: linear-gradient(180deg, #fff 0%, #f5f9ff 100%);
            cursor: pointer;
            color: var(--primary);
            font-size: 22px;
            line-height: 1;
            flex-shrink: 0;
            margin-left: auto;
            padding: 0;
        }
        .drawer-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(8, 33, 74, 0.45);
            z-index: 100;
            opacity: 0;
            transition: opacity .25s ease;
        }
        .drawer {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: min(300px, 88vw);
            background: #fff;
            z-index: 101;
            transform: translateX(-100%);
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 24px rgba(8, 33, 74, 0.14);
            overflow-y: auto;
        }
        .drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            flex-shrink: 0;
        }
        .drawer-brand {
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 92%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: .4px;
        }
        .drawer-context {
            font-size: 12px;
            color: var(--muted);
            padding: 8px 16px 0;
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .drawer-close {
            width: 34px;
            height: 34px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #f5f9ff;
            color: var(--muted);
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 0;
        }
        .drawer-net {
            padding: 10px 16px;
            border-bottom: 1px solid var(--line);
            flex-shrink: 0;
        }
        .drawer-nav {
            flex: 1;
            padding: 12px;
            display: grid;
            gap: 10px;
            align-content: start;
            overflow-y: auto;
        }
        .drawer-footer {
            padding: 12px 16px;
            border-top: 1px solid var(--line);
            flex-shrink: 0;
            display: grid;
            gap: 8px;
        }
        .drawer-footer form button,
        .drawer-footer a.drawer-link-btn {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: linear-gradient(180deg, #fff 0%, #f5f9ff 100%);
            font: inherit;
            font-weight: 700;
            color: var(--text);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            text-decoration: none;
        }
        body.drawer-open .drawer-overlay {
            display: block;
            opacity: 1;
        }
        body.drawer-open .drawer {
            transform: translateX(0);
        }
        body.drawer-open {
            overflow: hidden;
        }
    </style>
</head>
<body>
    @php($contextCondominio = request()->route('condominio'))
    @php($hideNav = trim($__env->yieldContent('hide_nav')) === '1')

    @auth
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer" id="drawer" role="navigation" aria-label="Menu de navegação">
        <div class="drawer-header">
            <span class="drawer-brand">SWA</span>
            <button class="drawer-close" id="drawerClose" aria-label="Fechar menu" type="button">&#10005;</button>
        </div>
        @if ($contextCondominio)
            <div class="drawer-context">{{ $contextCondominio->nome }}</div>
        @endif
        <div class="drawer-net">
            <span class="net-status" id="netStatusDrawer">Online</span>
        </div>
        <div class="drawer-nav">
            <section class="sidebar-section">
                <span class="sidebar-label">Sistema</span>
                <div class="sidebar-links">
                    <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Início</a>
                    <a href="{{ route('condominios.index') }}" @class(['active' => request()->routeIs('condominios.index') || request()->routeIs('condominios.create') || request()->routeIs('condominios.edit')])>Condomínios</a>
                    @unless($contextCondominio)
                        <a href="{{ route('ajuda.index') }}" @class(['active' => request()->routeIs('ajuda.index')])>Ajuda</a>
                    @endunless
                </div>
            </section>

            @if ($contextCondominio)
                <section class="sidebar-section">
                    <span class="sidebar-label">Operação</span>
                    <div class="sidebar-links">
                        <a href="{{ route('condominios.context.dashboard', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.dashboard')])>Painel</a>
                        <a href="{{ route('condominios.context.vistorias.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.vistorias.*')])>Vistorias</a>
                        <a href="{{ route('condominios.context.relatorios.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.relatorios.*')])>Relatórios</a>
                        <a href="{{ route('condominios.context.emails.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.emails.*')])>E-mails</a>
                    </div>
                </section>

                <section class="sidebar-section">
                    <span class="sidebar-label">Estrutura</span>
                    <div class="sidebar-links">
                        <a href="{{ route('condominios.context.blocos.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.blocos.*')])>Blocos</a>
                        <a href="{{ route('condominios.context.pavimentos.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.pavimentos.*')])>Pavimentos</a>
                        <a href="{{ route('condominios.context.unidades.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.unidades.*')])>Unidades</a>
                        <a href="{{ route('condominios.context.areas.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.areas.*')])>Áreas</a>
                        <a href="{{ route('condominios.context.templates.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.templates.*')])>Templates</a>
                    </div>
                </section>

                <section class="sidebar-section">
                    <span class="sidebar-label">Gestão</span>
                    <div class="sidebar-links">
                        <a href="{{ route('condominios.context.conflitos.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.conflitos.*')])>Conflitos</a>
                        <a href="{{ route('condominios.context.ocorrencias.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.ocorrencias.*')])>Ocorrências</a>
                        <a href="{{ route('condominios.context.emails.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.emails.*')])>E-mails</a>
                        <a href="{{ route('condominios.context.backups.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.backups.*')])>Backups</a>
                        <a href="{{ route('condominios.context.ajuda', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.ajuda')])>Ajuda</a>
                    </div>
                </section>
            @endif
        </div>
        <div class="drawer-footer">
            <a class="drawer-link-btn" href="{{ $contextCondominio ? route('condominios.context.ajuda', $contextCondominio) : route('ajuda.index') }}">Ajuda</a>
            <form method="POST" action="{{ route('logout') }}" data-offline-queue="off">
                @csrf
                <button type="submit">Sair</button>
            </form>
        </div>
    </div>
    @endauth

    <div class="shell">
        <header class="topbar">
            <div class="brand">
                <img class="brand-mark" src="{{ asset('images/swa-logo.jpeg') }}" alt="Logo SWA">
                <div class="brand-copy">
                    <strong>SWA</strong>
                    <span>
                        @if ($contextCondominio)
                            {{ $contextCondominio->nome }}
                        @else
                            Vistoria &amp; Compliance
                        @endif
                    </span>
                </div>
            </div>
            @auth
                <button class="btn-menu" id="btnMenu" aria-label="Abrir menu" type="button">&#9776;</button>
                <div class="topbar-actions">
                    <span class="net-status" id="netStatus">Online</span>
                    <a class="btn" href="{{ $contextCondominio ? route('condominios.context.ajuda', $contextCondominio) : route('ajuda.index') }}">Ajuda</a>
                    <form method="POST" action="{{ route('logout') }}" data-offline-queue="off">
                        @csrf
                        <button class="btn" type="submit">Sair</button>
                    </form>
                </div>
            @endauth
        </header>

        <div class="app-frame">
            @auth
                @unless($hideNav)
                    <aside class="sidebar-nav">
                        <section class="sidebar-section">
                            <span class="sidebar-label">Sistema</span>
                            <div class="sidebar-links">
                                <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Início</a>
                                <a href="{{ route('condominios.index') }}" @class(['active' => request()->routeIs('condominios.index') || request()->routeIs('condominios.create') || request()->routeIs('condominios.edit')])>Condomínios</a>
                                @unless($contextCondominio)
                                    <a href="{{ route('ajuda.index') }}" @class(['active' => request()->routeIs('ajuda.index')])>Ajuda</a>
                                @endunless
                            </div>
                        </section>

                        @if ($contextCondominio)
                            <section class="sidebar-section">
                                <span class="sidebar-label">Operação</span>
                                <div class="sidebar-links">
                                    <a href="{{ route('condominios.context.dashboard', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.dashboard')])>Painel</a>
                                    <a href="{{ route('condominios.context.vistorias.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.vistorias.*')])>Vistorias</a>
                                    <a href="{{ route('condominios.context.relatorios.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.relatorios.*')])>Relatórios</a>
                                    <a href="{{ route('condominios.context.emails.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.emails.*')])>E-mails</a>
                                </div>
                            </section>

                            <section class="sidebar-section">
                                <span class="sidebar-label">Estrutura</span>
                                <div class="sidebar-links">
                                    <a href="{{ route('condominios.context.blocos.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.blocos.*')])>Blocos</a>
                                    <a href="{{ route('condominios.context.pavimentos.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.pavimentos.*')])>Pavimentos</a>
                                    <a href="{{ route('condominios.context.unidades.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.unidades.*')])>Unidades</a>
                                    <a href="{{ route('condominios.context.areas.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.areas.*')])>Áreas</a>
                                    <a href="{{ route('condominios.context.templates.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.templates.*')])>Templates</a>
                                </div>
                            </section>

                            <section class="sidebar-section">
                                <span class="sidebar-label">Gestão</span>
                                <div class="sidebar-links">
                                    <a href="{{ route('condominios.context.conflitos.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.conflitos.*')])>Conflitos</a>
                                    <a href="{{ route('condominios.context.ocorrencias.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.ocorrencias.*')])>Ocorrências</a>
                                    <a href="{{ route('condominios.context.emails.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.emails.*')])>E-mails</a>
                                    <a href="{{ route('condominios.context.backups.index', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.backups.*')])>Backups</a>
                                    <a href="{{ route('condominios.context.ajuda', $contextCondominio) }}" @class(['active' => request()->routeIs('condominios.context.ajuda')])>Ajuda</a>
                                </div>
                            </section>
                        @endif
                    </aside>
                @endunless
            @endauth

            <main class="page-shell">
                @if (session('success'))
                    <div class="flash success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="flash error">{{ $errors->first() }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    <script>
        (() => {
            const netStatus = document.getElementById('netStatus');
            const queueKey = 'swa_offline_queue_v1';
            const idbName = 'swa_offline_queue_db_v1';
            const idbStore = 'requests';
            const maxOfflineFileSizeBytes = 6 * 1024 * 1024;
            let dbPromise = null;

            const canUseIndexedDb = () => typeof window !== 'undefined' && 'indexedDB' in window;

            const openQueueDb = () => {
                if (!canUseIndexedDb()) {
                    return Promise.resolve(null);
                }

                if (dbPromise) {
                    return dbPromise;
                }

                dbPromise = new Promise((resolve, reject) => {
                    const request = indexedDB.open(idbName, 1);

                    request.onupgradeneeded = () => {
                        const db = request.result;
                        if (!db.objectStoreNames.contains(idbStore)) {
                            db.createObjectStore(idbStore, { keyPath: 'id', autoIncrement: true });
                        }
                    };

                    request.onsuccess = () => resolve(request.result);
                    request.onerror = () => reject(request.error || new Error('Falha ao abrir IndexedDB.'));
                }).catch(() => null);

                return dbPromise;
            };

            const readQueue = () => {
                try {
                    const data = JSON.parse(localStorage.getItem(queueKey) || '[]');
                    return Array.isArray(data) ? data : [];
                } catch (error) {
                    return [];
                }
            };

            const writeQueue = (queue) => {
                localStorage.setItem(queueKey, JSON.stringify(queue));
            };

            const idbAdd = async (payload) => {
                const db = await openQueueDb();
                if (!db) {
                    return false;
                }

                return new Promise((resolve) => {
                    const tx = db.transaction(idbStore, 'readwrite');
                    tx.objectStore(idbStore).add(payload);
                    tx.oncomplete = () => resolve(true);
                    tx.onerror = () => resolve(false);
                    tx.onabort = () => resolve(false);
                });
            };

            const idbGetAll = async () => {
                const db = await openQueueDb();
                if (!db) {
                    return [];
                }

                return new Promise((resolve) => {
                    const tx = db.transaction(idbStore, 'readonly');
                    const request = tx.objectStore(idbStore).getAll();
                    request.onsuccess = () => resolve(Array.isArray(request.result) ? request.result : []);
                    request.onerror = () => resolve([]);
                });
            };

            const idbDelete = async (id) => {
                const db = await openQueueDb();
                if (!db) {
                    return;
                }

                await new Promise((resolve) => {
                    const tx = db.transaction(idbStore, 'readwrite');
                    tx.objectStore(idbStore).delete(id);
                    tx.oncomplete = () => resolve(true);
                    tx.onerror = () => resolve(false);
                    tx.onabort = () => resolve(false);
                });
            };

            const idbCount = async () => {
                const db = await openQueueDb();
                if (!db) {
                    return 0;
                }

                return new Promise((resolve) => {
                    const tx = db.transaction(idbStore, 'readonly');
                    const request = tx.objectStore(idbStore).count();
                    request.onsuccess = () => resolve(Number(request.result || 0));
                    request.onerror = () => resolve(0);
                });
            };

            const fileToDataUrl = (file) => new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(String(reader.result || ''));
                reader.onerror = () => reject(new Error('Falha ao ler arquivo para modo offline.'));
                reader.readAsDataURL(file);
            });

            const dataUrlToFile = (dataUrl, fileName, mimeType) => {
                const parts = String(dataUrl).split(',');
                const base64 = parts[1] || '';
                const binary = atob(base64);
                const len = binary.length;
                const bytes = new Uint8Array(len);

                for (let i = 0; i < len; i += 1) {
                    bytes[i] = binary.charCodeAt(i);
                }

                return new File([bytes], fileName || 'arquivo.bin', { type: mimeType || 'application/octet-stream' });
            };

            const updateNetStatus = async () => {
                const online = navigator.onLine;
                const pending = readQueue().length + await idbCount();
                const suffix = pending > 0 ? ` · fila ${pending}` : '';
                const text = `${online ? 'Online' : 'Offline'}${suffix}`;
                if (netStatus) {
                    netStatus.textContent = text;
                    netStatus.classList.toggle('offline', !online);
                }
                const netStatusDrawer = document.getElementById('netStatusDrawer');
                if (netStatusDrawer) {
                    netStatusDrawer.textContent = text;
                    netStatusDrawer.classList.toggle('offline', !online);
                }
            };

            const queueForm = async (form) => {
                const formData = new FormData(form);
                const payload = {
                    action: form.action,
                    method: (form.method || 'POST').toUpperCase(),
                    entries: [],
                    createdAt: Date.now(),
                };

                let hasBinary = false;

                for (const [key, value] of formData.entries()) {
                    if (value instanceof File) {
                        if (value.size > 0) {
                            hasBinary = true;

                            if (value.size > maxOfflineFileSizeBytes) {
                                alert('Arquivo muito grande para fila offline. Limite aproximado: 6 MB por arquivo.');
                                return false;
                            }

                            const dataUrl = await fileToDataUrl(value);
                            payload.entries.push({
                                key,
                                type: 'file',
                                fileName: value.name,
                                mimeType: value.type || 'application/octet-stream',
                                size: value.size,
                                dataUrl,
                            });
                        }

                        continue;
                    }

                    payload.entries.push({
                        key,
                        type: 'text',
                        value: String(value),
                    });
                }

                if (hasBinary) {
                    if (!canUseIndexedDb()) {
                        alert('Este navegador não suporta fila offline com arquivos.');
                        return false;
                    }

                    const saved = await idbAdd(payload);
                    if (!saved) {
                        alert('Não foi possível salvar o envio com arquivo na fila offline.');
                        return false;
                    }
                } else {
                    const queue = readQueue();
                    queue.push({
                        action: payload.action,
                        method: payload.method,
                        entries: payload.entries
                            .filter((entry) => entry.type === 'text')
                            .map((entry) => [entry.key, entry.value]),
                        createdAt: payload.createdAt,
                    });
                    writeQueue(queue);
                }

                alert('Sem internet: ação salva em fila local. Vamos sincronizar automaticamente quando ficar online.');
                return true;
            };

            const flushTextQueue = async () => {
                const queue = readQueue();
                if (queue.length === 0) {
                    return { sent: 0, remaining: 0 };
                }

                const remaining = [];
                let sent = 0;

                for (const item of queue) {
                    try {
                        const body = new URLSearchParams(item.entries);
                        const response = await fetch(item.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json, text/html',
                            },
                            credentials: 'same-origin',
                            body: body.toString(),
                        });

                        if (response.ok || response.redirected) {
                            sent += 1;
                        } else {
                            remaining.push(item);
                        }
                    } catch (error) {
                        remaining.push(item);
                    }
                }

                writeQueue(remaining);
                return { sent, remaining: remaining.length };
            };

            const flushBinaryQueue = async () => {
                const items = await idbGetAll();
                if (items.length === 0) {
                    return { sent: 0, remaining: 0 };
                }

                let sent = 0;
                let remaining = 0;

                const ordered = [...items].sort((a, b) => Number(a.createdAt || 0) - Number(b.createdAt || 0));

                for (const item of ordered) {
                    try {
                        const formData = new FormData();

                        (item.entries || []).forEach((entry) => {
                            if (entry.type === 'file') {
                                const file = dataUrlToFile(entry.dataUrl, entry.fileName, entry.mimeType);
                                formData.append(entry.key, file, entry.fileName);
                                return;
                            }

                            formData.append(entry.key, String(entry.value ?? ''));
                        });

                        const response = await fetch(item.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json, text/html',
                            },
                            credentials: 'same-origin',
                            body: formData,
                        });

                        if (response.ok || response.redirected) {
                            sent += 1;
                            await idbDelete(item.id);
                        } else {
                            remaining += 1;
                        }
                    } catch (error) {
                        remaining += 1;
                    }
                }

                return { sent, remaining };
            };

            const flushQueue = async () => {
                if (!navigator.onLine) {
                    return;
                }

                const textResult = await flushTextQueue();
                const binaryResult = await flushBinaryQueue();
                await updateNetStatus();

                const totalSent = textResult.sent + binaryResult.sent;
                const totalRemaining = textResult.remaining + binaryResult.remaining;

                if (totalSent > 0 && totalRemaining === 0) {
                    window.location.reload();
                }
            };

            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                if (form.getAttribute('data-offline-queue') === 'off') {
                    return;
                }

                const method = (form.method || 'GET').toUpperCase();
                if (!['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
                    return;
                }

                const actionPath = new URL(form.action, window.location.origin).pathname.toLowerCase();
                if (actionPath.includes('/login') || actionPath.includes('/logout')) {
                    return;
                }

                if (navigator.onLine) {
                    return;
                }

                event.preventDefault();
                queueForm(form).finally(() => {
                    updateNetStatus();
                });
            });

            updateNetStatus();
            window.addEventListener('online', () => {
                updateNetStatus();
                flushQueue();
            });
            window.addEventListener('offline', updateNetStatus);

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }

            flushQueue();

            // Drawer navigation
            (() => {
                const btnMenu = document.getElementById('btnMenu');
                const drawerEl = document.getElementById('drawer');
                const drawerOverlayEl = document.getElementById('drawerOverlay');
                const drawerCloseEl = document.getElementById('drawerClose');

                const openDrawer = () => document.body.classList.add('drawer-open');
                const closeDrawer = () => document.body.classList.remove('drawer-open');

                btnMenu?.addEventListener('click', openDrawer);
                drawerCloseEl?.addEventListener('click', closeDrawer);
                drawerOverlayEl?.addEventListener('click', closeDrawer);

                if (drawerEl) {
                    drawerEl.querySelectorAll('a').forEach((a) => {
                        a.addEventListener('click', closeDrawer);
                    });
                }

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeDrawer();
                });
            })();
        })();
    </script>
</body>
</html>
