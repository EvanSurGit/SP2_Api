<x-app-layout>
    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-forest.jpg') }}" alt="Forêt" class="w-full h-full object-cover filter brightness-75">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/10 to-black/30"></div>
        </div>

        <div class="relative max-w-6xl mx-auto px-6 py-20 sm:py-28 lg:py-32">
            <div class="max-w-3xl text-center mx-auto">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight drop-shadow-lg">WoodyCraft</h1>
                <p class="mt-4 text-lg sm:text-xl text-white/90">3D puzzles — l’union du bois, du design et de la passion. Explore nos collections.</p>

                {{-- CTA --}}
                <div class="mt-8 flex justify-center gap-3">
                    <a href="#categories" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-amber-500 text-white font-semibold shadow-lg hover:brightness-105 transition">
                        Parcourir les catégories
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </a>

                    <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white/10 text-white border border-white/10 hover:bg-white/5 transition">
                        Nouveautés
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- CONTENU --}}
    <main id="categories" class="bg-gray-50 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Recherche / Tri --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div class="flex-1">
                    <label for="search" class="sr-only">Rechercher</label>
                    <div class="relative">
                        <input id="search" type="search" placeholder="Rechercher une catégorie (ex: bois, nature, enfant...)" 
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-300"
                               aria-label="Recherche catégorie">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 21l-4.35-4.35"/><circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2"/></svg>
                        </div>
                    </div>
                </div>

                <div class="w-full sm:w-auto flex items-center gap-3">
                    <label for="sort" class="text-xs text-gray-500 hidden sm:inline">Trier :</label>
                    <select id="sort" class="rounded-xl border border-gray-200 bg-white py-2 px-3 text-sm shadow-sm focus:ring-amber-300">
                        <option value="default">Pertinence</option>
                        <option value="name_asc">Nom (A → Z)</option>
                        <option value="name_desc">Nom (Z → A)</option>
                    </select>
                </div>
            </div>

            {{-- Grid categories --}}
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="categoriesGrid" aria-live="polite">
                @forelse($categories as $categorie)
                    @php
                        // Résolution image : image_url > public path > storage/public > fallback
                        $img = null;
                        if (!empty($categorie->image_url)) {
                            $img = $categorie->image_url;
                        } elseif (!empty($categorie->image_path) && file_exists(public_path($categorie->image_path))) {
                            $img = asset($categorie->image_path);
                        } elseif (!empty($categorie->image_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($categorie->image_path)) {
                            $img = \Illuminate\Support\Facades\Storage::url($categorie->image_path);
                        } else {
                            $img = asset('images/produit.png');
                        }

                        $title = $categorie->nom ?? $categorie->name ?? 'Catégorie';
                        $desc = $categorie->description ?? '';
                        // data attributes to help client filtering
                        $dataTitle = e(strtolower($title));
                        $dataDesc  = e(strtolower($desc));
                    @endphp

                    <article class="group relative bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition transform hover:-translate-y-1"
                             tabindex="0"
                             role="article"
                             data-title="{{ $dataTitle }}"
                             data-desc="{{ $dataDesc }}">
                        <a href="{{ route('categories.show', $categorie) }}" class="absolute inset-0 z-10" aria-label="Ouvrir la catégorie {{ $title }}"></a>

                        {{-- Image + overlay --}}
                        <div class="relative h-44 sm:h-48 md:h-52 lg:h-40 overflow-hidden">
                            <img 
                                src="{{ $img }}" 
                                alt="{{ $title }}" 
                                loading="lazy"
                                decoding="async"
                                onerror="this.onerror=null;this.src='{{ asset('images/produit.png') }}';"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </div>

                        {{-- Content --}}
                        <div class="p-4 sm:p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ $title }}</h3>
                                    <p class="mt-1 text-xs text-gray-500 line-clamp-3">{{ \Illuminate\Support\Str::limit($desc ?: 'Collection artisanale en bois', 120) }}</p>
                                </div>

                                {{-- Badge (ex: nombre d'items) --}}
                                @if(method_exists($categorie, 'puzzles'))
                                    <div class="flex-shrink-0 ml-2 hidden sm:block">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-medium">
                                            {{ $categorie->puzzles()->count() }} puzzles
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v13h18V7"/><path d="M16 3H8v4h8V3z"/></svg>
                                        Bois
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('categories.show', $categorie) }}" class="text-amber-600 text-sm font-semibold hover:underline">Voir</a>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">Aucune catégorie pour le moment. Revenez plus tard ou créez-en une !</p>
                    </div>
                @endforelse
            </section>

            {{-- Pagination stub (si paginate) --}}
            <div class="mt-8 flex justify-center">
                @if(method_exists($categories, 'links'))
                    {{ $categories->links() }}
                @endif
            </div>
        </div>
    </main>

    {{-- Styles additionnels pour subtle effects --}}
    <style>
        /* small utility for clamping (if not present) */
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>

    {{-- JS: Filtre client et tri simple (progressive enhancement) --}}
    <script>
        (function () {
            const search = document.getElementById('search');
            const sort = document.getElementById('sort');
            const grid = document.getElementById('categoriesGrid');

            function normalize(s){ return (s||'').toString().trim().toLowerCase(); }

            function filterAndSort() {
                const q = normalize(search.value);
                const sortVal = sort.value;

                const items = Array.from(grid.querySelectorAll('[data-title]'));

                // filtering
                items.forEach(item => {
                    const title = normalize(item.dataset.title);
                    const desc  = normalize(item.dataset.desc);
                    const match = q === '' || title.includes(q) || desc.includes(q);
                    item.style.display = match ? '' : 'none';
                });

                // sorting (DOM reordering)
                if (sortVal === 'name_asc' || sortVal === 'name_desc') {
                    const visible = items.filter(i => i.style.display !== 'none');
                    visible.sort((a,b) => {
                        const ta = normalize(a.dataset.title);
                        const tb = normalize(b.dataset.title);
                        if (ta < tb) return sortVal === 'name_asc' ? -1 : 1;
                        if (ta > tb) return sortVal === 'name_asc' ? 1 : -1;
                        return 0;
                    });
                    visible.forEach(node => grid.appendChild(node));
                }
            }

            search.addEventListener('input', filterAndSort);
            sort.addEventListener('change', filterAndSort);

            // Accessibility: open article on Enter when focused
            document.querySelectorAll('[role="article"]').forEach(article => {
                article.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        const link = article.querySelector('a');
                        if (link) link.click();
                    }
                });
            });

            // initial run
            filterAndSort();
        })();
    </script>
</x-app-layout>
