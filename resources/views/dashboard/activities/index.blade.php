@extends('layouts.dashboard')

@section('content')
@php $user = auth()->user(); @endphp
<div class="sippm-card p-4">
    @if($user->hasAnyRole(['opd','camat']))
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('/dashboard/activities/create') }}" class="btn btn-sippm"><i class="bi bi-plus-circle me-1"></i>Input Kegiatan</a>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Judul</th><th>Tanggal</th><th>Lokasi</th><th>Status</th>@if($user->hasRole('kominfo'))<th></th>@endif</tr></thead>
            <tbody>
            @forelse($activities as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->date->translatedFormat('d M Y') }}</td>
                    <td>{{ $activity->location ?? '-' }}</td>
                    <td><span class="sippm-badge sippm-badge-{{ $activity->status->value === 'dipublikasikan' ? 'green' : ($activity->status->value === 'ditolak' ? 'red' : 'amber') }}">{{ $activity->status->label() }}</span></td>
                    @if($user->hasRole('kominfo'))
                    <td class="text-nowrap">
                        @if($activity->status->value === 'draft')
                            <form method="post" action="{{ url('/dashboard/activities/'.$activity->id.'/verify') }}" class="d-inline" data-confirm="Verifikasi kegiatan &quot;{{ $activity->title }}&quot; sebagai valid?">
                                @csrf
                                <input type="hidden" name="is_valid" value="1">
                                <button class="btn btn-sm btn-outline-success" type="submit">Verifikasi</button>
                            </form>
                        @elseif($activity->status->value === 'diverifikasi')
                            <form method="post" action="{{ url('/dashboard/activities/'.$activity->id.'/publish') }}" class="d-inline" data-confirm="Publikasikan kegiatan &quot;{{ $activity->title }}&quot; ke feed publik?">
                                @csrf
                                <button class="btn btn-sm btn-sippm" type="submit">Publikasikan</button>
                            </form>
                        @endif
                    </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kegiatan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $activities->links() }}
</div>
@endsection
