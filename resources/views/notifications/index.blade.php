@extends('layouts.app')
@section('title', 'Notifikasi Sistem')

@section('content')
@php
    $hasUnread = \App\Models\PushNotification::whereNull('customer_id')->where('is_read', false)->exists();
@endphp
<div class="p-6 space-y-5" x-data="notificationPage()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Notifikasi Sistem</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau aktivitas real-time Booking, Pembayaran, dan Ulasan</p>
        </div>
        <button type="button"
                @click="markAllAsRead()"
                x-show="hasUnread"
                class="flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50"
                x-cloak>
            Tandai Semua Dibaca
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden divide-y divide-slate-100">
        @forelse($notifications as $n)
        <div class="p-5 flex items-start gap-4 hover:bg-slate-50/50 transition-colors {{ !$n->is_read ? 'bg-slate-50/30' : '' }}">
            <div class="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0 {{ !$n->is_read ? 'bg-red-500' : 'bg-slate-200' }}"></div>
            
            <div class="flex-1 space-y-1">
                <div class="flex items-center gap-2">
                    <h4 class="font-semibold text-sm {{ !$n->is_read ? 'text-slate-900' : 'text-slate-600' }}">{{ $n->title }}</h4>
                    <span class="text-[10px] text-slate-400">•</span>
                    <span class="text-[11px] text-slate-400">{{ $n->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-slate-600 leading-normal">{{ $n->body }}</p>
            </div>

            @if(!$n->is_read)
            <button type="button"
                    @click="markAsRead('{{ $n->id }}')"
                    class="text-xs text-blue-600 hover:underline font-medium flex-shrink-0">
                Tandai dibaca
            </button>
            @else
            <span class="text-xs text-slate-400 flex-shrink-0">Sudah dibaca</span>
            @endif
        </div>
        @empty
        <div class="p-12 text-center text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-sm">Tidak ada notifikasi baru</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="pt-2">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function notificationPage() {
    return {
        hasUnread: {{ $hasUnread ? 'true' : 'false' }},
        async markAsRead(id) {
            try {
                const response = await fetch(`/notifications/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        },
        async markAllAsRead() {
            try {
                const response = await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        }
    }
}
</script>
@endpush
