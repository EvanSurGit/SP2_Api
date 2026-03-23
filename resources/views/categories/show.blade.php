<x-app-layout>
    <div class="bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h2 class="mb-6 text-lg font-semibold text-gray-800">
                {{ $categorie->nom ?? $categorie->name }}
            </h2>

            @if($puzzles->isEmpty())
                <p class="text-sm text-gray-500">Aucun puzzle pour cette catégorie.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($puzzles as $puzzle)
                        @php
                            // Résolution d'image robuste :
                            // priorité : image_url > public_path(image_path) > storage/app/public(image_path) > fallback public/images/produit.png
                            $img = null;

                            if (!empty($puzzle->image_url)) {
                                $img = $puzzle->image_url;
                            } elseif (!empty($puzzle->image_path) && file_exists(public_path($puzzle->image_path))) {
                                $img = asset($puzzle->image_path);
                            } elseif (!empty($puzzle->image_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($puzzle->image_path)) {
                                $img = \Illuminate\Support\Facades\Storage::url($puzzle->image_path);
                            } else {
                                $img = asset('images/produit.png');
                            }

                            $isWide = $loop->iteration > 4 && $loop->iteration <= 6;
                            $price = $puzzle->prix ?? $puzzle->price ?? 0;
                            // Format euro string (ex: "20,00 €")
                            $priceEuro = number_format($price, 2, ',', ' ') . ' €';
                        @endphp

                        <a href="{{ route('puzzles.show', $puzzle) }}"
                           class="group block bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:border-gray-300 transition {{ $isWide ? 'sm:col-span-2' : '' }}">
                            @if($isWide)
                                {{-- Carte large (paysage) --}}
                                <div class="flex flex-col sm:flex-row">
                                    <div class="sm:w-1/2">
                                        @if($img)
                                            <img src="{{ $img }}"
                                                 alt="{{ $puzzle->nom ?? $puzzle->name }}"
                                                 loading="lazy"
                                                 onerror="this.onerror=null;this.src='{{ asset('images/produit.png') }}';"
                                                 class="w-full h-56 object-cover rounded-t-xl sm:rounded-tr-none sm:rounded-l-xl">
                                        @else
                                            <div class="w-full h-56 bg-gray-100 rounded-t-xl sm:rounded-tr-none sm:rounded-l-xl grid place-items-center text-gray-400 text-xs">No image</div>
                                        @endif
                                    </div>

                                    <div class="p-4 sm:w-1/2">
                                        <h3 class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $puzzle->nom ?? $puzzle->name ?? 'Produit' }}
                                        </h3>

                                        <div class="mt-2 text-xs text-gray-500 line-clamp-2">
                                            {{ \Illuminate\Support\Str::limit($puzzle->description ?? '—', 90) }}
                                        </div>

                                        <div class="mt-3 font-semibold text-gray-900">
                                            {{ $priceEuro }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Petite carte (portrait) --}}
                                <div>
                                    @if($img)
                                        <img src="{{ $img }}"
                                             alt="{{ $puzzle->nom ?? $puzzle->name }}"
                                             loading="lazy"
                                             onerror="this.onerror=null;this.src='{{ asset('images/produit.png') }}';"
                                             class="w-full h-48 object-cover rounded-t-xl">
                                    @else
                                        <div class="w-full h-48 bg-gray-100 rounded-t-xl grid place-items-center text-gray-400 text-xs">No image</div>
                                    @endif
                                </div>

                                <div class="p-3">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $puzzle->nom ?? $puzzle->name ?? 'Produit' }}
                                    </h3>

                                    <div class="mt-1 text-xs text-gray-500">—</div>

                                    <div class="mt-2 font-semibold text-gray-900">
                                        {{ $priceEuro }}
                                    </div>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
