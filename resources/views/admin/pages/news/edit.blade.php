@extends('admin.layouts.app')

@section('title', 'Edit Artikel')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Master Data'],
    ['label' => 'News', 'url' => route('news.index')],
    ['label' => 'Edit'],
]"/>

<div class="w-full bg-white p-6 rounded-lg shadow mt-6">
    <h2 class="text-xl font-semibold mb-6">Edit Artikel</h2>

    <form action="{{ route('news.update', 1) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Judul
            </label>
            <input
                type="text"
                name="title"
                value="Peluncuran Aplikasi Perizinan Digital"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       @error('title') border-red-500 @enderror"
            >
        </div>

        {{-- Tags --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Tags
            </label>
            <input
                type="text"
                name="tags"
                value="perizinan,aplikasi,digital"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Isi Artikel
            </label>
            <textarea
                name="description"
                rows="5"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500"
            >Pemerintah resmi meluncurkan aplikasi perizinan digital untuk mempercepat layanan kepada masyarakat.</textarea>
        </div>

        {{-- Thumbnail --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Thumbnail
            </label>

                <img src="{{ asset('storage/news/tes.jpg') }}"
                     class="w-[50px] h-[50px] object-cover rounded mb-2">
            <input
                type="file"
                name="thumbnail"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Status
            </label>
            <select
                name="publish_st"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="draft">
                    Draft
                </option>
                <option value="publish" selected>
                    Publish
                </option>
            </select>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700
                   text-white py-2 rounded-lg transition">
            Update
        </button>
    </form>
</div>

@endsection
