@extends('layouts.admin')

@section('title', 'Messages')
@section('header', 'Contact Messages')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex gap-2">
            @if(($stats['unread'] ?? 0) > 0)
                <a href="{{ route('admin.contact.markAllAsRead') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check-double mr-2"></i>Mark All as Read
                </a>
            @endif
        </div>

        <form class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Messages</option>
                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
            </select>
            <select name="type" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Types</option>
                <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General</option>
                <option value="reservation" {{ request('type') == 'reservation' ? 'selected' : '' }}>Reservation</option>
                <option value="feedback" {{ request('type') == 'feedback' ? 'selected' : '' }}>Feedback</option>
                <option value="complaint" {{ request('type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
            </select>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-sm text-gray-500">Total Messages</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow-sm p-4 border border-red-100 text-center">
            <p class="text-2xl font-bold text-red-800">{{ $stats['unread'] ?? 0 }}</p>
            <p class="text-sm text-red-600">Unread</p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-sm p-4 border border-blue-100 text-center">
            <p class="text-2xl font-bold text-blue-800">{{ $stats['read'] ?? 0 }}</p>
            <p class="text-sm text-blue-600">Read</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border border-green-100 text-center">
            <p class="text-2xl font-bold text-green-800">{{ $stats['replied'] ?? 0 }}</p>
            <p class="text-sm text-green-600">Replied</p>
        </div>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($messages as $message)
                <div class="p-4 hover:bg-gray-50 transition cursor-pointer
                    {{ !$message->is_read ? 'bg-blue-50' : '' }}"
                    onclick="location.href='{{ route('admin.contact.show', $message) }}'">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-800 {{ !$message->is_read ? 'text-blue-800' : '' }}">
                                    {{ $message->name }}
                                </h3>
                                @if(!$message->is_read)
                                    <span class="px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">New</span>
                                @endif
                                @if($message->replied_at)
                                    <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Replied</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $message->email }}</p>
                            @if($message->phone)
                                <p class="text-sm text-gray-500">{{ $message->phone }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs rounded-full
                                @switch($message->message_type)
                                    @case('general') bg-gray-100 text-gray-700 @break
                                    @case('reservation') bg-blue-100 text-blue-700 @break
                                    @case('feedback') bg-green-100 text-green-700 @break
                                    @case('complaint') bg-red-100 text-red-700 @break
                                @endswitch">
                                {{ ucfirst($message->message_type) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $message->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mt-2 line-clamp-2 text-sm">{{ $message->message }}</p>
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-envelope-open-text text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No messages found</h3>
                    <p class="text-gray-500">Contact messages will appear here</p>
                </div>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection

