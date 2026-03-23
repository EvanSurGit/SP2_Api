<x-app-layout>
    <div class="max-w-2xl mx-auto text-center mt-12">
        <h1 class="text-xl font-bold text-gray-800 mb-4">Téléchargement de votre facture...</h1>
        <p class="text-gray-600 mb-8">
            Votre facture est en cours de génération. Le téléchargement va débuter automatiquement.
        </p>
    </div>

    {{-- Téléchargement automatique + redirection --}}
    <script>
        const pdfData = "data:application/pdf;base64,{{ $pdfData }}";
        const link = document.createElement('a');
        link.href = pdfData;
        link.download = "{{ $fileName }}";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        setTimeout(() => {
            window.location.href = "{{ route('checkout.success') }}";
        }, 2000);
    </script>
</x-app-layout>
