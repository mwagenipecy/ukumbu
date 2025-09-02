@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <livewire:client.user-dashboard />
</div>
@endsection

