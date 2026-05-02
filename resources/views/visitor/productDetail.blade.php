<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->libelle }} — FBK-Printing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .navbar-transparent {
            position: fixed; top: 0; width: 100%; z-index: 50;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .prod-img-main {
            width: 100%; height: 380px;
            object-fit: cover; border-radius: 1.25rem;
            background: #f9fafb;
        }
        .prod-img-placeholder {
            width: 100%; height: 380px;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 1.25rem; font-size: 80px;
        }
        .badge-cat {
            background: #fef3c7; color: #92400e;
            font-size: 12px; font-weight: 600;
            padding: 4px 14px; border-radius: 999px;
            display: inline-block;
        }
        .star { color: #f59e0b; }
        .star-empty { color: #d1d5db; }
        .price-main { font-size: 2rem; font-weight: 800; color: #d97706; }
        .price-old { font-size: 1rem; color: #9ca3af; text-decoration: line-through; }
        .btn-cart {
            width: 100%;
            background: linear-gradient(135deg, #f5a962, #d97706);
            color: #fff; font-weight: 700; font-size: 15px;
            padding: 14px; border: none; border-radius: 12px;
            cursor: pointer; transition: opacity .2s, transform .1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-cart:hover { opacity: .88; transform: scale(1.01); }
        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; color: #6b7280; font-weight: 500;
            padding: 6px 14px; border: 1.5px solid #e5e7eb;
            border-radius: 999px; transition: all .2s;
            text-decoration: none;
        }
        .btn-back:hover { color: #d97706; border-color: #f59e0b; }
        #cart-count {
            transition: transform 0.3s ease;
        }

        /* Cartes produits similaires */
        .rel-card {
            background: #fff; border: 1.5px solid #f3f4f6;
            border-radius: 16px; overflow: hidden;
            transition: all .25s; cursor: pointer;
            text-decoration: none;
        }
        .rel-card:hover { border-color: #f59e0b; transform: translateY(-3px); box-shadow: 0 8px 24px rgba(245,158,11,.15); }
        .rel-img { width: 100%; height: 130px; object-fit: cover; background: #fafafa; }
        .rel-placeholder {
            width: 100%; height: 130px;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; background: #fef3c7;
        }
        .rel-info { padding: 10px 12px 14px; }
        .rel-name { font-size: 12px; font-weight: 600; color: #1f2937; line-height: 1.4;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .rel-price { font-size: 14px; font-weight: 700; color: #d97706; margin-top: 4px; }

        /* Toast */
        #toast {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            background: #1f2937; color: #fff;
            padding: 12px 20px; border-radius: 12px;
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            transform: translateY(80px); opacity: 0;
            transition: all .35s ease;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }
        #toast.show { transform: translateY(0); opacity: 1; }
    </style>
</head>
<body class="bg-gray-50">

{{-- NAVBAR (identique au homepage) --}}
<nav class="navbar-transparent">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('assets/img/fbk.png') }}" alt="FBK-Printing" class="h-10 w-auto object-contain">
                <span class="text-xl font-bold text-gray-800 hidden sm:inline">FBK-Printing</span>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('accueil') }}" class="text-gray-700 font-medium hover:text-amber-600 transition">Accueil</a>
                <a href="{{ route('accueil') }}#produits" class="text-gray-700 font-medium hover:text-amber-600 transition">Produits</a>
                <a href="{{ route('accueil') }}#pourquoi" class="text-gray-700 font-medium hover:text-amber-600 transition">Pourquoi nous</a>
                <a href="{{ route('accueil') }}#apropos" class="text-gray-700 font-medium hover:text-amber-600 transition">À Propos</a>
                <a href="{{ route('accueil') }}#contact" class="text-gray-700 font-medium hover:text-amber-600 transition">Contact</a>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    @if(Auth::user()->isCustomer())
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

        {{-- Menu mobile déroulant --}}
        <div id="mobileMenu" class="md:hidden hidden pb-4 border-t border-gray-100 mt-2">
            <div class="flex flex-col space-y-1 pt-3">
                <a href="{{ route('accueil') }}" class="px-4 py-2 text-gray-700 font-medium hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">Accueil</a>
                <a href="{{ route('accueil') }}#produits" class="px-4 py-2 text-gray-700 font-medium hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">Produits</a>
                <a href="{{ route('accueil') }}#pourquoi" class="px-4 py-2 text-gray-700 font-medium hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">Pourquoi nous</a>
                <a href="{{ route('accueil') }}#apropos" class="px-4 py-2 text-gray-700 font-medium hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">À Propos</a>
                <a href="{{ route('accueil') }}#contact" class="px-4 py-2 text-gray-700 font-medium hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">Contact</a>
            </div>
        </div>
    </div>
</nav>

<div class="pt-24 pb-16 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
        <a href="{{ route('accueil') }}" class="hover:text-amber-600 transition">Accueil</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('products.index') }}" class="hover:text-amber-600 transition">Catalogue</a>
        <i class="fas fa-chevron-right text-xs"></i>
        @if($product->categories->first())
        <span class="text-gray-400">{{ $product->categories->first()->category_type }}</span>
        <i class="fas fa-chevron-right text-xs"></i>
        @endif
        <span class="text-gray-700 font-medium truncate max-w-xs">{{ $product->libelle }}</span>
    </nav>

    {{-- FICHE PRODUIT --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

            {{-- IMAGE --}}
            <div class="p-6 flex items-center justify-center bg-gray-50">
                @if($product->image)
                    <img src="{{ asset('products/' . $product->image) }}"
                         alt="{{ $product->libelle }}"
                         class="prod-img-main"
                         onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="prod-img-placeholder" style="display:none;">🖨️</div>
                @else
                    <div class="prod-img-placeholder">🖨️</div>
                @endif
            </div>

            {{-- INFOS --}}
            <div class="p-8 flex flex-col justify-between">
                <div>
                    {{-- Catégorie + SKU --}}
                    <div class="flex items-center gap-3 mb-4">
                        @if($product->categories->first())
                            <span class="badge-cat">{{ $product->categories->first()->category_type }}</span>
                        @endif
                        <span class="text-xs text-gray-400 font-mono">SKU : {{ $product->sku }}</span>
                    </div>

                    {{-- Nom --}}
                    <h1 class="text-2xl font-bold text-gray-900 mb-3 leading-tight">{{ $product->libelle }}</h1>

                    {{-- Étoiles --}}
                    <div class="flex items-center gap-2 mb-4">
                        <span>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->rating ?? 4.5))
                                    <i class="fas fa-star star text-sm"></i>
                                @elseif($i - 0.5 <= ($product->rating ?? 4.5))
                                    <i class="fas fa-star-half-alt star text-sm"></i>
                                @else
                                    <i class="far fa-star star-empty text-sm"></i>
                                @endif
                            @endfor
                        </span>
                        <span class="text-sm text-gray-500">{{ number_format($product->rating ?? 4.5, 1) }} / 5</span>
                    </div>

                    {{-- Description --}}
                    @if($product->description)
                    <p class="text-gray-600 text-sm leading-relaxed mb-5">{{ $product->description }}</p>
                    @endif

                    {{-- Infos carton --}}
                    @if($product->pcs)
                    <div class="flex items-center gap-2 mb-2 text-sm text-gray-500">
                        <i class="fas fa-box text-amber-400"></i>
                        <span>Pièces / carton : <strong class="text-gray-700">{{ $product->pcs }} pcs</strong></span>
                    </div>
                    @endif
                    @if($product->price_carton)
                    <div class="flex items-center gap-2 mb-5 text-sm text-gray-500">
                        <i class="fas fa-tags text-amber-400"></i>
                        <span>Prix carton : <strong class="text-gray-700">{{ number_format($product->price_carton, 0, ',', ' ') }} GNF</strong></span>
                    </div>
                    @endif
                </div>

                {{-- Prix + Bouton --}}
                <div>
                    <div class="flex items-baseline gap-3 mb-5">
                        <span class="price-main">{{ number_format($product->promo_price ?? $product->price, 0, ',', ' ') }} GNF</span>
                        @if($product->promo_price)
                            <span class="price-old">{{ number_format($product->price, 0, ',', ' ') }} GNF</span>
                            @php $disc = round((1 - $product->promo_price / $product->price) * 100); @endphp
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded-full">-{{ $disc }}%</span>
                        @endif
                    </div>

                    {{-- Tarifs de gros --}}
                    @php
                        $basePrice = ($product->promo_price && (!$product->promo_ends_at || $product->promo_ends_at->isFuture()))
                            ? (float) $product->promo_price
                            : (float) $product->price;
                        $bulkTiers = [
                            ['qty' => '5+',  'discount' => 3],
                            ['qty' => '10+', 'discount' => 5],
                            ['qty' => '15+', 'discount' => 7],
                            ['qty' => '20+', 'discount' => 10],
                        ];
                    @endphp
                    <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-3">
                        <p class="text-xs font-semibold text-amber-800 mb-2.5 flex items-center gap-1.5">
                            <i class="fas fa-tags text-amber-500"></i>
                            Tarifs de gros — prix unitaire selon la quantité
                        </p>
                        <div class="grid grid-cols-4 gap-1.5">
                            @foreach($bulkTiers as $tier)
                            @php $tierPrice = (int) round($basePrice * (1 - $tier['discount'] / 100)); @endphp
                            <div class="flex flex-col items-center bg-white rounded-lg border border-amber-100 py-2 px-1 shadow-sm">
                                <span class="text-[11px] font-bold text-amber-700">{{ $tier['qty'] }} pcs</span>
                                <span class="text-[12px] font-bold text-gray-800 mt-0.5 leading-tight">
                                    {{ number_format($tierPrice, 0, ',', ' ') }}
                                </span>
                                <span class="text-[10px] text-white font-semibold bg-green-500 rounded-full px-1.5 mt-1">
                                    −{{ $tier['discount'] }}%
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-amber-600 mt-2 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i>
                            La réduction est appliquée automatiquement dans votre panier
                        </p>
                    </div>

                    {{-- Sélecteur de quantité --}}
                    <div class="flex items-center gap-4 mb-4">
                        <span class="text-sm font-medium text-gray-600">Quantité :</span>
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <button type="button" onclick="changeQty(-1)"
                                class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 active:bg-gray-200 transition font-bold text-lg select-none">
                                −
                            </button>
                            <input type="number" id="qty-input" value="1" min="1" max="9999"
                                class="w-14 h-10 text-center text-base font-semibold text-gray-800 border-x border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-400 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none">
                            <button type="button" onclick="changeQty(1)"
                                class="w-10 h-10 flex items-center justify-center text-amber-600 hover:bg-amber-50 active:bg-amber-100 transition font-bold text-lg select-none">
                                +
                            </button>
                        </div>
                        <span id="qty-discount-badge" class="hidden text-xs font-bold text-white bg-green-500 px-2.5 py-1 rounded-full transition-all"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button class="btn-cart" onclick="addToCart({{ $product->id }}, this)">
                            <i class="fas fa-shopping-bag"></i>
                            Ajouter
                        </button>
                        <button class="btn-cart bg-gradient-to-r from-green-500 to-green-600" style="background: linear-gradient(135deg, #10b981, #059669);" onclick="buyNow({{ $product->id }}, this)">
                            <i class="fas fa-bolt"></i>
                            Commander
                        </button>
                    </div>

                    <a href="{{ url()->previous() === url()->current() ? route('accueil') : url()->previous() }}"
                       class="btn-back mt-3 w-full justify-center">
                        <i class="fas fa-arrow-left text-xs"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUITS SIMILAIRES --}}
    @if(isset($suggested) && $suggested->count() > 0)
    <div class="mt-14">
        <h2 class="text-xl font-bold text-gray-800 mb-5">
            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
            Vous pourriez aussi aimer
        </h2>
    
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($suggested as $s)
            <a href="{{ route('productDetail', $s->id) }}" class="rel-card">
    
                @if($s->image)
                    <img src="{{ asset('products/' . $s->image) }}"
                         class="rel-img"
                         onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="rel-placeholder" style="display:none;">🖨️</div>
                @else
                    <div class="rel-placeholder">🖨️</div>
                @endif
    
                <div class="rel-info">
                    <div class="rel-name">{{ $s->libelle }}</div>
    
                    <div class="rel-price">
                        {{ number_format($s->promo_price ?? $s->price, 0, ',', ' ') }} GNF
                    </div>
                </div>
    
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

@include('partials.footer-visitor')

{{-- TOAST --}}
<div id="toast">
    <i class="fas fa-check-circle text-green-400"></i>
    <span id="toast-msg">Produit ajouté au panier</span>
</div>

<script>
const discountTiers = [
    { min: 20, pct: 10 },
    { min: 15, pct: 7  },
    { min: 10, pct: 5  },
    { min: 5,  pct: 3  },
];

function getQty() {
    return Math.max(1, parseInt(document.getElementById('qty-input').value) || 1);
}

function changeQty(delta) {
    const input = document.getElementById('qty-input');
    input.value = Math.max(1, (parseInt(input.value) || 1) + delta);
    updateDiscountBadge();
}

function updateDiscountBadge() {
    const qty   = getQty();
    const badge = document.getElementById('qty-discount-badge');
    const tier  = discountTiers.find(t => qty >= t.min);
    if (tier) {
        badge.textContent = '−' + tier.pct + '%';
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

document.getElementById('qty-input').addEventListener('input', updateDiscountBadge);

function updateCartBadge(count) {
    document.querySelectorAll('#cart-count').forEach(el => {
        el.textContent = count;
        el.style.transform = 'scale(1.3)';
        setTimeout(() => { el.style.transform = 'scale(1)'; }, 300);
    });
}

function addToCart(id, btn) {
    const qty = getQty();
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout...'; }
    fetch(`{{ url('/cart/add') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ quantity: qty })
    })
    .then(response => {
        if (!response.ok) throw new Error();
        return response.json();
    })
    .then(data => {
        showToast(data.message || 'Produit ajouté au panier !');
        if (data.count !== undefined) updateCartBadge(data.count);
    })
    .catch(() => showToast('Impossible d\'ajouter ce produit.', 'error'))
    .finally(() => {
        if (btn) {
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-shopping-bag"></i> Ajouter';
            }, 1200);
        }
    });
}

function buyNow(id, btn) {
    const qty        = getQty();
    const isCustomer = {{ auth()->check() && auth()->user()->isCustomer() ? 'true' : 'false' }};

    if (isCustomer) {
        window.location.href = '{{ url("/shop/buy-now") }}/' + id + '?qty=' + qty;
        return;
    }

    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...'; }

    fetch(`{{ url('/cart/add') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ quantity: qty })
    })
    .then(response => {
        if (!response.ok) throw new Error('Erreur réseau');
        return response.json();
    })
    .then(data => {
        if (data.count !== undefined) updateCartBadge(data.count);
        showToast('Produit ajouté ! Redirection vers la connexion...');
        setTimeout(() => {
            window.location.href = '{{ route("otp.login") }}?product_id=' + id + '&qty=' + qty;
        }, 500);
    })
    .catch(error => {
        showToast('Impossible de passer la commande : ' + error.message, 'error');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt"></i> Commander';
        }
    });
}

document.getElementById('mobileMenuBtn').addEventListener('click', () => {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
});

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    const msgEl = document.getElementById('toast-msg');
    msgEl.textContent = msg;
    t.classList.add('show');
    
    // Changer la couleur selon le type
    if (type === 'error') {
        t.style.background = '#ef4444';
    } else {
        t.style.background = '#1f2937';
    }
    
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
</body>
</html>
