<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian - SmartKlinik</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-sm w-full text-center">
        
        <div class="mb-4">
            <h2 class="text-xl font-bold text-gray-800">Klinik Sehat Selalu</h2>
            <p class="text-sm text-gray-500">Antrian Admisi Pendaftaran</p>
        </div>

        <div class="border-t-2 border-b-2 border-dashed border-gray-300 py-6 my-4">
            <p class="text-sm text-gray-500 mb-2">Nomor Antrian Anda</p>
            <h1 class="text-6xl font-black text-blue-600">{{ $queue->queue_code }}</h1>
            <p class="text-xs text-gray-400 mt-3">{{ $queue->created_at->format('d M Y, H:i') }}</p>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-600">
                Mohon tunggu hingga nomor Anda dipanggil oleh petugas pendaftaran.
            </p>
        </div>

        <div class="space-y-3">
            <button onclick="window.print()" class="w-full py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                🖨️ Cetak Tiket
            </button>
            <a href="{{ route('antrian') }}" class="block w-full py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                Kembali
            </a>
        </div>
        
    </div>
</body>
</html>
