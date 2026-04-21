<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FBK-Printing - Révélez votre éclat naturel</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts - Poppins et Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome pour les icônes --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { font-family: 'Poppins', sans-serif; }

        /* ── Hero ── */
        .hero-section {
            background: linear-gradient(135deg, #f5a962 0%, #f18d5c 50%, #d4753c 100%);
            min-height: 600px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute; top: 0; right: 0;
            width: 500px; height: 500px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(100px,-50px);
        }
        .hero-section::after {
            content: '';
            position: absolute; bottom: 0; left: 0;
            width: 400px; height: 400px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            transform: translate(-100px,100px);
        }
        .hero-content { position: relative; z-index: 10; }

        /* ── Misc helpers ── */
        .gradient-text {
            background: linear-gradient(135deg, #f5a962, #d4753c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .navbar-transparent {
            position: fixed; top: 0; width: 100%; z-index: 50;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .feature-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #f5a962, #f18d5c);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 28px;
            margin: 0 auto 20px;
        }

        /* ══════════════════════════════════════
           SECTION PRODUITS — onglets + sidebar
        ══════════════════════════════════════ */
        .tabs {
            display: flex; gap: 4px;
            border-bottom: 2px solid #f3f4f6;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 12px 26px; font-size: 14px; font-weight: 600;
            border: none; background: none;
            color: #6b7280; cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
            border-radius: 6px 6px 0 0;
            display: flex; align-items: center; gap: 8px;
        }
        .tab-btn:hover { color: #d97706; background: #fef3c7; }
        .tab-btn.active { color: #d97706; border-bottom-color: #d97706; background: none; }

        .panel { display: none; }
        .panel.active { display: block; }

        /* Grille catégories */
        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 16px;
        }
        .cat-card {
            background: #fff; border: 1.5px solid #f3f4f6;
            border-radius: 16px; padding: 1.5rem 1rem;
            text-align: center; cursor: pointer;
            transition: all 0.2s;
        }
        .cat-card:hover {
            border-color: #f59e0b;
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(245,158,11,0.12);
        }
        .cat-icon {
            width: 56px; height: 56px; border-radius: 50%;
            margin: 0 auto 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
        }
        .cat-name { font-size: 13px; font-weight: 600; color: #1f2937; }
        .cat-count { font-size: 12px; color: #9ca3af; margin-top: 4px; }

        /* Layout liste produits + sidebar */
        .products-layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 768px) {
            .products-layout { grid-template-columns: 1fr; }
            .sidebar { position: static !important; }
        }

        /* Sidebar */
        .sidebar {
            background: #fff; border: 1.5px solid #f3f4f6;
            border-radius: 16px; padding: 1.5rem;
            position: sticky; top: 90px;
        }
        .sidebar-title {
            font-size: 11px; font-weight: 700; color: #9ca3af;
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 16px;
        }
        .filter-group { margin-bottom: 1.5rem; }
        .filter-label {
            font-size: 13px; font-weight: 600; color: #374151;
            margin-bottom: 10px; display: block;
        }
        .filter-option {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 0; cursor: pointer;
        }
        .filter-option input[type=checkbox],
        .filter-option input[type=radio] {
            width: 15px; height: 15px;
            accent-color: #d97706; cursor: pointer;
        }
        .filter-option span { font-size: 13px; color: #374151; }
        .filter-option .f-badge {
            margin-left: auto; font-size: 11px;
            background: #f3f4f6; color: #6b7280;
            padding: 1px 8px; border-radius: 20px;
        }
        .price-inputs { display: flex; gap: 8px; align-items: center; }
        .price-inputs input {
            width: 72px; padding: 6px 10px; font-size: 12px;
            border: 1.5px solid #e5e7eb; border-radius: 8px;
            background: #fff; color: #374151;
            font-family: 'Poppins', sans-serif;
            outline: none;
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
        .filter-divider { border: none; border-top: 1px solid #f3f4f6; margin: 0 0 1.25rem; }

        /* Grille produits */
        .results-header {
            display: flex; justify-content: space-between;
            align-items: center; margin-bottom: 16px;
            flex-wrap: wrap; gap: 8px;
        }
        .results-count { font-size: 13px; color: #6b7280; }
        .sort-sel {
            font-size: 12px; padding: 6px 12px;
            border: 1.5px solid #e5e7eb; border-radius: 8px;
            background: #fff; color: #374151; cursor: pointer;
            font-family: 'Poppins', sans-serif; outline: none;
        }
        .sort-sel:focus { border-color: #f59e0b; }

        .prod-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
        }
        .prod-card {
            background: #fff; border: 1.5px solid #f3f4f6;
            border-radius: 16px; overflow: hidden;
            transition: all 0.2s; cursor: pointer;
        }
        .prod-card:hover {
            border-color: #f59e0b;
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(245,158,11,0.12);
        }
        .prod-img {
            height: 130px; display: flex; align-items: center;
            justify-content: center; position: relative;
            background: #fafafa;
            overflow: hidden;
        }
        .prod-img img { width: 100%; height: 100%; object-fit: cover; }
        .prod-img .no-img { font-size: 40px; }
        .prod-badge {
            position: absolute; top: 10px; left: 10px;
            font-size: 10px; font-weight: 700;
            padding: 3px 9px; border-radius: 20px;
        }
        .badge-promo { background: #fef3c7; color: #92400e; }
        .badge-best { background: #d1fae5; color: #065f46; }

        .prod-info { padding: 12px 14px 14px; }
        .prod-name {
            font-size: 13px; font-weight: 600; color: #1f2937;
            margin-bottom: 3px; line-height: 1.35;
        }
        .prod-cat-tag { font-size: 11px; color: #9ca3af; margin-bottom: 6px; }
        .prod-stars { font-size: 12px; color: #f59e0b; margin-bottom: 6px; }
        .prod-price-row {
            display: flex; align-items: baseline; gap: 6px;
            margin-bottom: 10px;
        }
        .prod-price { font-size: 16px; font-weight: 700; color: #d97706; }
        .prod-price-old {
            font-size: 11px; color: #9ca3af;
            text-decoration: line-through;
        }
        .prod-btn {
            display: block; width: 100%; padding: 8px; font-size: 12px;
            font-weight: 600; text-align: center;
            background: linear-gradient(135deg, #f5a962, #d97706);
            color: #fff; border: none; border-radius: 8px; cursor: pointer;
            transition: opacity 0.2s;
            font-family: 'Poppins', sans-serif;
        }
        .prod-btn:hover { opacity: 0.88; }

        .empty-state {
            text-align: center; padding: 3rem 1rem;
            color: #9ca3af; font-size: 14px;
        }
        .empty-state i { font-size: 32px; margin-bottom: 12px; display: block; color: #d1d5db; }
    </style>
</head>
<body class="bg-white">

    {{-- ══════════════════════════════════════════
         NAVIGATION
    ══════════════════════════════════════════ --}}
    <nav class="navbar-transparent">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center text-white font-bold text-lg">F</div>
                    <span class="text-xl font-bold text-gray-800 hidden sm:inline">FBK-Printing</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#accueil" class="text-gray-700 font-medium hover:text-amber-600 transition">Accueil</a>
                    <a href="#produits" class="text-gray-700 font-medium hover:text-amber-600 transition">Produits</a>
                    <a href="#pourquoi" class="text-gray-700 font-medium hover:text-amber-600 transition">Pourquoi nous</a>
                    <a href="#apropos" class="text-gray-700 font-medium hover:text-amber-600 transition">À Propos</a>
                    <a href="#contact" class="text-gray-700 font-medium hover:text-amber-600 transition">Contact</a>                   
                </div>
               <div class="flex items-center space-x-4">
                    @auth
                        @if(Auth::user()->isCustomer())
                            <a href="{{ route('shop.home') }}" class="text-gray-700 hover:text-amber-600 transition font-medium text-sm">
                                <i class="fas fa-store text-xl"></i>
                                <span class="hidden sm:inline"> Ma boutique</span>
                            </a>
                            <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-amber-600 transition font-medium text-sm">
                                <i class="fas fa-receipt text-xl"></i>
                                <span class="hidden sm:inline"> Mes commandes</span>
                            </a>
                            <form method="POST" action="{{ route('shop.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-amber-600 transition font-medium text-sm">
                                    <i class="fas fa-sign-out-alt text-xl"></i>
                                    <span class="hidden sm:inline"> Déconnexion</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('home') }}" class="text-gray-700 hover:text-amber-600 transition font-medium">
                                <i class="fas fa-tachometer-alt text-xl"></i>
                                <span class="hidden sm:inline"> Espace gestion</span>
                            </a>
                            <a href="{{ url('/logout') }}" class="text-gray-700 hover:text-amber-600 transition font-medium text-sm">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                                <span class="hidden sm:inline"> Déconnexion</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('otp.login') }}" class="text-gray-700 hover:text-amber-600 transition font-medium">
                            <i class="fas fa-sign-in-alt text-xl"></i>
                            <span class="hidden sm:inline"> Se connecter</span>
                        </a>
                        <a href="{{ route('login') }}" class="text-gray-500 hover:text-amber-600 transition text-xs hidden lg:inline" title="Connexion équipe">Pro</a>
                    @endauth

                    {{-- Panier --}}
                    <a href="{{ route('panier') }}" class="relative p-2 text-gray-700 hover:text-amber-600 transition">
                        <i class="fas fa-shopping-bag text-xl"></i>
                        <span id="cart-count" class="absolute top-0 right-0 bg-amber-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            {{ count(session('cart', [])) }}
                        </span>
                    </a>

                    {{-- Menu mobile --}}
                    <button class="md:hidden p-2 text-gray-700" id="mobileMenuBtn">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    {{-- ══════════════════════════════════════════
        HERO (avec image produit beauté)
    ══════════════════════════════════════════ --}}
    <section id="accueil" class="hero-section pt-32 pb-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="hero-content grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                <div>
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                        Révélez votre éclat naturel
                    </h1>
                    <p class="text-lg text-white/90 mb-8 leading-relaxed">
                        Découvrez nos produits de beauté premium, formulés spécialement pour sublimer votre peau naturellement. Depuis Conakry, la Guinée pour le monde entier.
                    </p>
                    <a href="#produits" class="px-8 py-3 bg-white text-amber-600 rounded-full font-semibold transition duration-300 hover:shadow-lg hover:scale-105 inline-block">
                        <i class="fas fa-arrow-right mr-2"></i> Découvrir
                    </a>
                </div>
                <div class="flex justify-center">
                    <div class="relative w-72 h-96 rounded-3xl overflow-hidden shadow-2xl border-2 border-white/30">
                        {{-- Image depuis public/products/logohero.png --}}
                        <img src="{{ asset('products/logohero.png') }}" 
                            alt="Produits de beauté FBK" 
                            class="w-full h-full object-cover"
                            onerror="this.onerror=null; this.src='https://placehold.co/400x500?text=Image+beaut%C3%A9'">
                        {{-- Léger overlay pour améliorer la lisibilité si besoin --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         SECTION PRODUITS (onglets)
    ══════════════════════════════════════════ --}}
    <section id="produits" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            {{-- En-tête --}}
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Nos <span class="gradient-text">Produits</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Explorez notre sélection de soins beauté premium — catégories, coups de cœur et offres du moment.
                </p>
            </div>

            {{-- Onglets --}}
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('categories')">
                    <i class="fas fa-th-large"></i> Catégories
                </button>
                <button class="tab-btn" onclick="switchTab('best')">
                    <i class="fas fa-star"></i> Meilleurs produits
                </button>
                <button class="tab-btn" onclick="switchTab('promo')">
                    <i class="fas fa-tag"></i> Promotions
                </button>
            </div>

            {{-- ─── Panel : Catégories ─── --}}
            <div id="panel-categories" class="panel active">
                <div class="cat-grid">
                    @foreach($categories as $cat)
                    <div class="cat-card" onclick="switchTabWithFilter('best', '{{ $cat->name }}')">
                        <div class="cat-icon" style="background:{{ $cat->bg_color ?? '#fef3c7' }};color:{{ $cat->text_color ?? '#d97706' }};">
                            <i class="{{ $cat->icon ?? 'fas fa-box' }}"></i>
                        </div>
                        <div class="cat-name">{{ $cat->name }}</div>
                        <div class="cat-count">{{ $cat->products_count }} produits</div>
                    </div>
                    @endforeach

                    {{-- Fallback si pas de catégories en base : retirer ce bloc une fois la BDD peuplée --}}
                    @if($categories->isEmpty())
                        @php
                            $staticCats = [
                                ['name'=>'Soins visage','count'=>12,'icon'=>'fas fa-smile','bg'=>'#fef3c7','color'=>'#d97706'],
                                ['name'=>'Corps','count'=>8,'icon'=>'fas fa-leaf','bg'=>'#d1fae5','color'=>'#059669'],
                                ['name'=>'Cheveux','count'=>10,'icon'=>'fas fa-wind','bg'=>'#ede9fe','color'=>'#7c3aed'],
                                ['name'=>'Maquillage','count'=>15,'icon'=>'fas fa-heart','bg'=>'#fce7f3','color'=>'#db2777'],
                                ['name'=>'Parfums','count'=>6,'icon'=>'fas fa-spray-can','bg'=>'#e0f2fe','color'=>'#0284c7'],
                                ['name'=>'Naturel & Bio','count'=>9,'icon'=>'fas fa-seedling','bg'=>'#dcfce7','color'=>'#16a34a'],
                                ['name'=>'Huiles','count'=>7,'icon'=>'fas fa-tint','bg'=>'#fef9c3','color'=>'#b45309'],
                                ['name'=>'Coffrets','count'=>5,'icon'=>'fas fa-gift','bg'=>'#fee2e2','color'=>'#dc2626'],
                            ];
                        @endphp
                        @foreach($staticCats as $sc)
                        <div class="cat-card" onclick="switchTabWithFilter('best', '{{ $sc['name'] }}')">
                            <div class="cat-icon" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                <i class="{{ $sc['icon'] }}"></i>
                            </div>
                            <div class="cat-name">{{ $sc['name'] }}</div>
                            <div class="cat-count">{{ $sc['count'] }} produits</div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- ─── Panel : Meilleurs produits ─── --}}
            <div id="panel-best" class="panel">
                <div class="products-layout">

                    {{-- Sidebar --}}
                    <aside class="sidebar">
                        <p class="sidebar-title"><i class="fas fa-sliders-h mr-2"></i>Filtres</p>

                        <div class="filter-group">
                            <span class="filter-label">Catégories</span>
                            @foreach($categories as $cat)
                            <label class="filter-option">
                                <input type="checkbox" class="cat-filter-best" value="{{ $cat->name }}" onchange="filterProducts('best')">
                                <span>{{ $cat->name }}</span>
                                <span class="f-badge">{{ $cat->products_count }}</span>
                            </label>
                            @endforeach
                            @if($categories->isEmpty())
                                @foreach(['Soins visage','Corps','Cheveux','Maquillage','Parfums','Huiles','Naturel & Bio'] as $cn)
                                <label class="filter-option">
                                    <input type="checkbox" class="cat-filter-best" value="{{ $cn }}" onchange="filterProducts('best')">
                                    <span>{{ $cn }}</span>
                                </label>
                                @endforeach
                            @endif
                        </div>

                        <hr class="filter-divider">

                        <div class="filter-group">
                            <span class="filter-label">Prix (GNF)</span>
                            <div class="price-inputs">
                                <input type="number" id="min-best" placeholder="Min" min="0" oninput="filterProducts('best')">
                                <span class="price-sep">—</span>
                                <input type="number" id="max-best" placeholder="Max" min="0" oninput="filterProducts('best')">
                            </div>
                        </div>

                        <hr class="filter-divider">

                        <div class="filter-group">
                            <span class="filter-label">Note minimale</span>
                            <label class="filter-option"><input type="radio" name="rating-best" value="0" checked onchange="filterProducts('best')"><span>Toutes</span></label>
                            <label class="filter-option"><input type="radio" name="rating-best" value="4" onchange="filterProducts('best')"><span>4+ étoiles</span></label>
                            <label class="filter-option"><input type="radio" name="rating-best" value="4.5" onchange="filterProducts('best')"><span>4.5+ étoiles</span></label>
                        </div>

                        <button class="reset-btn" onclick="resetFilters('best')">
                            <i class="fas fa-undo mr-1"></i> Réinitialiser
                        </button>
                    </aside>

                    {{-- Grille --}}
                    <div>
                        <div class="results-header">
                            <span class="results-count" id="count-best">— produits</span>
                            <select class="sort-sel" id="sort-best" onchange="filterProducts('best')">
                                <option value="rating">Mieux notés</option>
                                <option value="price-asc">Prix croissant</option>
                                <option value="price-desc">Prix décroissant</option>
                            </select>
                        </div>
                        <div class="prod-grid" id="grid-best"></div>
                    </div>
                </div>
            </div>

            {{-- ─── Panel : Promotions ─── --}}
            <div id="panel-promo" class="panel">
                <div class="products-layout">

                    {{-- Sidebar --}}
                    <aside class="sidebar">
                        <p class="sidebar-title"><i class="fas fa-sliders-h mr-2"></i>Filtres</p>

                        <div class="filter-group">
                            <span class="filter-label">Catégories</span>
                            @foreach($categories as $cat)
                            <label class="filter-option">
                                <input type="checkbox" class="cat-filter-promo" value="{{ $cat->name }}" onchange="filterProducts('promo')">
                                <span>{{ $cat->name }}</span>
                                <span class="f-badge">{{ $cat->promo_count ?? '' }}</span>
                            </label>
                            @endforeach
                            @if($categories->isEmpty())
                                @foreach(['Soins visage','Corps','Cheveux','Maquillage'] as $cn)
                                <label class="filter-option">
                                    <input type="checkbox" class="cat-filter-promo" value="{{ $cn }}" onchange="filterProducts('promo')">
                                    <span>{{ $cn }}</span>
                                </label>
                                @endforeach
                            @endif
                        </div>

                        <hr class="filter-divider">

                        <div class="filter-group">
                            <span class="filter-label">Réduction minimale</span>
                            <label class="filter-option"><input type="radio" name="disc-promo" value="0" checked onchange="filterProducts('promo')"><span>Toutes</span></label>
                            <label class="filter-option"><input type="radio" name="disc-promo" value="20" onchange="filterProducts('promo')"><span>-20% et plus</span></label>
                            <label class="filter-option"><input type="radio" name="disc-promo" value="30" onchange="filterProducts('promo')"><span>-30% et plus</span></label>
                        </div>

                        <hr class="filter-divider">

                        <div class="filter-group">
                            <span class="filter-label">Prix promo</span>
                            <div class="price-inputs">
                                <input type="number" id="min-promo" placeholder="Min" min="0" oninput="filterProducts('promo')">
                                <span class="price-sep">—</span>
                                <input type="number" id="max-promo" placeholder="Max" min="0" oninput="filterProducts('promo')">
                            </div>
                        </div>

                        <button class="reset-btn" onclick="resetFilters('promo')">
                            <i class="fas fa-undo mr-1"></i> Réinitialiser
                        </button>
                    </aside>

                    {{-- Grille --}}
                    <div>
                        <div class="results-header">
                            <span class="results-count" id="count-promo">— produits</span>
                            <select class="sort-sel" id="sort-promo" onchange="filterProducts('promo')">
                                <option value="discount">Meilleures réductions</option>
                                <option value="price-asc">Prix croissant</option>
                                <option value="price-desc">Prix décroissant</option>
                            </select>
                        </div>
                        <div class="prod-grid" id="grid-promo"></div>
                    </div>
                </div>
            </div>

            {{-- Lien catalogue complet --}}
            <div class="text-center mt-14">
                <a href="{{ route('products.index') }}" class="px-10 py-4 bg-amber-600 text-white rounded-full font-semibold text-lg hover:bg-amber-700 transition duration-300 inline-block shadow-lg">
                    <i class="fas fa-th mr-2"></i> Voir tous les produits
                </a>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         POURQUOI NOUS
    ══════════════════════════════════════════ --}}
    <section id="pourquoi" class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Pourquoi nous <span class="gradient-text">choisir</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Trois raisons principales pour faire confiance à FBK-Printing
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="feature-icon"><i class="fas fa-truck"></i></div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Livraison Rapide</h3>
                    <p class="text-gray-600 leading-relaxed">Livraison express partout en Guinée et à l'international en 3-5 jours ouvrables. Suivi en temps réel de votre commande.</p>
                </div>
                <div class="text-center">
                    <div class="feature-icon"><i class="fas fa-gem"></i></div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Qualité Premium</h3>
                    <p class="text-gray-600 leading-relaxed">Tous nos produits sont certifiés, testés dermatologiquement et fabriqués avec des ingrédients naturels de qualité premium.</p>
                </div>
                <div class="text-center">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Service Client 24/7</h3>
                    <p class="text-gray-600 leading-relaxed">Équipe dédiée disponible 24h/24 pour répondre à vos questions et vous conseiller sur les meilleurs produits.</p>
                </div>
            </div>
        </div>
    </section>


    {{-- ══════════════════════════════════════════
         À PROPOS
    ══════════════════════════════════════════ --}}
    <section id="apropos" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="inline-block px-4 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-semibold mb-4">
                        Qui sommes-nous ?
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6 leading-tight">
                        FBK <span class="gradient-text">Printing</span> Industrie
                    </h2>
                    <p class="text-gray-600 text-lg leading-relaxed mb-6">
                        Nous sommes <strong>FBK Printing Industrie</strong>, spécialistes des produits de beauté et d'impression premium en Guinée. Nous fournissons les meilleures affiches et soins, fabriqués avec passion depuis notre atelier de <strong>Gare Voiture Linsan, Madina Marché</strong>.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-8">
                        Notre mission : sublimer la beauté naturelle de chaque client avec des produits certifiés, accessibles et fabriqués localement avec des ingrédients de qualité premium.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-3 bg-white rounded-xl px-5 py-3 shadow-sm border border-gray-100">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Localisation</p>
                                <p class="text-sm font-semibold text-gray-700">Madina Marché, Conakry</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-white rounded-xl px-5 py-3 shadow-sm border border-gray-100">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-award text-amber-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Qualité</p>
                                <p class="text-sm font-semibold text-gray-700">Produits certifiés</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="relative w-full max-w-sm">
                        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-8 text-white text-center shadow-2xl">
                            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-industry text-white text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">FBK Printing</h3>
                            <p class="text-white/80 text-sm mb-6">Gare Voiture Linsan, Madina Marché — Conakry, Guinée</p>
                            <div class="space-y-3 text-left">
                                <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-2">
                                    <i class="fas fa-phone text-white/80 text-sm"></i>
                                    <span class="text-sm">+224 626 311 915</span>
                                </div>
                                <div class="flex items-center gap-3 bg-white/10 rounded-xl px-4 py-2">
                                    <i class="fas fa-envelope text-white/80 text-sm"></i>
                                    <span class="text-sm">souleymanesuccess@gmail.com</span>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-4 -right-4 w-24 h-24 bg-amber-200 rounded-full opacity-40 -z-10"></div>
                        <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-amber-300 rounded-full opacity-30 -z-10"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         CONTACT
    ══════════════════════════════════════════ --}}
    <section id="contact" class="py-20 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="inline-block px-4 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-semibold mb-4">
                    Contactez-nous
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Nous sommes à <span class="gradient-text">votre écoute</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Une question, une commande ou un partenariat ? Contactez FBK Printing directement.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:border-amber-300 hover:shadow-lg transition duration-300">
                    <div class="feature-icon mx-auto mb-5"><i class="fas fa-map-marker-alt"></i></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Adresse</h3>
                    <p class="text-gray-600 leading-relaxed">Guinée Conakry<br>Madina Marché<br>Gare Voiture Linsan</p>
                </div>
                <div class="text-center bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:border-amber-300 hover:shadow-lg transition duration-300">
                    <div class="feature-icon mx-auto mb-5"><i class="fas fa-phone-alt"></i></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Appelez-nous</h3>
                    <p class="text-gray-600 leading-relaxed">
                        <a href="tel:+224626311915" class="hover:text-amber-600 transition font-medium">+224 626 311 915</a><br>
                        <a href="tel:+224626314400" class="hover:text-amber-600 transition font-medium">+224 626 31 44 00</a>
                    </p>
                </div>
                <div class="text-center bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:border-amber-300 hover:shadow-lg transition duration-300">
                    <div class="feature-icon mx-auto mb-5"><i class="fas fa-envelope"></i></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Email</h3>
                    <p class="text-gray-600 leading-relaxed">
                        <a href="mailto:souleymanesuccess@gmail.com" class="hover:text-amber-600 transition font-medium break-all">
                            souleymanesuccess@gmail.com
                        </a>
                    </p>
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="{{ route('contact') }}" class="px-10 py-4 bg-amber-600 text-white rounded-full font-semibold text-lg hover:bg-amber-700 transition duration-300 inline-block shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i> Envoyer un message
                </a>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         NEWSLETTER
    ══════════════════════════════════════════ --}}
    <section class="py-16 bg-gradient-to-r from-amber-500 to-amber-600">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center text-white">
                <h2 class="text-4xl font-bold mb-4">Recevez nos offres exclusives</h2>
                <p class="text-lg text-white/90 mb-8">Inscrivez-vous à notre newsletter et bénéficiez de 10% de réduction sur votre première commande</p>
                <form action="" method="POST" class="flex gap-2 max-w-md mx-auto">
                    @csrf
                    <input type="email" name="email" placeholder="Votre email" required
                        class="flex-1 px-6 py-3 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-white">
                    <button type="submit" class="px-8 py-3 bg-white text-amber-600 rounded-full font-semibold hover:shadow-lg transition">
                        S'inscrire
                    </button>
                </form>
                @if(session('newsletter_success'))
                    <p class="text-green-200 mt-2">{{ session('newsletter_success') }}</p>
                @endif
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════
         FOOTER
    ══════════════════════════════════════════ --}}
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center text-white font-bold">F</div>
                        <span class="font-bold text-white">FBK-Printing</span>
                    </div>
                    <p class="text-sm text-gray-400">Produits de beauté premium fabriqués en Guinée avec passion et expertise.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Liens utiles</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#apropos" class="text-gray-400 hover:text-white transition">À propos</a></li>
                        <li><a href="#produits" class="text-gray-400 hover:text-white transition">Produits</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Recrutement</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Aide</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Politique de retour</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Mentions légales</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Suivez-nous</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-white hover:bg-amber-600 transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-white hover:bg-amber-600 transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-white hover:bg-amber-600 transition"><i class="fab fa-pinterest"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-white hover:bg-amber-600 transition"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} FBK-Printing. Tous droits réservés. Conakry, Guinée.</p>
                <p class="mt-2">Fabriqué avec <i class="fas fa-heart text-red-500"></i> pour la beauté naturelle</p>
            </div>
        </div>
    </footer>

    {{-- ══════════════════════════════════════════
         SCRIPTS
    ══════════════════════════════════════════ --}}
    <script>
        {{-- Données injectées depuis le contrôleur --}}
        {{-- Chaque produit doit avoir : name, category_name, price, old_price (nullable),
             discount (nullable, ex: 25), rating, image (nullable), is_best, is_promo --}}
        const bestProducts  = @json($bestProducts ?? []);
        const promoProducts = @json($promoProducts ?? []);

        /* ── Rendu d'une carte produit ── */
        function renderCard(p) {
            const stars    = '★'.repeat(Math.floor(p.rating)) + (p.rating % 1 >= 0.5 ? '½' : '');
            const isPromo  = !!p.old_price;
            const badgeTxt = isPromo ? `-${p.discount}%` : 'Top vente';
            const badgeCls = isPromo ? 'badge-promo' : 'badge-best';
            const oldHTML  = p.old_price
                ? `<span class="prod-price-old">${Number(p.old_price).toLocaleString('fr-FR')} GNF</span>`
                : '';
            
            const imgHTML = p.image
                ? `<img src="${p.image}" alt="Image non trouvée: ${p.image}" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=Introuvable'">`
                : `<span class="no-img">🌿</span>`;
            return `<div class="prod-card">
                <div class="prod-img">
                    <span class="prod-badge ${badgeCls}">${badgeTxt}</span>
                    ${imgHTML}
                </div>
                <div class="prod-info">
                    <div class="prod-name">${p.name}</div>
                    <div class="prod-cat-tag">${p.category_name ?? ''}</div>
                    <div class="prod-stars">${stars} <span style="color:#9ca3af;font-size:11px;">${parseFloat(p.rating).toFixed(1)}</span></div>
                    <div class="prod-price-row">
                        <span class="prod-price">${Number(p.price).toLocaleString('fr-FR')} GNF</span>
                        ${oldHTML}
                    </div>
                    <button class="prod-btn" onclick="addToCart(${p.id})">
                        <i class="fas fa-shopping-bag mr-1"></i> Ajouter
                    </button>
                </div>
            </div>`;
        }

        /* ── Filtrage / tri ── */
        function filterProducts(tab) {
            const isPromo = tab === 'promo';
            let data      = isPromo ? [...promoProducts] : [...bestProducts];

            /* Catégories cochées */
            const checked = [...document.querySelectorAll(`.cat-filter-${tab}:checked`)].map(el => el.value);
            if (checked.length) data = data.filter(p => checked.includes(p.category_name));

            /* Fourchette de prix */
            const minP = parseFloat(document.getElementById(`min-${tab}`).value) || 0;
            const maxP = parseFloat(document.getElementById(`max-${tab}`).value) || Infinity;
            data = data.filter(p => p.price >= minP && p.price <= maxP);

            /* Filtre spécifique */
            if (isPromo) {
                const disc = parseFloat(document.querySelector('input[name="disc-promo"]:checked')?.value ?? 0);
                data = data.filter(p => (p.discount ?? 0) >= disc);
            } else {
                const minR = parseFloat(document.querySelector('input[name="rating-best"]:checked')?.value ?? 0);
                data = data.filter(p => p.rating >= minR);
            }

            /* Tri */
            const sort = document.getElementById(`sort-${tab}`).value;
            if (sort === 'price-asc')  data.sort((a,b) => a.price - b.price);
            if (sort === 'price-desc') data.sort((a,b) => b.price - a.price);
            if (sort === 'rating')     data.sort((a,b) => b.rating - a.rating);
            if (sort === 'discount')   data.sort((a,b) => (b.discount??0) - (a.discount??0));

            /* Affichage */
            const grid = document.getElementById(`grid-${tab}`);
            grid.innerHTML = data.length
                ? data.map(renderCard).join('')
                : `<div class="empty-state col-span-full">
                       <i class="fas fa-box-open"></i>
                       Aucun produit trouvé avec ces filtres.
                   </div>`;
            document.getElementById(`count-${tab}`).textContent =
                `${data.length} produit${data.length !== 1 ? 's' : ''}`;
        }

        function resetFilters(tab) {
            document.querySelectorAll(`.cat-filter-${tab}`).forEach(el => el.checked = false);
            document.getElementById(`min-${tab}`).value = '';
            document.getElementById(`max-${tab}`).value = '';
            const name = tab === 'promo' ? 'disc-promo' : 'rating-best';
            const first = document.querySelector(`input[name="${name}"]`);
            if (first) first.checked = true;
            document.getElementById(`sort-${tab}`).selectedIndex = 0;
            filterProducts(tab);
        }

        /* ── Navigation onglets ── */
        function switchTab(tab) {
            const order = ['categories','best','promo'];
            document.querySelectorAll('.tab-btn').forEach((btn, i) => {
                btn.classList.toggle('active', order[i] === tab);
            });
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            document.getElementById(`panel-${tab}`).classList.add('active');
            if (tab === 'best' || tab === 'promo') filterProducts(tab);
        }

        function switchTabWithFilter(tab, cat) {
            switchTab(tab);
            setTimeout(() => {
                document.querySelectorAll(`.cat-filter-${tab}`).forEach(el => {
                    el.checked = (el.value === cat);
                });
                filterProducts(tab);
            }, 10);
        }

        /* ── Panier (avec débogage) ── */
        function addToCart(id) {
            console.log("Tentative d'ajout du produit ID:", id);
            
            fetch(`{{ url('/cart/add') }}/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log("Réponse reçue, statut:", response.status);
                if (!response.ok) throw new Error('Erreur réseau ou produit introuvable');
                return response.json();
            })
            .then(data => {
                console.log("Données reçues:", data);
                const badge = document.getElementById('cart-count');
                if (badge && data.count !== undefined) {
                    badge.textContent = data.count;
                    badge.classList.add('animate__animated', 'animate__rubberBand');
                    setTimeout(() => badge.classList.remove('animate__animated', 'animate__rubberBand'), 1000);
                }
                alert("Produit ajouté au panier !");
            })
            .catch(error => {
                console.error("Erreur lors de l'ajout:", error);
                alert("Erreur : Impossible d'ajouter le produit au panier.");
            });
        }

        /* ── Init ── */
        filterProducts('best');
        filterProducts('promo');

        /* ── Smooth scroll ── */
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const t = document.querySelector(a.getAttribute('href'));
                if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        /* ── Menu mobile ── */
        document.getElementById('mobileMenuBtn').addEventListener('click', () => {
            alert('Menu mobile — à implémenter avec votre système de navigation');
        });
    </script>
</body>
</html>