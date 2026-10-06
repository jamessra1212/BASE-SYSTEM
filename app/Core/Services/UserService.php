<?php

namespace App\Core\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected User $user)
    {}

    /**
     * Find a user model instance by ID or fail.
     */
    public function findById($id): User
    {
        // Calling find() directly on the model instance returns a pure User object
        return $this->user->findOrFail($id);
    }

    /**
     * Handle view formatting layout responses context bindings.
     */
    public function main_user($request)
    {
        $user = $request->has('id') ? $this->findById($request->id) : null;

        return view('BackEnd.auth.extras.user_entry', [
            'user'      => $user,
            'modalName' => 'USER_ENTRY_MODAL'
        ]);
    }

    /**
     * Process persistence engine pipeline updates and new structural record creations.
     */
    public function user_store($request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $validated['id'] ?? null;
        $userInstance = $userId ? $this->findById($userId) : null;
        $actor = $request->user();

        if ($userInstance && $denied = $this->denyIfProtected($actor, $userInstance)) {
            return $denied;
        }

        if (($validated['role'] ?? null) === 'Super Admin' && ! $actor->isSuperAdmin()) {
            return $this->error('Only a Super Admin can grant the Super Admin role.', 403);
        }

        $role = $validated['role'] ?? null;
        unset($validated['id'], $validated['role'], $validated['avatar']);

        // Blank on update means "keep the current password"; the model's
        // 'hashed' cast takes care of hashing a new one.
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // Capitalize structural text items uniformly
        if (isset($validated['minitial'])) {
            $validated['minitial'] = strtoupper($validated['minitial']);
        }

        // FILE UPLOAD HANDLING PIPELINE FOR AVATAR IMAGES
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            $validated['avatar_data'] = base64_encode(file_get_contents($file->getRealPath()));
            $validated['avatar_mime'] = $file->getMimeType();
        }

        try {
            $userInstance = DB::transaction(function () use ($userInstance, $validated, $role) {
                if ($userInstance) {
                    $userInstance->update($validated);
                } else {
                    $validated['img_slug'] ??= 'avatar-default.png';
                    $userInstance = $this->user->create($validated);
                }

                // Sync the RBAC role (single-select: empty selection clears any existing role)
                $userInstance->syncRoles($role ? [$role] : []);

                return $userInstance;
            });
        } catch (\Throwable $e) {
            return $this->failure('Saving the user failed.', $e);
        }

        activity()
            ->causedBy($actor)
            ->performedOn($userInstance)
            ->log(($userId ? 'updated' : 'created') . " user \"{$userInstance->fullname}\"");

        return response()->json([
            'status'  => 'success',
            'message' => $userId
                ? 'System User record updates have been applied successfully.'
                : 'System User record has been processed and committed successfully.',
        ], 200);
    }

    public function user_cpass($request)
    {
        $user = $this->findById($request->id);
        return view('BackEnd.auth.extras.user_cpass', compact('user'));
    }

    // 2. Form execution destination point
    public function user_upass($request): JsonResponse
    {
        $userInstance = $this->findById($request->id);

        if ($denied = $this->denyIfProtected($request->user(), $userInstance)) {
            return $denied;
        }

        try {
            // Rotating the remember token signs the user out of "remember me" sessions
            $userInstance->forceFill([
                'password'       => $request->password,
                'remember_token' => Str::random(60),
            ])->save();
        } catch (\Throwable $e) {
            return $this->failure('Updating the password failed.', $e);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($userInstance)
            ->log("changed password for user \"{$userInstance->fullname}\"");

        return response()->json([
            'status'  => 'success',
            'message' => 'Account password credentials have been refreshed successfully.'
        ], 200);
    }

    public function user_ustat($request): JsonResponse
    {
        // Validate that the request parameters match structural constraints securely
        $request->validate([
            'id'           => ['required', 'integer', 'exists:users,id'],
            'is_activated' => ['required', 'in:0,1']
        ]);

        $userInstance = $this->findById($request->id);

        // Prevent users from deactivating their own active profile session context
        if ($request->user()->id == $userInstance->id && $request->is_activated == 0) {
            return $this->error('Security policy breach: You cannot deactivate your own active administrative session context.', 403);
        }

        if ($denied = $this->denyIfProtected($request->user(), $userInstance)) {
            return $denied;
        }

        try {
            $userInstance->update([
                'is_activated' => $request->is_activated
            ]);
        } catch (\Throwable $e) {
            return $this->failure('Changing the account status failed.', $e);
        }

        $statusText = $request->is_activated == 1 ? 'activated' : 'deactivated';

        activity()
            ->causedBy($request->user())
            ->performedOn($userInstance)
            ->log("{$statusText} user \"{$userInstance->fullname}\"");

        return response()->json([
            'status'  => 'success',
            'message' => "The profile record has been successfully {$statusText}."
        ], 200);
    }

    public function user_destroy($request): JsonResponse
    {
        // Validate that the request parameters match structural constraints securely
        $request->validate([
            'id' => ['required', 'integer', 'exists:users,id']
        ]);

        // Prevent users from deleting their own active profile session context
        if ($request->user()->id == $request->id) {
            return $this->error('Security policy breach: You cannot delete your own active administrative session context.', 403);
        }

        $userInstance = $this->findById($request->id);

        if ($denied = $this->denyIfProtected($request->user(), $userInstance)) {
            return $denied;
        }

        $fullname = $userInstance->fullname;

        try {
            $userInstance->delete();
        } catch (\Throwable $e) {
            return $this->failure('Deleting the user failed.', $e);
        }

        activity()
            ->causedBy($request->user())
            ->log("deleted user \"{$fullname}\"");

        return response()->json([
            'status'  => 'success',
            'message' => 'The user profile record has been successfully deleted.'
        ], 200);
    }

    /**
     * Holding "manage users" must not be a path to Super Admin: only a
     * Super Admin may edit, re-password, deactivate or delete another one.
     */
    protected function denyIfProtected(User $actor, User $target): ?JsonResponse
    {
        if ($target->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            return $this->error('Only a Super Admin can modify a Super Admin account.', 403);
        }

        return null;
    }

    protected function error(string $message, int $status): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message], $status);
    }

    /**
     * Log the real exception server-side; the client only gets a generic
     * message, so SQL/stack details never leak into the UI.
     */
    protected function failure(string $message, \Throwable $e): JsonResponse
    {
        Log::error($message, ['exception' => $e]);

        return $this->error($message . ' Please try again or contact the administrator.', 500);
    }
}
