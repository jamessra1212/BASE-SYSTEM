<?php

namespace App\Services\BackEnd;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MainService
{
    public function __construct(
        protected User $user
    ) {}

    /**
     * Fetch active platform session telemetry
     */
    public function getUserSessions(User $user): mixed
    {
        if (config('session.driver') !== 'database') {
            return [];
        }

        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    /**
     * Mutate user password security credentials
     */
    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->password = Hash::make($newPassword);
        $user->setRememberToken(Str::random(60));
        return $user->save();
    }

    /**
     * Detach linked external social profile identities
     */
    public function disconnectGoogle(User $user): bool
    {
        $user->google_id = null;
        return $user->save();
    }

    /**
     * Drop matched dataset tracking entries out of active sessions storage
     */
    public function terminateSessions(User $user, ?string $targetId, string $currentId): void
    {
        $query = DB::table('sessions')->where('user_id', $user->id);

        if ($targetId) {
            // Drop a specific isolated user device session window
            $query->where('id', $targetId)->delete();
        } else {
            // Flush all peripheral application connections except the current machine window context
            $query->where('id', '!=', $currentId)->delete();
        }

        // Deleting the session row alone isn't enough: a device holding a
        // "remember me" cookie would sign itself straight back in. Laravel
        // keeps one remember token per user, so rotating it cancels every
        // remembered device at once.
        $user->setRememberToken(Str::random(60));
        $user->save();
    }
}
