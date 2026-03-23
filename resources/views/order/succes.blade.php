<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-12 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            {{ session('message') ?? 'Merci pour votre commande 🎉' }}
        </h1>

        <p class="text-gray-600 mb-8">
            Votre commande a bien été enregistrée. Vous recevrez un email de confirmation sous peu.
        </p>

        <a href="{{ route('home') }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Retour à l’accueil
        </a>
    </div>

    {{-- POPUP AVIS --}}
    @if(session('showReviewPopup'))
        <div x-data="{ open: true }"
             x-show="open"
             class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
             x-cloak>
            <div class="bg-white rounded-2xl shadow-xl w-96 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Donnez votre avis 🗣️</h2>
                <p class="text-sm text-gray-500 mb-4">
                    Que pensez-vous des puzzles que vous venez d’acheter ?
                </p>

                {{-- Exemple de formulaire d’avis --}}
                <form action="{{ route('reviews.store', ['puzzle' => 1]) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Note :</label>
                        <select name="rating" class="border-gray-300 rounded-md w-full">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} ⭐</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire :</label>
                        <textarea name="comment" rows="3"
                                  class="w-full border-gray-300 rounded-md"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="open = false"
                                class="px-4 py-2 text-sm text-gray-600 hover:underline">
                            Plus tard
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Envoyer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-app-layout>
