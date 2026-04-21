@php
    $isCustomer = Auth::check() && Auth::user()->isCustomer();
    $catalogUrl = $isCustomer ? route('shop.home') : route('accueil');
@endphp
<nav class="bg-white border-b border-amber-100 shadow-sm">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-between items-center gap-2 min-h-[4rem] py-2">
            <a href="{{ $catalogUrl }}" class="flex items-center gap-2 text-gray-800 font-bold">
                <span class="w-9 h-9 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full flex items-center justify-center text-white text-sm shrink-0">F</span>
                <span class="hidden sm:inline">FBK — Boutique</span>
            </a>
            <div class="flex flex-wrap items-center justify-end gap-3 sm:gap-4 text-sm font-medium">
                <a href="{{ $catalogUrl }}" class="text-gray-700 hover:text-amber-600">Catalogue</a>
                <a href="{{ route('panier') }}" class="text-gray-700 hover:text-amber-600">Panier</a>
                @auth
                    @if($isCustomer)
                        <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-amber-600">Mes commandes</a>
                        <a href="{{ route('addresses.index') }}" class="text-gray-700 hover:text-amber-600 hidden sm:inline">Adresses</a>
                        <form method="POST" action="{{ route('shop.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-amber-600">Déconnexion</button>
                        </form>
                    @else
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-amber-600 hidden sm:inline">Espace gestion</a>
                    @endif
                @else
                    <a href="{{ route('otp.login') }}" class="text-amber-600 hover:text-amber-700 font-medium">Se connecter</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
