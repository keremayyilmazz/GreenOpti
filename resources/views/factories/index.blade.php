@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Fabrikalarım</h5>
                    <a href="{{ route('factories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Fabrika Ekle
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($factories->isEmpty())
                        <div class="text-center p-4">
                            <p class="text-muted">Henüz fabrika eklenmemiş.</p>
                            <a href="{{ route('factories.create') }}" class="btn btn-primary">
                                İlk Fabrikayı Ekle
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fabrika Adı</th>
                                        <th>Adres</th>
                                        <th>Koordinatlar</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($factories as $factory)
                                        <tr>
                                            <td>{{ $factory->name }}</td>
                                            <td>{{ $factory->address }}</td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ number_format($factory->latitude, 6) }}, 
                                                    {{ number_format($factory->longitude, 6) }}
                                                </small>
                                            </td>
                                            <td>
                                                <a href="{{ route('factories.edit', $factory) }}" 
                                                   class="btn btn-sm btn-info">
                                                    Düzenle
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection