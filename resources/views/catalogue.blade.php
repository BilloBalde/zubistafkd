<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catalogue — FBK-Printing</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { font-family: 'Poppins', sans-serif; }

        .navbar-fixed {
            position: fixed; top: 0; width: 100%; z-index: 50;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        }

        /* ── Sidebar ── */
        .sidebar {
            background: #fff;
            border: 1.5px solid #f3f4f6;
            border-radius: 16px;
            padding: 1.5rem;
            position: sticky;
            top: 90px;
        }
        .sidebar-title {
            font-size: 11px; font-weight: 700; color: #9ca3af;
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 16px;
        }
        .filter-group { margin-bottom: 1.5rem; }
        .filter-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 10px; display: block; }
        .filter-option { display: flex; align-items: center; gap: 8px; padding: 5px 0; cursor: pointer; }
        .filter-option input { width: 15px; height: 15px; accent-color: #d97706; cursor: pointer; }
        .filter-option span { font-size: 13px; color: #374151; }
        .filter-badge { margin-left: auto; font-size: 11px; background: #f3f4f6; color: #6b7280; padding: 1px 8px; border-radius: 20px; }
        .filter-divider { border: none; border-top: 1px solid #f3f4f6; margin: 0 0 1.25rem; }
        .price-inputs { display: flex; gap: 8px; align-items: center; }
        .price-inputs input {
            width: 80px; padding: 6px 10px; font-size: 12px;
            border: 1.5px solid #e5e7eb; border-radius: 8px;
            background: #fff; color: #374151; outline: none;
            font-family: 'Poppins', sans-serif;
        }
        .price-inputs input:focus { border-color: #f59e0b; }
        .price-sep { font-size: 12px; color: #9ca3af; }
        .reset-btn {
            width: 100%; padding: 8px; font-size: 12px; font-weight: 600;
            color: #d97706; border: 1.5px solid #fcd34d;
            border-radius: 8px; background: none; cursor: pointer;
            margin-top: 4px; transition: background 0.2s;
        }
        .reset-btn:hover { background: #fef3c7; }

        /* ── Grille produits ── */
        .prod-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        @media (max-width: 1280px) { .prod-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px)  { .prod-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px)  { .prod-grid { grid-template-columns: repeat(1, 1fr); } }
        .prod-card {
            background: #fff; border: 1.5px solid #f3f4f6;
            border-radius: 22px; overflow: hidden;
            transition: all 0.28s cubic-bezier(.34,1.1,.64,1); cursor: pointer;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            display: flex; flex-direction: column;
        }
        .prod-card:hover {
            border-color: #f59e0b;
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(245,158,11,0.16);
        }
        .prod-img {
            height: 220px; display: flex; align-items: center;
            justify-content: center; position: relative;
            background: #fafafa; overflow: hidden;
        }
        .prod-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.35s, filter 0.3s; }
        .prod-card:hover .prod-img img { transform: scale(1.07); }
        .prod-img img.lazy { filter: blur(10px); opacity: 0.7; }
        .prod-img img.lazy.loaded { filter: blur(0); opacity: 1; transition: filter 0.4s, opacity 0.4s; }
        .prod-img .no-img { font-size: 56px; }
        .prod-badge {
            position: absolute; top: 12px; left: 12px;
            font-size: 10px; font-weight: 700;
            padding: 4px 12px; border-radius: 20px;
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .badge-promo { background: rgba(254,243,199,0.97); color: #92400e; }
        .badge-best  { background: rgba(209,250,229,0.97); color: #065f46; }
        .prod-cat-overlay {
            position: absolute; bottom: 10px; right: 10px;
            font-size: 10px; font-weight: 700; color: #fff;
            background: rgba(217,119,6,0.88);
            padding: 3px 10px; border-radius: 20px;
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.18);
            letter-spacing: 0.02em;
        }
        .prod-info { padding: 16px 16px 18px; display: flex; flex-direction: column; flex: 1; }
        .prod-name {
            font-size: 14px; font-weight: 700; color: #1f2937;
            margin-bottom: 8px; line-height: 1.45;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .prod-stars {
            font-size: 12px; color: #f59e0b; margin-bottom: 10px;
            display: flex; align-items: center; gap: 4px;
        }
        .prod-stars .rating-num { color: #9ca3af; font-size: 11px; font-weight: 500; }
        .prod-price-row {
            display: flex; align-items: baseline; gap: 8px;
            margin-bottom: 12px; flex-wrap: wrap;
        }
        .prod-price { font-size: 20px; font-weight: 800; color: #d97706; }
        .prod-price-old { font-size: 12px; color: #9ca3af; text-decoration: line-through; }
        .promo-countdown {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; font-weight: 600; color: #dc2626;
            background: #fef2f2; border: 1px solid #fca5a5;
            border-radius: 6px; padding: 3px 8px; margin-bottom: 8px;
        }
        .prod-btns { display: flex; gap: 8px; margin-top: auto; }
        .prod-btn {
            flex: 1; padding: 11px 6px; font-size: 12px;
            font-weight: 700; text-align: center;
            background: linear-gradient(135deg, #f5a962, #d97706);
            color: #fff; border: none; border-radius: 12px; cursor: pointer;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 4px 14px rgba(217,119,6,0.25);
        }
        .prod-btn:hover { opacity: 0.9; transform: scale(1.03); box-shadow: 0 6px 20px rgba(217,119,6,0.35); }
        .prod-btn-order {
            flex: 1; padding: 11px 6px; font-size: 12px;
            font-weight: 700; text-align: center;
            background: none; color: #d97706;
            border: 2px solid #d97706; border-radius: 12px; cursor: pointer;
            transition: all 0.2s; font-family: 'Poppins', sans-serif;
        }
        .prod-btn-order:hover { background: #d97706; color: #fff; transform: scale(1.03); box-shadow: 0 4px 14px rgba(217,119,6,0.25); }

        /* ── Qty widget ── */
        .qty-widget { border-top: 1px solid #f3f4f6; padding-top: 10px; margin-bottom: 10px; }
        .qty-row { display:flex; align-items:center; gap:6px; margin-bottom:8px; }
        .qty-label { font-size:11px; color:#6b7280; font-weight:500; white-space:nowrap; }
        .qty-input {
            width:50px; padding:4px 6px; font-size:13px; font-weight:600; text-align:center;
            border:1.5px solid #e5e7eb; border-radius:8px; background:#fff; color:#374151;
            font-family:'Poppins',sans-serif; outline:none; transition:border-color 0.2s;
        }
        .qty-input:focus { border-color:#f59e0b; }
        .qty-btn {
            width:28px; height:28px; flex-shrink:0;
            background:#f3f4f6; border:none; border-radius:7px;
            display:flex; align-items:center; justify-content:center;
            cursor:pointer; font-size:16px; line-height:1; color:#374151;
            transition:background 0.2s, color 0.2s; font-family:'Poppins',sans-serif;
        }
        .qty-btn:hover { background:#fef3c7; color:#d97706; }
        .disc-info { min-height:32px; }
        .disc-badge {
            display:inline-flex; align-items:center; gap:3px;
            font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px;
            background:rgba(254,243,199,0.95); color:#92400e; margin-bottom:3px;
            animation:badgePop 0.25s cubic-bezier(.34,1.56,.64,1);
        }
        .disc-saving { font-size:10px; color:#059669; font-weight:600; margin-top:2px; }
        @keyframes badgePop { from{transform:scale(0.7);opacity:0} to{transform:scale(1);opacity:1} }
        .price-animate { animation:priceFlip 0.28s ease; }
        @keyframes priceFlip { 0%{opacity:0;transform:translateY(-5px)} 100%{opacity:1;transform:translateY(0)} }

        .empty-state { text-align: center; padding: 4rem 1rem; color: #9ca3af; font-size: 14px; }
        .empty-state i { font-size: 40px; margin-bottom: 16px; display: block; color: #d1d5db; }

        /* ── Search bar ── */
        .search-bar {
            display: flex; align-items: center;
            border: 1.5px solid #e5e7eb; border-radius: 12px;
            overflow: hidden; background: #fff;
            transition: border-color 0.2s;
        }
        .search-bar:focus-within { border-color: #f59e0b; }
        .search-bar input {
            flex: 1; padding: 10px 16px; font-size: 13px;
            border: none; outline: none; background: transparent;
            font-family: 'Poppins', sans-serif; color: #374151;
        }
        .search-bar button {
            padding: 10px 16px; background: #f59e0b; border: none;
            color: #fff; cursor: pointer; transition: background 0.2s;
        }
        .search-bar button:hover { background: #d97706; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 6px; justify-content: center; margin-top: 2rem; flex-wrap: wrap; }
        .page-btn-item {
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600; cursor: pointer;
            border: 1.5px solid #e5e7eb; background: #fff; color: #374151;
            transition: all 0.2s;
        }
        .page-btn-item:hover, .page-btn-item.active {
            background: #f59e0b; border-color: #f59e0b; color: #fff;
        }

        @media (max-width: 768px) {
            .catalogue-layout { grid-template-columns: 1fr !important; }
            .sidebar { position: static !important; }
        }
    </style>
</head>
<body class="bg-gray-50">

    {{-- NAVBAR --}}
    <nav class="navbar-fixed">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ route('accueil') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('assets/img/fbk.png') }}" alt="FBK-Printing" class="h-10 w-auto object-contain">
                    <span class="text-xl font-bold text-gray-800 hidden sm:inline">FBK-Printing</span>
                </a>
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('accueil') }}" class="text-gray-700 font-medium hover:text-amber-600 transition">Accueil</a>
                    <a href="{{ route('products.index') }}" class="text-amber-600 font-semibold border-b-2 border-amber-500 pb-1">Catalogue</a>
                    <a href="{{ route('accueil') }}#contact" class="text-gray-700 font-medium hover:text-amber-600 transition">Contact</a>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        @if(Auth::user()->isCustomer())
                            <form method="POST" action="{{ route('shop.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-amber-600 transition font-medium text-sm">
                                    <i class="fas fa-sign-out-alt text-xl"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('home') }}" class="text-gray-700 hover:text-amber-600 transition font-medium text-sm">
                                <i class="fas fa-tachometer-alt text-xl"></i>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('otp.login') }}" class="text-gray-700 hover:text-amber-600 transition font-medium text-sm">
                            <i class="fas fa-sign-in-alt text-xl"></i>
                            <span class="hidden sm:inline ml-1">Connexion</span>
                        </a>
                    @endauth
                    <a href="{{ route('panier') }}" class="relative p-2 text-gray-700 hover:text-amber-600 transition">
                        <i class="fas fa-shopping-bag text-xl"></i>
                        <span id="cart-count" class="absolute top-0 right-0 bg-amber-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            {{ count(session('cart', [])) }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO BANNER --}}
    <div class="pt-20 bg-gradient-to-r from-amber-500 to-amber-600">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-1">Notre Catalogue</h1>
                    <p class="text-white/80 text-sm">
                        <span id="total-count">{{ count($allProducts) }}</span> produits disponibles
                    </p>
                </div>
                {{-- Barre de recherche --}}
                <div class="w-full md:max-w-sm">
                    <div class="search-bar">
                        <input type="text" id="search-input" placeholder="Rechercher un produit…" oninput="applyFilters()">
                        <button><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CONTENU PRINCIPAL --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid catalogue-layout gap-6" style="grid-template-columns: 240px 1fr; align-items: start;">

            {{-- SIDEBAR FILTRES --}}
            <aside class="sidebar">
                <p class="sidebar-title"><i class="fas fa-sliders-h mr-2"></i>Filtres</p>

                <div class="filter-group">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                        <span class="filter-label" style="margin-bottom:0">Catégories</span>
                        <a href="{{ route('public.categories') }}"
                           style="font-size:11px;font-weight:600;color:#f59e0b;text-decoration:none;display:flex;align-items:center;gap:4px;padding:3px 8px;border:1px solid #fcd34d;border-radius:20px;transition:all 0.2s;"
                           onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-th-large" style="font-size:10px;"></i> Toutes
                        </a>
                    </div>
                    <label class="filter-option">
                        <input type="radio" name="cat-filter" value="" checked onchange="applyFilters()">
                        <span>Toutes</span>
                    </label>
                    @foreach($categories as $cat)
                    <label class="filter-option">
                        <input type="radio" name="cat-filter" value="{{ $cat->name }}" onchange="applyFilters()">
                        <span>{{ $cat->name }}</span>
                        <span class="filter-badge">{{ $cat->products_count }}</span>
                    </label>
                    @endforeach
                </div>

                <hr class="filter-divider">

                <div class="filter-group">
                    <span class="filter-label">Prix (GNF)</span>
                    <div class="price-inputs">
                        <input type="number" id="min-price" placeholder="Min" min="0" oninput="applyFilters()">
                        <span class="price-sep">—</span>
                        <input type="number" id="max-price" placeholder="Max" min="0" oninput="applyFilters()">
                    </div>
                </div>

                <hr class="filter-divider">

                <div class="filter-group">
                    <span class="filter-label">Trier par</span>
                    <select id="sort-select" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-amber-400" onchange="applyFilters()" style="font-family:'Poppins',sans-serif;">
                        <option value="default">Plus récents</option>
                        <option value="price-asc">Prix croissant</option>
                        <option value="price-desc">Prix décroissant</option>
                        <option value="name-asc">Nom A → Z</option>
                        <option value="promo">Promotions d'abord</option>
                    </select>
                </div>

                <hr class="filter-divider">

                <div class="filter-group">
                    <span class="filter-label">Type</span>
                    <label class="filter-option">
                        <input type="radio" name="type-filter" value="" checked onchange="applyFilters()">
                        <span>Tous</span>
                    </label>
                    <label class="filter-option">
                        <input type="radio" name="type-filter" value="promo" onchange="applyFilters()">
                        <span>En promotion</span>
                    </label>
                </div>

                <button class="reset-btn" onclick="resetFilters()">
                    <i class="fas fa-undo mr-1"></i> Réinitialiser
                </button>
            </aside>

            {{-- GRILLE PRODUITS --}}
            <div>
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <span id="results-count" class="text-sm text-gray-500"></span>
                </div>
                <div class="prod-grid" id="prod-grid"></div>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
    </div>

    {{-- FOOTER SIMPLE --}}
    <footer class="bg-gray-900 text-gray-400 text-center py-6 text-sm mt-10">
        <p>&copy; {{ date('Y') }} FBK-Printing — <a href="{{ route('accueil') }}" class="hover:text-white transition">Retour à l'accueil</a></p>
    </footer>

    <script>
        const ALL_PRODUCTS = @json($allProducts);
        const PER_PAGE = 24;

        /* Auth state — évalué côté serveur une seule fois au chargement */
        const _auth = {
            loggedIn:    @json(auth()->check()),
            checkoutUrl: '{{ route("checkout") }}',
            loginUrl:    '{{ route("otp.login") }}',
            cartAddUrl:  '{{ url("/cart/add") }}',
            csrfToken:   document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        };
        let currentPage = 1;
        let filtered = [...ALL_PRODUCTS];

        function renderCard(p) {
            const rating    = parseFloat(p.rating) || 0;
            const starsHTML = [1,2,3,4,5].map(i =>
                `<span style="color:${i <= Math.floor(rating) ? '#f59e0b' : '#e5e7eb'}">★</span>`
            ).join('');
            const isPromo   = !!p.old_price;
            const badgeTxt  = isPromo ? `-${p.discount}%` : 'Top vente';
            const badgeCls  = isPromo ? 'badge-promo' : 'badge-best';
            const oldHTML   = p.old_price
                ? `<span class="prod-price-old">${Number(p.old_price).toLocaleString('fr-FR')} GNF</span>` : '';
            const imgHTML   = p.image
                ? `<img class="lazy" data-src="${p.image}" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect fill='%23f0f0f0' width='400' height='400'/%3E%3C/svg%3E" alt="${p.name}" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'">`
                : '';
            const countdownHTML = p.promo_ends_at
                ? `<div class="promo-countdown" data-ends="${p.promo_ends_at}">
                       <i class="fas fa-clock" style="font-size:10px;"></i> <span class="countdown-label">—</span>
                   </div>`
                : '';
            return `<div class="prod-card" onclick="window.location='/product/${p.id}'">
                <div class="prod-img">
                    <span class="prod-badge ${badgeCls}">${badgeTxt}</span>
                    ${imgHTML}
                    <span class="no-img" ${p.image ? 'style="display:none"' : ''}>🖨️</span>
                    <span class="prod-cat-overlay">${p.category_name ?? ''}</span>
                </div>
                <div class="prod-info">
                    <div class="prod-name">${p.name}</div>
                    <div class="prod-stars">
                        ${starsHTML}
                        <span class="rating-num">${rating.toFixed(1)}</span>
                    </div>
                    <div class="qty-widget" data-product-id="${p.id}" onclick="event.stopPropagation()">
                        <div class="qty-row">
                            <span class="qty-label">Qté&nbsp;:</span>
                            <button class="qty-btn" onclick="changeQty(this,-1)">−</button>
                            <input class="qty-input" type="number" value="1" min="1" max="9999"
                                   oninput="recalcPrice(this)">
                            <button class="qty-btn" onclick="changeQty(this,1)">+</button>
                        </div>
                        <div class="disc-info">
                            <div class="prod-price-row">
                                <span class="prod-price price-val">${Number(p.price).toLocaleString('fr-FR')} GNF</span>
                                ${oldHTML}
                            </div>
                            ${countdownHTML}
                        </div>
                    </div>
                    <div style="font-size:10px;color:#92400e;background:#fef3c7;border:1px solid #fde68a;border-radius:6px;padding:4px 8px;margin:4px 0 6px;display:flex;align-items:center;gap:5px;line-height:1.3;">
                        <i class="fas fa-tags" style="font-size:9px;color:#d97706;flex-shrink:0;"></i>
                        <span><strong>Remise dès 5 pcs</strong> &nbsp;·&nbsp; −3% → −10% sur la quantité</span>
                    </div>
                    <div class="prod-btns">
                        <button class="prod-btn" onclick="event.stopPropagation();addToCart(${p.id})">
                            <i class="fas fa-shopping-bag mr-1"></i> Panier
                        </button>
                        <button class="prod-btn-order" onclick="event.stopPropagation();orderNow(${p.id})">
                            <i class="fas fa-bolt mr-1"></i> Commander
                        </button>
                    </div>
                </div>
            </div>`;
        }

        function applyFilters() {
            const search   = document.getElementById('search-input').value.toLowerCase().trim();
            const catVal   = document.querySelector('input[name="cat-filter"]:checked')?.value ?? '';
            const typeVal  = document.querySelector('input[name="type-filter"]:checked')?.value ?? '';
            const minP     = parseFloat(document.getElementById('min-price').value) || 0;
            const maxP     = parseFloat(document.getElementById('max-price').value) || Infinity;
            const sort     = document.getElementById('sort-select').value;

            filtered = ALL_PRODUCTS.filter(p => {
                if (search && !p.name.toLowerCase().includes(search) && !(p.category_name ?? '').toLowerCase().includes(search)) return false;
                if (catVal && p.category_name !== catVal) return false;
                if (typeVal === 'promo' && !p.old_price) return false;
                if (p.price < minP || p.price > maxP) return false;
                return true;
            });

            if (sort === 'price-asc')  filtered.sort((a,b) => a.price - b.price);
            if (sort === 'price-desc') filtered.sort((a,b) => b.price - a.price);
            if (sort === 'name-asc')   filtered.sort((a,b) => a.name.localeCompare(b.name));
            if (sort === 'promo')      filtered.sort((a,b) => (b.discount??0) - (a.discount??0));

            currentPage = 1;
            render();
        }

        function render() {
            const start = (currentPage - 1) * PER_PAGE;
            const page  = filtered.slice(start, start + PER_PAGE);
            const grid  = document.getElementById('prod-grid');

            grid.innerHTML = page.length
                ? page.map(renderCard).join('')
                : `<div class="empty-state col-span-full">
                       <i class="fas fa-box-open"></i>
                       Aucun produit trouvé.
                   </div>`;

            document.getElementById('results-count').textContent =
                `${filtered.length} produit${filtered.length !== 1 ? 's' : ''} trouvé${filtered.length !== 1 ? 's' : ''}`;

            renderPagination();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function renderPagination() {
            const total = Math.ceil(filtered.length / PER_PAGE);
            const pag   = document.getElementById('pagination');
            if (total <= 1) { pag.innerHTML = ''; return; }

            let html = '';
            if (currentPage > 1)
                html += `<div class="page-btn-item" onclick="goPage(${currentPage-1})"><i class="fas fa-chevron-left text-xs"></i></div>`;
            for (let i = 1; i <= total; i++) {
                if (total > 7 && Math.abs(i - currentPage) > 2 && i !== 1 && i !== total) {
                    if (i === currentPage - 3 || i === currentPage + 3) html += `<div class="page-btn-item" style="cursor:default">…</div>`;
                    continue;
                }
                html += `<div class="page-btn-item ${i===currentPage?'active':''}" onclick="goPage(${i})">${i}</div>`;
            }
            if (currentPage < total)
                html += `<div class="page-btn-item" onclick="goPage(${currentPage+1})"><i class="fas fa-chevron-right text-xs"></i></div>`;
            pag.innerHTML = html;
        }

        function goPage(n) { currentPage = n; render(); }

        function resetFilters() {
            document.getElementById('search-input').value = '';
            document.querySelectorAll('input[name="cat-filter"]')[0].checked = true;
            document.querySelectorAll('input[name="type-filter"]')[0].checked = true;
            document.getElementById('min-price').value = '';
            document.getElementById('max-price').value = '';
            document.getElementById('sort-select').selectedIndex = 0;
            applyFilters();
        }

        /* ── Qty helpers ── */
        const _qtyTimers = new Map();

        function changeQty(btnEl, delta) {
            const input = btnEl.closest('.qty-row').querySelector('.qty-input');
            input.value = Math.max(1, (parseInt(input.value) || 1) + delta);
            recalcPrice(input);
        }

        function recalcPrice(inputEl) {
            const widget    = inputEl.closest('.qty-widget');
            if (!widget) return;
            const productId = widget.dataset.productId;
            const qty       = Math.max(1, parseInt(inputEl.value) || 1);
            inputEl.value   = qty;

            clearTimeout(_qtyTimers.get(widget));
            _qtyTimers.set(widget, setTimeout(() => {
                fetch(`/calculate-price?product_id=${productId}&quantity=${qty}`)
                    .then(r => r.json())
                    .then(data => _updateDiscountUI(widget, data))
                    .catch(() => {});
            }, 280));
        }

        function _updateDiscountUI(widget, data) {
            const discInfo = widget.querySelector('.disc-info');
            if (!discInfo) return;
            const fmt      = n => Number(n).toLocaleString('fr-FR');
            const rawTotal = data.unit_price * data.quantity;
            let html = '<div class="prod-price-row" style="flex-wrap:wrap;gap:4px;align-items:baseline;">';
            if (data.discount_percent > 0) {
                html += `<span class="disc-badge"><i class="fas fa-tag" style="font-size:8px;"></i>&nbsp;-${data.discount_percent}%</span>`;
            }
            html += `<span class="prod-price price-val price-animate">${fmt(data.final_price)} GNF</span>`;
            if (data.discount_percent > 0) {
                html += `<span class="prod-price-old">${fmt(rawTotal)} GNF</span>`;
            }
            html += '</div>';
            if (data.discount_percent > 0) {
                html += `<div class="disc-saving"><i class="fas fa-check-circle" style="font-size:9px;"></i> Économie&nbsp;: ${fmt(data.discount_amount)} GNF</div>`;
            }
            discInfo.innerHTML = html;
        }

        function _getQtyForProduct(id) {
            const widget = document.querySelector(`.qty-widget[data-product-id="${id}"]`);
            return widget ? Math.max(1, parseInt(widget.querySelector('.qty-input').value) || 1) : 1;
        }

        function addToCart(id) {
            if (!_auth.loggedIn) {
                window.location.href = _auth.loginUrl;
                return;
            }
            const qty = _getQtyForProduct(id);
            fetch(`${_auth.cartAddUrl}/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': _auth.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ quantity: qty })
            })
            .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then(data => {
                const badge = document.getElementById('cart-count');
                if (badge && data.count !== undefined) {
                    badge.textContent = data.count;
                    badge.style.transform = 'scale(1.5)';
                    setTimeout(() => badge.style.transform = 'scale(1)', 300);
                }
                showToast(`${qty} article${qty > 1 ? 's' : ''} ajouté${qty > 1 ? 's' : ''} au panier !`);
            })
            .catch(() => showToast('Impossible d\'ajouter ce produit.', true));
        }

        function orderNow(id) {
            if (!_auth.loggedIn) {
                window.location.href = `${_auth.loginUrl}?product_id=${id}`;
                return;
            }
            const qty = _getQtyForProduct(id);
            fetch(`${_auth.cartAddUrl}/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': _auth.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ quantity: qty })
            })
            .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
            .then(data => {
                const badge = document.getElementById('cart-count');
                if (badge && data.count !== undefined) {
                    badge.textContent = data.count;
                    badge.style.transform = 'scale(1.3)';
                    setTimeout(() => badge.style.transform = 'scale(1)', 300);
                }
                showToast('Produit ajouté ! Redirection...');
                setTimeout(() => { window.location.href = _auth.checkoutUrl; }, 500);
            })
            .catch(() => showToast('Impossible de passer la commande.', true));
        }

        function showToast(msg, isError = false) {
            let t = document.getElementById('toast');
            if (!t) {
                t = document.createElement('div');
                t.id = 'toast';
                t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;color:#fff;transition:opacity 0.4s;box-shadow:0 4px 20px rgba(0,0,0,0.15)';
                document.body.appendChild(t);
            }
            t.style.background = isError ? '#ef4444' : '#10b981';
            t.textContent = msg;
            t.style.opacity = '1';
            clearTimeout(t._timer);
            t._timer = setTimeout(() => t.style.opacity = '0', 2800);
        }

        // Lire le paramètre ?cat= dans l'URL et pré-sélectionner la catégorie
        const urlCat = new URLSearchParams(window.location.search).get('cat');
        if (urlCat) {
            const radios = document.querySelectorAll('input[name="cat-filter"]');
            radios.forEach(r => {
                if (r.value.toLowerCase() === urlCat.toLowerCase()) {
                    r.checked = true;
                }
            });
        }

        // Init
        applyFilters();

        /* ── Lazy loading images ── */
        function initLazy() {
            const imgs = document.querySelectorAll('img.lazy:not(.observed)');
            if (!imgs.length) return;
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (!e.isIntersecting) return;
                    const img = e.target;
                    img.src = img.dataset.src;
                    img.onload = () => img.classList.add('loaded');
                    img.classList.remove('lazy');
                    io.unobserve(img);
                });
            }, { rootMargin: '100px' });
            imgs.forEach(img => { img.classList.add('observed'); io.observe(img); });
        }

        /* ── Promo countdown ── */
        function updateCountdowns() {
            document.querySelectorAll('.promo-countdown[data-ends]').forEach(el => {
                const diff = new Date(el.dataset.ends) - Date.now();
                const lbl  = el.querySelector('.countdown-label');
                if (!lbl) return;
                if (diff <= 0) { lbl.textContent = 'Expiré'; return; }
                const h = Math.floor(diff / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                lbl.textContent = h > 48
                    ? `${Math.floor(h/24)}j restants`
                    : `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            });
        }

        const origRender = render;
        render = function() { origRender(); initLazy(); updateCountdowns(); };
        setInterval(updateCountdowns, 1000);
        initLazy();
    </script>
</body>
</html>
