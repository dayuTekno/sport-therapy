@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}

<form method="POST" action="/logout">
  <!-- CSRF token jika menggunakan Laravel -->
  <input type="hidden" name="_token" value="{{ csrf_token() }}">

  <button type="submit" 
          class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition-colors duration-200">
    Logout
  </button>
</form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
