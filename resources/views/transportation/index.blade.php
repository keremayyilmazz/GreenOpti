@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Taşıma Yöntemleri</h2>
        <a href="{{ route('transportations.create') }}" class="btn btn-primary">Yeni Taşıma Yöntemi</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>Km Başına Maliyet</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transportations as $transportation)
                    <tr>
                        <td>{{ $transportation->name }}</td>
                        <td>{{ number_format($transportation->cost_per_km, 2) }} ₺</td>
                        <td>
                            <a href="{{ route('transportations.edit', $transportation) }}" class="btn btn-sm btn-primary">Düzenle</a>
                            <form action="{{ route('transportations.destroy', $transportation) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Emin misiniz?')">Sil</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
