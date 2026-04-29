@extends('admin.layouts.app')

@section('title', 'Kelola Dokter Poly')

@section('content')

    @php
        $label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200';
        $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
    @endphp

    <x-breadcrumb :items="[
        ['label' => 'Poli', 'url' => ''],
        ['label' => 'Dokter Poly', 'url' => route('poly-doctors.index')],
        ['label' => 'Kelola Dokter'],
    ]" />

    <div class="mt-6 w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Kelola Dokter - Poliklinik {{ $polyclinic->name }}</h2>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('poly-doctors.update', $polyclinic->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="pt-6">
                <label class="{{ $label }}">Pilih Dokter</label>
                <select name="doctors[]" class="select2 w-full @error('doctors'){{ $fieldErr }}@enderror" multiple="multiple">
                    @php
                        $selectedDoctors = $polyclinic->doctors->pluck('id')->toArray();
                    @endphp
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ in_array($doc->id, $selectedDoctors) ? 'selected' : '' }}>
                            {{ $doc->doctor_code }} - {{ $doc->full_name }}
                        </option>
                    @endforeach
                </select>

                @error('doctors')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex pt-6 items-center justify-between">
                <a href="{{ route('poly-doctors.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function() {
            $('.select2').select2({
                placeholder: "Pilih Dokter",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

@endsection
