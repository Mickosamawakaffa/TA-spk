@extends('layouts.app')

@section('title', 'Detail Kontrakan')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ $kontrakan->nama }}</h3>
        <a href="{{ route('kontrakan.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p class="mb-2"><strong>Alamat:</strong> {{ $kontrakan->alamat }}</p>
            <p class="mb-2"><strong>Harga:</strong> Rp {{ number_format($kontrakan->harga, 0, ',', '.') }}</p>
            <p class="mb-2"><strong>Jarak:</strong> {{ $kontrakan->jarak }} meter</p>
            <p class="mb-2"><strong>Jumlah Kamar:</strong> {{ $kontrakan->jumlah_kamar }}</p>
            @if(!empty($kontrakan->fasilitas))
            <p class="mb-0"><strong>Fasilitas:</strong> {{ $kontrakan->fasilitas }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
