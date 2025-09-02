@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <livewire:client.booking-details :bookingId="$id" />
</div>
@endsection

