@extends('admin.layouts.app')

@section('title', 'Edit Role')

@section('content')

@php

    $field =
        'w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-blue-400';
    $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
    $fileField =
        'w-full rounded-xl border border-dashed border-gray-300 bg-gray-50/80 px-4 py-3 text-sm text-gray-700 shadow-sm transition file:mr-3 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:border-blue-400 hover:bg-blue-50/50 dark:border-gray-600 dark:bg-gray-900/30 dark:text-gray-200 dark:file:bg-blue-500 dark:hover:border-blue-500';
    $label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200';
    $hint = 'mt-1.5 text-xs text-gray-500 dark:text-gray-400';
@endphp

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Master Data', 'url' => ''],
        ['label' => 'Tindakan', 'url' => route('procedures.index')],
        ['label' => 'Edit Tindakan'],
    ]"/>

    <div class="mt-6 w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Edit Tindakan</h2>

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('procedures.update', $es->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="pt-6">
                            <label class="{{ $label }}">Kode Tindakan</label>
                            <input
                                type="text"
                                name="procedure_code"
                                value="{{ old('procedure_code', $es['procedure_code'] ?? '') }}"
                                class="{{ $field }} @error('procedure_code'){{ $fieldErr }}@enderror"
                                placeholder="Kode Tindakan"
                            >
                            @error('procedure_code')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>


            <div class="pt-6">
                            <label class="{{ $label }}">Nama Tindakan</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $es->name ?? '') }}"
                                class="{{ $field }} @error('name'){{ $fieldErr }}@enderror"
                                placeholder="Nama kategori"
                            >
                            @error('name')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6">
                            <label class="{{ $label }}">Harga Tindakan (Rp)</label>
                            <input
                                type="number"
                                name="price"
                                value="{{ old('price', $es->price ?? 0) }}"
                                class="{{ $field }} @error('price'){{ $fieldErr }}@enderror"
                                placeholder="0"
                            >
                            @error('price')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-6">
                            <label class="{{ $label }}">Deskripsi</label>
                            <textarea
                                type="text"
                                name="desc"
                                class="{{ $field }} @error('desc'){{ $fieldErr }}@enderror"
                                placeholder="Deskripsi"
                            >{{ old('desc', $es->desc ?? '') }} @error('desc')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror</textarea>

        {{-- Buttons --}}
        <div class="flex pt-6 items-center justify-between">
            <a href="{{ route('procedures.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Simpan
            </button>
        </div>

@endsection
