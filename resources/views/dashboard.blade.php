@extends('layout')

@section('content')
    <h3 class="mb-3">Dashboard</h3>
    <div class="bg-gray-800 text-green-200 border border-gray-700 p-4 rounded" role="alert">
        Anda login sebagai <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role }})
    </div>
@endsection
