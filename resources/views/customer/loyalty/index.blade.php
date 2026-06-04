@extends('layouts.app')

@section('title', 'Loyalty Rewards - PaSSSna Restaurant')

@section('styles')
<style>
    .reward-card {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-radius: 15px;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    .reward-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .reward-card.locked {
        opacity: 0.6;
        filter: grayscale(50%);
    }
    .points-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
    }
    .promo-code {
        background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-family: monospace;
        font-weight: bold;
        letter-spacing: 1px;
    }
</style>
@endsection

@section('content')
<!-- Content with proper spacing for fixed navbar -->
<div class="pb-8 bg-restaurant-light min-h-screen">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8 animate-slide-down">
            <div class="bg-white/90 backdrop-blur rounded-2xl p-6 shadow-xl border border-amber-100 text-center">
                <div class="flex flex-col items-center gap-4">
                    <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-16 w-auto">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">Loyalty Rewards</h1>
                        <p class="text-gray-600">Redeem your points for exclusive rewards</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Your Points</p>
                            <p class="text-3xl font-bold text-navy-900">{{ $userPoints = Auth::user()->loyalty_points ?? 0 }}</p>
                        </div>
                        <div class="points-circle shadow-lg shadow-amber-200/50">
                            <span>{{ $userPoints }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Points Info -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Order::where('user_id', Auth::id())->where('status', 'completed')->count() }}</p>
                    <p class="text-gray-600">Completed Orders</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-coins text-green-600 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">10</p>
                    <p class="text-gray-600">Points per Order</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-gift text-yellow-600 text-xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800">{{ $activeRedemptions->count() }}</p>
                    <p class="text-gray-600">Active Rewards</p>
                </div>
            </div>
        </div>

        <!-- Active Rewards -->
        @if($activeRedemptions->count() > 0)
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Your Active Rewards</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 justify-items-center">
                @foreach($activeRedemptions as $redemption)
                <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-sm">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $redemption->reward->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $redemption->reward->formatted_reward }}</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">
                            {{ $redemption->reward->formatted_minimum_order }}
                        </span>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600 mb-1">Promo Code:</p>
                        <div class="flex items-center justify-between">
                            <span class="promo-code">{{ $redemption->promo_code }}</span>
                            <button onclick="copyToClipboard('{{ $redemption->promo_code }}')" class="text-primary-600 hover:text-primary-700">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('customer.loyalty.use', $redemption->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                            <i class="fas fa-check mr-2"></i>Mark as Used
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Available Rewards -->
        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Available Rewards</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 justify-items-center">
            @forelse($availableRewards as $reward)
            <div class="reward-card p-6 w-full max-w-sm {{ $userPoints < $reward->points_required ? 'locked' : '' }}">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-600 to-secondary-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas {{ $reward->reward_type == 'free_item' ? 'fa-utensils' : 'fa-percent' }} text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $reward->name }}</h3>
                    <p class="text-gray-600 text-sm mt-1">{{ $reward->description }}</p>
                </div>
                <div class="text-center mb-4">
                    <div class="inline-flex items-center justify-center px-4 py-2 bg-blue-100 rounded-full">
                        <i class="fas fa-coins text-yellow-500 mr-2"></i>
                        <span class="font-bold text-navy-900">{{ $reward->points_required }} Points</span>
                    </div>
                </div>
                <div class="text-center mb-4">
                    <p class="text-sm text-gray-500">{{ $reward->formatted_reward }}</p>
                    <p class="text-xs text-gray-400">{{ $reward->formatted_minimum_order }}</p>
                </div>
                @if($userPoints >= $reward->points_required)
                    <form action="{{ route('customer.loyalty.redeem') }}" method="POST">
                        @csrf
                        <input type="hidden" name="reward_id" value="{{ $reward->id }}">
                        <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-primary-700 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition duration-300">
                            <i class="fas fa-gift mr-2"></i>Redeem Now
                        </button>
                    </form>
                @else
                    <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-lg font-semibold cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>Need {{ $reward->points_required - $userPoints }} more points
                    </button>
                @endif
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-gift text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No rewards available at the moment.</p>
                <p class="text-gray-400 text-sm">Check back later for exciting rewards!</p>
            </div>
            @endforelse
        </div>

        <!-- Redemption History -->
        @if($redemptions->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Redemption History</h2>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Reward</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Points Used</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Promo Code</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($redemptions as $redemption)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800">{{ $redemption->reward->name }}</p>
                                <p class="text-sm text-gray-500">{{ $redemption->reward->formatted_reward }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-navy-900">-{{ $redemption->points_used }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $redemption->promo_code ?? 'N/A' }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($redemption->status == 'used') bg-green-100 text-green-800
                                    @elseif($redemption->status == 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($redemption->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $redemption->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($redemptions->hasPages())
                <div class="p-6 border-t">
                    {{ $redemptions->links() }}
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Promo code copied to clipboard',
                timer: 2000,
                showConfirmButton: false
            });
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endsection

