<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - VenueLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1F398A 0%, #1F398A/80 100%);
        }
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Logo/Brand -->
            <div class="pulse-animation">
                <div class="mx-auto h-20 w-20 gradient-bg rounded-full flex items-center justify-center">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div>
                <h1 class="text-9xl font-bold text-red-500 mb-4">500</h1>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Internal Server Error</h2>
                <p class="text-lg text-gray-600 mb-8">
                    Oops! Something went wrong on our end. We're working to fix this issue. Please try again later.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <button onclick="window.location.reload()" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#1F398A] hover:bg-[#1F398A]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1F398A] transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Try Again
                </button>
                
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('welcome.client.page') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1F398A] transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Go Home
                    </a>
                    
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('dashboard') : route('user.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-[#1F398A] bg-[#1F398A]/10 hover:bg-[#1F398A]/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1F398A] transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Dashboard
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-12 p-6 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Technical Support</h3>
                <p class="text-sm text-red-600 mb-4">
                    If this error persists, please contact our technical support team with the error details.
                </p>
                <div class="flex justify-center space-x-4 text-sm">
                    <a href="mailto:tech@venuelink.com" class="text-red-600 hover:text-red-800">
                        📧 tech@venuelink.com
                    </a>
                    <span class="text-red-400">|</span>
                    <a href="tel:+1234567890" class="text-red-600 hover:text-red-800">
                        📞 +1 (234) 567-890
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
