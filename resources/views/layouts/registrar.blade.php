<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="check-notifications" content="{{ session('check_notifications') ? 'true' : 'false' }}">

    {{-- Bell notification URLs --}}
    <meta name="notif-fetch-url"   content="{{ route('registrar.notifications.index') }}">
    <meta name="notif-read-url"    content="{{ route('registrar.notifications.markOneRead', ['id' => '__ID__']) }}">
    <meta name="notif-readall-url" content="{{ route('registrar.notifications.markAllRead') }}">

    <title>@yield('title', 'CCST DocRequest') — Registrar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Volkhov:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* Green Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #1B6B3A;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #134D29;
        }

        :root {
            --green-dark:   #1B6B3A;
            --blue-main:    #1A9FE0;
            --blue-dark:    #0D7FBF;
            --yellow:       #F5C518;
            --text-dark:    #1A1A1A;
            --text-gray:    #666666;
            --border:       #D0DDD0;
            --white:        #FFFFFF;
            --sidebar-w:    240px;
            --right-w:      350px;
            --header-h:     50px;
            --footer-h:     35px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f4;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── HEADER ── */
        .site-header {
            height: var(--header-h);
            background: linear-gradient(to right, #0C6637 13%, #9CDBBA 81%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 200;
            overflow: visible;
        }

        .site-header .header-title {
            font-family: 'Volkhov', serif;
            font-size: 0.96rem;
            color: var(--white);
            letter-spacing: 0.3px;
            flex: 1;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
            padding-right: 4px;
            position: relative;
        }

        /* ── BELL BUTTON ── */
        .bell-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            position: relative;
            padding: 4px;
            line-height: 1;
            opacity: 0.9;
            transition: opacity 0.15s;
        }

        .bell-btn:hover { opacity: 1; }

        .bell-badge {
            position: absolute;
            top: -2px;
            right: -4px;
            background: #DC3545;
            color: white;
            font-size: 0.55rem;
            font-weight: 700;
            min-width: 14px;
            height: 14px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 2px;
        }

        /* ── BELL DROPDOWN ── */
        .bell-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 320px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 9999;
            overflow: hidden;
        }

        .bell-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: #F0F7F0;
            border-bottom: 1px solid var(--border);
        }

        .bell-dropdown-header span {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--green-dark);
        }

        .bell-mark-all {
            background: none;
            border: none;
            font-size: 0.76rem;
            color: var(--blue-main);
            cursor: pointer;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        .bell-mark-all:hover { text-decoration: underline; }

        .bell-dropdown-body {
            max-height: 360px;
            overflow-y: auto;
        }

        .notif-item {
            display: block;
            padding: 10px 14px;
            border-bottom: 1px solid #f2f2f2;
            text-decoration: none;
            color: var(--text-dark);
            transition: background 0.12s;
        }

        .notif-item:hover { background: #f7faf7; }
        .notif-item:last-child { border-bottom: none; }

        .notif-item .notif-msg {
            font-size: 0.83rem;
            line-height: 1.4;
        }

        .notif-item .notif-time {
            font-size: 0.73rem;
            color: var(--text-gray);
            margin-top: 3px;
        }

        .bell-empty {
            text-align: center;
            padding: 28px 14px;
            color: var(--text-gray);
            font-size: 0.83rem;
        }

        .bell-empty i {
            display: block;
            font-size: 1.6rem;
            margin-bottom: 6px;
            opacity: 0.5;
        }

        /* ── PAGE BODY ── */
        .page-body {
            display: flex;
            flex: 1;
            min-height: 0;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--white);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 18px 0 14px;
            position: fixed;
            top: var(--header-h);
            left: 0;
            height: calc(100vh - var(--header-h) - var(--footer-h));
            overflow-y: auto;
            z-index: 100;
            scrollbar-width: none; /* Hide for Firefox */
        }
        .sidebar::-webkit-scrollbar { display: none; } /* Hide for Chrome/Safari */

        .sidebar-logo {
            text-align: center;
            padding: 20px 12px 25px;
            margin-bottom: 16px;
        }

        .sidebar-logo img {
            width: 125px;
            height: 125px;
            object-fit: contain;
        }

        .sidebar-avatar {
            text-align: center;
            padding: 0 12px 38px;
            border-bottom: 1px solid var(--border);
            margin-bottom: -8px;
        }

        .avatar-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: var(--green-dark);
            color: white;
            font-size: 1.79rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            overflow: hidden;
            border: 2px solid var(--border);
        }

        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }

        .user-name {
            font-weight: 700;
            font-size: 0.99rem;
            color: var(--text-dark);
            line-height: 1.3;
        }

        .role-badge {
            display: inline-block;
            background: var(--yellow);
            color: var(--text-dark);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 20px;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .sidebar-nav { flex: 1; padding: 8px 0; }

        .sidebar-nav a {
            display: block;
            padding: 12px 16px;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.98rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            text-align: center;
            transition: all 0.15s;
        }

        .sidebar-nav a:hover {
            color: var(--green-dark);
            background: rgba(12,102,55,0.05);
        }

        .sidebar-nav a.active {
            color: var(--text-dark);
            font-weight: 700;
            border-left-color: var(--green-dark);
            background: rgba(12,102,55,0.06);
        }

        .sidebar-footer {
            padding: 12px 14px 0;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: center;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-gray);
            font-family: 'Poppins', sans-serif;
            font-size: 0.90rem;
            font-weight: 500;
            padding: 4px 0;
            text-decoration: none;
            transition: color 0.15s;
        }

        .logout-btn:hover { color: var(--text-dark); }

        .logout-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--blue-main);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.92rem;
            flex-shrink: 0;
        }

        /* ── MAIN CONTENT with background image ── */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-w);
            margin-right: var(--right-w);
            padding: 28px 24px;
            min-width: 0;
            overflow-y: auto;
            overflow-x: hidden;
            background: url('{{ asset("images/page-bg.jpeg") }}') center/cover no-repeat fixed;
            position: relative;
            height: calc(100vh - var(--header-h) - var(--footer-h));
        }

        /* Semi-transparent overlay for readability */
        .main-content::before {
            content: '';
            position: fixed;
            top: var(--header-h);
            left: var(--sidebar-w);
            right: var(--right-w);
            bottom: var(--footer-h);
            background: rgba(255, 255, 255, 0.45);
            pointer-events: none;
            z-index: 0;
        }

        /* Ensure content sits above overlay */
        .main-content > * {
            position: relative;
            z-index: 1;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: var(--right-w);
            position: fixed;
            top: var(--header-h);
            right: 0;
            bottom: var(--footer-h);
            overflow: hidden;
            background: transparent;
            z-index: 50;
            display: flex;
            align-items: stretch;
            padding: 0;
        }


        .right-panel-steps-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        /* ── SHARED CARD STYLES ── */
        .ccst-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 14px;
        }

        .right-panel .ccst-card {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 10px;
        }

        .right-panel .ccst-card-body {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            color: white;
        }

        .right-panel .rp-stat-row {
            border-bottom-color: rgba(255,255,255,0.2);
            color: white;
        }

        .right-panel .rp-guide-step {
            border-bottom-color: rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.92);
        }

        .ccst-card-header {
            background: var(--green-dark);
            color: white;
            font-size: 0.86rem;
            font-weight: 700;
            padding: 9px 16px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .ccst-card-header.yellow {
            background: var(--yellow);
            color: var(--text-dark);
        }

        .ccst-card-header.blue {
            background: var(--blue-main);
            color: white;
        }

        .ccst-card-body {
            padding: 12px 14px;
            font-size: 0.90rem;
            line-height: 1.6;
            color: var(--text-dark);
        }

        /* ── FLASH MESSAGES ── */
        .flash-container { margin-bottom: 16px; }

        /* ── FOOTER ── */
        .site-footer {
            height: var(--footer-h);
            background: linear-gradient(to right, #9CDBBA 13%, #0C6637 81%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
            z-index: 200;
            width: 100%;
        }

        .site-footer span {
            font-size: 0.72rem;
            color: var(--white);
            font-weight: 500;
        }

        /* Notification Styles */
        .notif-item.unread {
            background: #f5fdf7;
            border-left: 3px solid #1B6B3A;
        }

        .notif-item:hover {
            background: #f7faf7;
        }

        .notif-item .notif-msg {
            font-size: 0.85rem;
            color: #1a1a1a;
            line-height: 1.4;
        }

        .notif-item .notif-time {
            font-size: 0.72rem;
            color: #999;
            margin-top: 4px;
        }

        .bell-empty {
            text-align: center;
            padding: 28px 14px;
            color: var(--text-gray);
            font-size: 0.83rem;
        }

        /* Scrollable table body */
        .table-scroll-body {
            max-height: 500px;
            overflow-y: auto;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .right-panel {
                display: none;
            }
            .main-content {
                margin-right: 0;
            }
            .main-content::before {
                right: 0;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
                z-index: 1000;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }


    </style>

    @stack('styles')
</head>
<body>

    {{-- HEADER --}}
    <header class="site-header">
        <span class="header-title">
            Clark College of Science and Technology's Document Request and Tracking System
        </span>
        <div class="header-right">

            <button class="bell-btn" id="bellBtn">
                <i class="bi bi-bell-fill"></i>
                <span class="bell-badge" id="bellBadge" style="display:none;">0</span>
            </button>
            <div class="bell-dropdown" id="bellDropdown">
                <div class="bell-dropdown-header">
                    <span><i class="bi bi-bell me-1"></i> Notifications</span>
                    <button class="bell-mark-all" id="bellMarkAll">Mark all as read</button>
                </div>
                <div class="bell-dropdown-body" id="bellDropdownBody">
                    <div class="bell-empty">No new notifications</div>
                </div>
            </div>

        </div>
    </header>

    {{-- PAGE BODY --}}
    <div class="page-body">

        {{-- SIDEBAR --}}
        <aside class="sidebar">

            <div class="sidebar-logo">
                <img src="{{ asset('images/ccst-logo.png') }}" alt="CCST" onerror="this.style.display='none'">
            </div>

            <div class="sidebar-avatar">
                <div class="avatar-circle">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ route('registrar.account.photo') }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                    @endif
                </div>
                <div class="user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                <span class="role-badge">Registrar</span>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('registrar.dashboard') }}" class="{{ request()->routeIs('registrar.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('registrar.requests.index') }}" class="{{ request()->routeIs('registrar.requests.*') ? 'active' : '' }}">
                    Requests
                </a>
                <a href="{{ route('registrar.walkin.create') }}" class="{{ request()->routeIs('registrar.walkin.*') ? 'active' : '' }}">
                    Walk-In Mode
                </a>
                <a href="{{ route('registrar.calendar') }}" class="{{ request()->routeIs('registrar.calendar') ? 'active' : '' }}">
                    Calendar
                </a>
                <a href="{{ route('registrar.students.index') }}" class="{{ request()->routeIs('registrar.students.*') ? 'active' : '' }}">
                    Students
                </a>
                <a href="{{ route('registrar.document-types.index') }}" class="{{ request()->routeIs('registrar.document-types.*') ? 'active' : '' }}">
                    Documents
                </a>
                <a href="{{ route('registrar.reports.index') }}" class="{{ request()->routeIs('registrar.reports.*') ? 'active' : '' }}">
                    Reports
                </a>
                <a href="{{ route('registrar.account') }}" class="{{ request()->routeIs('registrar.account') ? 'active' : '' }}">
                    Account
                </a>
                @if(auth()->check() && auth()->user()->is_admin)
                <a href="{{ route('registrar.manage.index') }}" class="{{ request()->routeIs('registrar.manage.*') ? 'active' : '' }}">
                    Manage Staff
                </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span class="logout-icon"><i class="bi bi-box-arrow-right"></i></span>
                        Log Out
                    </button>
                </form>
            </div>

        </aside>

        {{-- MAIN CONTENT --}}
        <main class="main-content">
            <div class="flash-container">
                {{-- Only show error messages --}}
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
            @yield('content')
        </main>

        {{-- RIGHT PANEL --}}
        <aside class="right-panel">
            @yield('right-panel')
            <img src="{{ asset('images/4-easy-steps-to-process.png') }}"
                alt="4 Easy Steps to Process Documents"
                class="right-panel-steps-img">
        </aside>

    </div>

    {{-- FOOTER --}}
    <footer class="site-footer">
        <span>© Copyright 2026 Clark College of Science and Technology | Document Request and Tracking System</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    

    <script>
    // ═══════════════════════════════════════════════════════════════════════
    // CCST NOTIFICATION SYSTEM - Registrar
    // ═══════════════════════════════════════════════════════════════════════

    (function() {
        const FETCH_URL = '{{ route("registrar.notifications.index") }}';
        const READ_URL_TEMPLATE = '{{ route("registrar.notifications.markOneRead", ["id" => "__ID__"]) }}';
        const READ_ALL_URL = '{{ route("registrar.notifications.markAllRead") }}';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        let lastNotificationCount = 0;
        let hasAutoOpenedThisSession = false;
        
        const bellBtn = document.getElementById('bellBtn');
        const bellBadge = document.getElementById('bellBadge');
        const bellDropdown = document.getElementById('bellDropdown');
        const bellBody = document.getElementById('bellDropdownBody');
        const markAllBtn = document.getElementById('bellMarkAll');
        
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
        
        function updateBadge(count) {
            if (!bellBadge) return;
            if (count > 0) {
                bellBadge.textContent = count > 99 ? '99+' : count;
                bellBadge.style.display = 'flex';
            } else {
                bellBadge.style.display = 'none';
            }
        }
        
        function renderNotificationsWithHighlight(notifications, highlightId = null) {
            if (!bellBody) return;
            
            const notifArray = notifications ? (Array.isArray(notifications) ? notifications : Object.values(notifications)) : [];
            
            if (notifArray.length === 0) {
                bellBody.innerHTML = `<div class="bell-empty"><i class="bi bi-bell-slash"></i>No notifications yet.</div>`;
                return;
            }
            
            let html = '';
            notifArray.forEach(function(notif) {
                const unreadClass = notif.read ? '' : 'unread';
                const isHighlighted = (highlightId && notif.id === highlightId);
                const highlightStyle = isHighlighted ? 'background: rgba(255, 241, 182, 1.0); border-left: 4px solid #F5C518;' : '';
                
                html += `<div class="notif-item ${unreadClass}" data-id="${escapeHtml(notif.id)}" data-url="${escapeHtml(notif.url)}" style="cursor:pointer; padding:12px 14px; border-bottom:1px solid #f0f0f0; ${highlightStyle}">
                            <div style="flex:1;">
                                <div class="notif-msg" style="font-size:0.85rem; color:#1a1a1a; line-height:1.4;">${escapeHtml(notif.message)}</div>
                                <div class="notif-time" style="font-size:0.72rem; color:#999; margin-top:4px;">${escapeHtml(notif.time)}</div>
                            </div>
                            ${isHighlighted ? '<div style="margin-left:8px;"><i class="bi bi-star-fill" style="color:#F5C518; font-size:0.7rem;"></i></div>' : ''}
                        </div>`;
            });
            
            bellBody.innerHTML = html;
            
            document.querySelectorAll('.notif-item').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = this.dataset.id;
                    const url = this.dataset.url;
                    
                    if (id && READ_URL_TEMPLATE) {
                        const readUrl = READ_URL_TEMPLATE.replace('__ID__', id);
                        fetch(readUrl, {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        }).finally(function() {
                            if (url && url !== '#') window.location.href = url;
                        });
                    } else if (url && url !== '#') {
                        window.location.href = url;
                    }
                });
            });
        }
        
        function loadAndShowNotifications() {
            fetch(FETCH_URL, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_TOKEN },
                credentials: 'same-origin'
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                renderNotificationsWithHighlight(data.notifications || []);
                updateBadge(data.unread || 0);
            })
            .catch(function(err) { console.error('Failed to load notifications:', err); });
        }
        
        function markAllAsRead() {
            fetch(READ_ALL_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
            .then(function() {
                updateBadge(0);
                if (bellDropdown && bellDropdown.style.display === 'block') loadAndShowNotifications();
            })
            .catch(function(err) { console.error('Failed to mark all as read:', err); });
        }
        
        if (bellBtn) {
            bellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (bellDropdown.style.display === 'block') {
                    bellDropdown.style.display = 'none';
                } else {
                    loadAndShowNotifications();
                    bellDropdown.style.display = 'block';
                }
            });
        }
        
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                markAllAsRead();
            });
        }
        
        document.addEventListener('click', function(e) {
            if (bellBtn && !bellBtn.contains(e.target) && bellDropdown && !bellDropdown.contains(e.target)) {
                bellDropdown.style.display = 'none';
            }
        });
        
        // Initial badge count
        fetch('{{ route("registrar.notifications.index") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_TOKEN },
            credentials: 'same-origin'
        })
        .then(function(res) { return res.json(); })
        .then(function(data) { updateBadge(data.unread || 0); })
        .catch(function() {});
        
        // Check for immediate notification from form submission
        const shouldCheck = document.querySelector('meta[name="check-notifications"]')?.content === 'true';
        if (shouldCheck) {
            setTimeout(function() {
                fetch(FETCH_URL, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    credentials: 'same-origin'
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.notifications && data.notifications.length > 0) {
                        const newestId = data.notifications[0]?.id;
                        renderNotificationsWithHighlight(data.notifications, newestId);
                        updateBadge(data.unread || 0);
                        if (bellDropdown) {
                            bellDropdown.style.display = 'block';
                            setTimeout(function() { if (bellDropdown) bellDropdown.style.display = 'none'; }, 8000);
                        }
                    }
                });
                const meta = document.querySelector('meta[name="check-notifications"]');
                if (meta) meta.content = 'false';
            }, 500);
        }
        
        // Poll for new notifications every 10 seconds
        setInterval(function() {
            fetch(FETCH_URL, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_TOKEN },
                credentials: 'same-origin'
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                const newUnread = data.unread || 0;
                const hasNew = newUnread > lastNotificationCount;
                updateBadge(newUnread);
                if (hasNew && !hasAutoOpenedThisSession) {
                    hasAutoOpenedThisSession = true;
                    renderNotificationsWithHighlight(data.notifications || []);
                    if (bellDropdown) {
                        bellDropdown.style.display = 'block';
                        setTimeout(function() { if (bellDropdown) bellDropdown.style.display = 'none'; }, 8000);
                    }
                }
                lastNotificationCount = newUnread;
            })
            .catch(function(err) { console.error('Poll error:', err); });
        }, 10000);
    })();
    </script>

    @stack('scripts')
</body>
</html>