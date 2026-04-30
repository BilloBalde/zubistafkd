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
        .navbar-fixed {
            position: fixed; top: 0; width: 100%; z-index: 50;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
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
                <a href="{{ route('products.index') }}" class="text-gray-700 font-medium hover:text-amber-600 transition">Catalogue</a>
                <a href="{{ route('public.categories') }}" class="text-gray-700 font-medium hover:text-amber-600 transition">Catégories</a>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    @if(Auth::user()->isCustomer())
                        <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-amber-600 transition text-sm">
                            <i class="fas fa-receipt text-xl"></i>
                        </a>
                        <form method="POST" action="{{ route('shop.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-amber-600 transition text-sm">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-amber-600 transition text-sm">
                            <i class="fas fa-tachometer-alt text-xl"></i>
                        </a>
                    @endif
                @else
                    <a href="{{ route('otp.login') }}" class="text-gray-700 hover:text-amber-600 transition text-sm">
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
                    <div class="flex items-baseline gap-3 mb-6">
                        <span class="price-main">{{ number_format($product->promo_price ?? $product->price, 0, ',', ' ') }} GNF</span>
                        @if($product->promo_price)
                            <span class="price-old">{{ number_format($product->price, 0, ',', ' ') }} GNF</span>
                            @php $disc = round((1 - $product->promo_price / $product->price) * 100); @endphp
                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded-full">-{{ $disc }}%</span>
                        @endif
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
    @if($related->count() > 0)
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-5">
            <i class="fas fa-layer-group text-amber-500 mr-2"></i>
            Produits similaires
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($related as $r)
            <a href="{{ route('productDetail', $r->id) }}" class="rel-card">
                @if($r->image)
                    <img src="{{ asset('products/' . $r->image) }}" alt="{{ $r->libelle }}"
                         class="rel-img"
                         onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="rel-placeholder" style="display:none;">🖨️</div>
                @else
                    <div class="rel-placeholder">🖨️</div>
                @endif
                <div class="rel-info">
                    <div class="rel-name">{{ $r->libelle }}</div>
                    <div class="rel-price">{{ number_format($r->promo_price ?? $r->price, 0, ',', ' ') }} GNF</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- FOOTER --}}
<footer class="bg-gray-900 text-gray-400 text-center py-6 text-sm mt-4">
    <p>&copy; {{ date('Y') }} FBK-Printing —
        <a href="{{ route('accueil') }}" class="hover:text-white transition">Retour à l'accueil</a>
    </p>
</footer>

{{-- TOAST --}}
<div id="toast">
    <i class="fas fa-check-circle text-green-400"></i>
    <span id="toast-msg">Produit ajouté au panier</span>
</div>

<script>
function addToCart(id, btn) {
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout...'; }
    fetch(`{{ url('/cart/add') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error();
        return response.json();
    })
    .then(data => {
        showToast(data.message || 'Produit ajouté au panier !');
        // Mettre à jour le compteur du panier avec animation
        console.log('Cart count:', data.count);
        if (data.count !== undefined) {
            document.querySelectorAll('#cart-count').forEach(el => {
                console.log('Updating cart badge to:', data.count);
                el.textContent = data.count;
                el.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    el.style.transform = 'scale(1)';
                }, 300);
            });
        }
    })
    .catch(err => {
        console.error('Erreur lors de l\'ajout au panier:', err);
        showToast('Impossible d\'ajouter ce produit.', 'error');
    })
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
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...'; }
    
    // Ajouter au panier et rediriger
    fetch(`{{ url('/cart/add') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response:', response);
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        console.log('Data received:', data);
        // Mettre à jour le compteur du panier
        if (data.count !== undefined) {
            document.querySelectorAll('#cart-count').forEach(el => {
                el.textContent = data.count;
                el.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    el.style.transform = 'scale(1)';
                }, 300);
            });
        }
        showToast('Produit ajouté ! Redirection vers la connexion...');
        // Rediriger vers la page de login avec l'ID du produit
        const loginUrl = '{{ route("otp.login") }}?product_id=' + id;
        console.log('Redirecting to:', loginUrl);
        setTimeout(() => {
            window.location.href = loginUrl;
        }, 500);
    })
    .catch((error) => {
        console.error('Erreur:', error);
        showToast('Impossible de passer la commande : ' + error.message, 'error');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt"></i> Commander';
        }
    });
}

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
