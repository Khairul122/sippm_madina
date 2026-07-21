@extends('layouts.dashboard')

@section('content')
@php $user = auth()->user(); @endphp
<div class="sippm-card p-4">
    @if($user->hasAnyRole(['opd','camat']))
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('/dashboard/activities/create') }}" class="btn btn-sippm"><i class="bi bi-plus-circle me-1"></i>Input Kegiatan</a>
    </div>
    @endif

    <form method="get" class="row g-2 mb-3 align-items-center">
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach(['draft','diverifikasi','dipublikasikan','ditolak'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        @if($canFilterByTarget)
        <div class="col-auto">
            <select name="target" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Tujuan</option>
                <optgroup label="OPD">
                    @foreach($opds as $opd)
                        <option value="opd:{{ $opd->id }}" @selected(request('target') === 'opd:'.$opd->id)>{{ $opd->name }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Kecamatan">
                    @foreach($kecamatans as $kecamatan)
                        <option value="kecamatan:{{ $kecamatan->id }}" @selected(request('target') === 'kecamatan:'.$kecamatan->id)>{{ $kecamatan->name }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>
        @endif
        <div class="col-auto">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" title="Dari tanggal" onchange="this.form.submit()">
        </div>
        <div class="col-auto">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" title="Sampai tanggal" onchange="this.form.submit()">
        </div>
        @if(request()->anyFilled(['status','target','date_from','date_to']))
            <div class="col-auto">
                <a href="{{ url('/dashboard/activities') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Judul</th><th>Tanggal</th><th>Lokasi</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($activities as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->date->translatedFormat('d M Y') }}</td>
                    <td>{{ $activity->location ?? '-' }}</td>
                    <td><span class="sippm-badge sippm-badge-{{ $activity->status->value === 'dipublikasikan' ? 'green' : ($activity->status->value === 'ditolak' ? 'red' : 'amber') }}">{{ $activity->status->label() }}</span></td>
                    <td class="text-nowrap text-end">
                        <a href="{{ url('/dashboard/activities/'.$activity->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Detail</a>
                        
                        @can('update', $activity)
                            <a href="{{ url('/dashboard/activities/'.$activity->id.'/edit') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil-square"></i> Ubah</a>
                        @endcan
                        
                        @if($user->hasRole('kominfo'))
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
                            @elseif($activity->status->value === 'dipublikasikan')
                                <form method="post" action="{{ url('/dashboard/activities/'.$activity->id.'/unpublish') }}" class="d-inline" data-confirm="Tarik kembali kegiatan &quot;{{ $activity->title }}&quot; ke draft? Kegiatan akan hilang dari feed publik.">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary" type="submit">Tarik ke Draft</button>
                                </form>
                            @endif
                        @endif

                        @can('delete', $activity)
                            <form method="post" action="{{ url('/dashboard/activities/'.$activity->id) }}" class="d-inline" data-confirm="Hapus kegiatan secara permanen?">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                            </form>
                        @endcan
                    </td>
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
