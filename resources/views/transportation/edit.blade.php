@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Taşıma Yöntemi Düzenle</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('transportations.update', $transportation) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Taşıma Yöntemi Adı</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $transportation->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cost_per_km" class="form-label">Km Başına Maliyet (₺)</label>
                            <input type="number" step="0.01" class="form-control @error('cost_per_km') is-invalid @enderror" 
                                id="cost_per_km" name="cost_per_km" value="{{ old('cost_per_km', $transportation->cost_per_km) }}" required>
                            @error('cost_per_km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('transportations.index') }}" class="btn btn-secondary">Geri</a>
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
