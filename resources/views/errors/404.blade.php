<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - VenueLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1F398A 0%, #1F398A/80 100%);
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Logo/Brand -->
            <div class="floating-animation">
                <div class="mx-auto h-20 w-20 gradient-bg rounded-full flex items-center justify-center">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>

            <!-- Error Code -->
            <div>
                <h1 class="text-9xl font-bold text-[#1F398A] mb-4">404</h1>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Page Not Found</h2>
                <p class="text-lg text-gray-600 mb-8">
                    Sorry, we couldn't find the page you're looking for. The page might have been moved, deleted, or you entered the wrong URL.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ route('welcome.client.page') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#1F398A] hover:bg-[#1F398A]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1F398A] transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Go Home
                </a>
                
                <div class="flex justify-center space-x-4">
                    <button onclick="history.back()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1F398A] transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </button>
                    
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
            <div class="mt-12 p-6 bg-gray-100 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Need Help?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    If you believe this is an error, please contact our support team.
                </p>
                <div class="flex justify-center space-x-4 text-sm">
                    <a href="mailto:support@venuelink.com" class="text-[#1F398A] hover:text-[#1F398A]/80">
                        📧 support@venuelink.com
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="tel:+1234567890" class="text-[#1F398A] hover:text-[#1F398A]/80">
                        📞 +1 (234) 567-890
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
