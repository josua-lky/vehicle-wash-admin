@extends('layouts.app')
@section('title', 'Edit Booking - ' . $booking->booking_code)

@section('content')
<div class="p-6 space-y-6">
    {{-- Back Link --}}
    <div class="flex items-center justify-between">
        <a href="/bookings/{{ $booking->id }}" class="flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Detail Booking
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 max-w-2xl">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit Data Booking</h3>
            <p class="text-xs text-slate-400 mt-0.5">Ubah status booking, tugaskan teknisi, atau tambahkan catatan.</p>
        </div>
        <form method="POST" action="/bookings/{{ $booking->id }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">Status Booking</label>
                    <select name="status" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        @foreach(['pending' => 'Pending', 'confirmed' => 'Dikonfirmasi', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $label)
                        <option value="{{ $val }}" {{ old('status', $booking->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide font-medium">Catatan Admin / Catatan Tambahan</label>
                    <textarea name="notes" rows="4" placeholder="Catatan khusus dari admin..."
                              class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-700 resize-none">{{ old('notes', $booking->notes) }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="/bookings/{{ $booking->id }}" class="px-4 py-2.5 text-sm font-medium border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold rounded-xl text-slate-900 shadow" style="background:#F0C419;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
