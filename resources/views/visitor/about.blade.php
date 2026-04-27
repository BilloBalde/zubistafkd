<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos — FBK-Printing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 bg-white/95 backdrop-blur shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('accueil') }}" class="flex items-center gap-2">
                <span class="text-2xl font-black text-amber-500">FBK</span>
                <span class="text-sm font-semibold text-gray-700">Printing</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('accueil') }}" class="text-sm text-gray-600 hover:text-amber-600">Accueil</a>
                <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:text-amber-600">Catalogue</a>
                <a href="{{ route('contact') }}" class="text-sm text-gray-600 hover:text-amber-600">Contact</a>
                <a href="{{ route('otp.login') }}" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Boutique
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-amber-50 to-orange-100">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-black text-gray-900 mb-4">À propos de <span class="text-amber-500">FBK Printing</span></h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                Votre partenaire de confiance pour tous vos besoins d'impression en Guinée.
            </p>
        </div>
    </section>

    <!-- Contenu -->
    <section class="py-16">
        <div class="max-w-5xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Notre mission</h2>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        FBK Printing est une entreprise guinéenne spécialisée dans l'impression de haute qualité.
                        Nous offrons une large gamme de services d'impression pour les particuliers et les entreprises,
                        avec des délais rapides et des prix compétitifs.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Notre équipe expérimentée est dédiée à fournir des produits qui reflètent votre image
                        et répondent à vos attentes les plus élevées.
                    </p>
                </div>
                <div class="bg-amber-50 rounded-2xl p-8 text-center">
                    <div class="text-6xl text-amber-400 mb-4"><i class="fas fa-print"></i></div>
                    <div class="text-3xl font-black text-gray-900">FBK Printing</div>
                    <div class="text-amber-600 font-semibold mt-1">Qualité & Rapidité</div>
                </div>
            </div>

            <!-- Valeurs -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                @foreach([
                    ['icon' => 'fa-star', 'title' => 'Qualité', 'text' => 'Des impressions nettes et durables sur tous types de supports.'],
                    ['icon' => 'fa-bolt', 'title' => 'Rapidité', 'text' => 'Délais de livraison optimisés pour respecter vos deadlines.'],
                    ['icon' => 'fa-handshake', 'title' => 'Fiabilité', 'text' => 'Un service client à votre écoute du lundi au samedi.'],
                ] as $val)
                <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                    <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas {{ $val['icon'] }} text-amber-500 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">{{ $val['title'] }}</h3>
                    <p class="text-gray-500 text-sm">{{ $val['text'] }}</p>
                </div>
                @endforeach
            </div>

            <!-- CTA -->
            <div class="text-center">
                <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold px-8 py-3 rounded-xl transition">
                    <i class="fas fa-envelope"></i> Nous contacter
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-8 py-3 rounded-xl transition ml-3">
                    <i class="fas fa-th-large"></i> Voir le catalogue
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-8 text-center text-sm">
        <p>© {{ date('Y') }} FBK Printing — Tous droits réservés</p>
    </footer>

</body>
</html>
