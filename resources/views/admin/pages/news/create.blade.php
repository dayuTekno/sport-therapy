@extends('admin.layouts.app')

@section('title', 'Create Artikel')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Master Data'],
    ['label' => 'News', 'url' => route('news.index')],
    ['label' => 'Create'],
]"/>

<div class="w-full bg-white p-6 rounded-lg shadow mt-6">
    <h2 class="text-xl font-semibold mb-6">Create Artikel</h2>

    <form action="{{ route('news.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-4">
        @csrf

        {{-- Judul --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Judul
            </label>
            <input
                type="text"
                name="title"
                value="{{ old('title') }}"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       @error('title') border-red-500 @enderror"
            >
            @error('title')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>


        {{-- Tags --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Tags
            </label>
            <input
                type="text"
                name="tags"
                value="{{ old('tags') }}"
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
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       @error('description') border-red-500 @enderror"
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Thumbnail --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Thumbnail
            </label>
            <input
                type="file"
                name="thumbnail"
                class="w-full border rounded-lg px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       @error('thumbnail') border-red-500 @enderror"
            >
            @error('thumbnail')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
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
                <option value="draft">Draft</option>
                <option value="publish">Publish</option>
            </select>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700
                   text-white py-2 rounded-lg transition">
            Save
        </button>
    </form>
</div>

@endsection
