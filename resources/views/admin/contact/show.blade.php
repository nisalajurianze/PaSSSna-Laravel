@extends('layouts.admin')

@section('title', 'Contact Message Details')
@section('header', 'Contact Message Details')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Message from {{ $contact->name }}</h3>
            <span class="px-4 py-2 {{ $contact->is_read ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800' }} rounded-full text-sm font-medium">
                {{ $contact->is_read ? 'Read' : 'Unread' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Contact Information</h4>
                <p class="font-medium">{{ $contact->name }}</p>
                <p class="text-gray-600">{{ $contact->email }}</p>
                <p class="text-gray-600">{{ $contact->phone ?? 'No phone provided' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Message Details</h4>
                <p><span class="text-gray-500">Subject:</span> {{ $contact->subject ?? 'No subject' }}</p>
                <p><span class="text-gray-500">Received:</span> {{ $contact->created_at ? $contact->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
            </div>
        </div>

        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Message</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $contact->message }}</p>
            </div>
        </div>

        @if($contact->reply_message)
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Reply</h4>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $contact->reply_message }}</p>
                <p class="text-sm text-gray-500 mt-2">
                    Replied on: {{ $contact->replied_at ? $contact->replied_at->format('M d, Y h:i A') : 'N/A' }}
                </p>
            </div>
        </div>
        @endif
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.contact.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Back</a>
        @if(!$contact->reply_message)
        <form action="{{ route('admin.contact.reply', ['contact' => $contact->id]) }}" method="POST" class="flex gap-2">
            @csrf
            <textarea name="reply_message" rows="3" class="px-4 py-2 border border-gray-300 rounded-lg w-80" placeholder="Enter your reply..." required></textarea>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Send Reply</button>
        </form>
        @endif
    </div>
</div>
@endsection
