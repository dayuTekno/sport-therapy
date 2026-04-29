@extends('admin.layouts.app')

@section('title', 'Kelola ICD Poly')

@section('content')

    @php
        $label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200';
        $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
    @endphp

    <x-breadcrumb :items="[
        ['label' => 'Poli', 'url' => ''],
        ['label' => 'ICD Poly', 'url' => route('poly-icds.index')],
        ['label' => 'Kelola ICD'],
    ]" />

    <div class="mt-6 w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Kelola ICD - Poliklinik {{ $polyclinic->name }}</h2>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('poly-icds.update', $polyclinic->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="pt-6">
                <label class="{{ $label }}">Pilih ICD</label>
                <select name="icds[]" class="select2 w-full @error('icds'){{ $fieldErr }}@enderror" multiple="multiple">
                    @php
                        $selectedIcds = $polyclinic->icds->pluck('id')->toArray();
                    @endphp
                    @foreach($icds as $icd)
                        <option value="{{ $icd->id }}" {{ in_array($icd->id, $selectedIcds) ? 'selected' : '' }}>
                            {{ $icd->icd_code }} - {{ $icd->name }}
                        </option>
                    @endforeach
                </select>

                @error('icds')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex pt-6 items-center justify-between">
                <a href="{{ route('poly-icds.index') }}"
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
                placeholder: "Pilih ICD",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

@endsection
