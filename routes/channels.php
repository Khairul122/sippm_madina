<?php

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Authorization for every PrivateChannel used by
| App\Infrastructure\Broadcasting\Events\*. Driver-agnostic — works
| identically once BROADCAST_CONNECTION switches from "log" to "reverb"
| in Fase 8 (see AGENTS.md "Keputusan Arsitektur yang Sudah Diambil").
| 'public-activities' (ActivityPublished) needs no entry here: it is a
| plain Channel, not a PrivateChannel, so it is not authorized at all.
|
*/

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return (int) $user->id === $id;
});

Broadcast::channel('channel-kominfo', function (User $user) {
    return $user->hasRole('kominfo');
});

Broadcast::channel('channel-opd.{opdId}', function (User $user, int $opdId) {
    return $user->hasRole('opd') && (int) $user->opd_id === $opdId;
});

Broadcast::channel('channel-camat.{kecamatanId}', function (User $user, int $kecamatanId) {
    return $user->hasRole('camat') && (int) $user->kecamatan_id === $kecamatanId;
});
