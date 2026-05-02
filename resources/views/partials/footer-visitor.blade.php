<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <img src="{{ asset('assets/img/fbk.png') }}" alt="FBK-Printing" class="h-8 w-auto object-contain">
                    <span class="font-bold text-white">FBK-Printing</span>
                </div>
                <p class="text-sm text-gray-400">Spécialiste en matériaux d'imprimantes — papier, encre, rouleaux, presses — basé à Conakry, Guinée.</p>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Liens utiles</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('accueil') }}#apropos" class="text-gray-400 hover:text-white transition">À propos</a></li>
                    <li><a href="{{ route('accueil') }}#produits" class="text-gray-400 hover:text-white transition">Produits</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Blog</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition">Recrutement</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Aide</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('accueil') }}#contact" class="text-gray-400 hover:text-white transition">Contact</a></li>
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

        {{-- Carte boutique --}}
        <div class="border-t border-gray-800 pt-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-start gap-6">
                <div class="md:w-1/3">
                    <h4 class="font-bold text-white mb-2 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-amber-500"></i> Notre boutique
                    </h4>
                    <p class="text-sm text-gray-400 mb-1">Cimenterie, Conakry, Guinée</p>
                    <p class="text-sm text-gray-400 mb-4">Ouvert du lundi au samedi · 08h – 18h</p>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=Cimenterie+Conakry+Guin%C3%A9e"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        <i class="fas fa-directions"></i> Obtenir l'itinéraire
                    </a>
                </div>
                <div class="md:w-2/3 rounded-xl overflow-hidden shadow-lg border border-gray-700" style="height:240px;">
                    <iframe
                        src="https://maps.google.com/maps?q=Cimenterie+Conakry+Guin%C3%A9e&z=15&output=embed"
                        width="100%" height="100%"
                        style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Localisation FBK Printing - Cimenterie Conakry">
                    </iframe>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
            <p>&copy; {{ date('Y') }} FBK-Printing. Tous droits réservés. Conakry, Guinée.</p>
            <p class="mt-2">Conçu avec <i class="fas fa-heart text-red-500"></i> pour l'impression professionnelle</p>
        </div>
    </div>
</footer>
