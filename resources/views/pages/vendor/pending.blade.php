@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-semibold mb-4">Account Pending Approval</h1>
        <p class="mb-4">Your vendor account is currently pending approval by the administrator. You will receive an email once your account is approved.</p>
        <div class="mt-6">
            <h2 class="text-lg font-medium">Contact Administrator</h2>
            <p class="mt-2">If you need expedited approval, please contact the admin using the information below:</p>
            <ul class="mt-3 list-disc list-inside">
                <li>Email: <a href="mailto:support@example.com" class="text-blue-600 underline">support@example.com</a></li>
                <li>Phone: <a href="tel:+1234567890" class="text-blue-600 underline">+1 234 567 890</a></li>
            </ul>
        </div>
        <div class="mt-6">
            <a href="/logout" class="text-sm text-gray-600 underline">Logout</a>
        </div>
    </div>
    <div class="text-center text-sm text-gray-500 mt-6">
        If you believe this is a mistake, please reach out to support.
    </div>
</div>
@endsection


