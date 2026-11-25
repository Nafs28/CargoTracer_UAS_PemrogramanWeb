<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(protected AuditService $auditService)
    {
    }

    /**
     * List all users (Admin only).
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'role', 'phone', 'created_at')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200);
    }

    /**
     * Create a new user (Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'operator', 'customer'])],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $this->auditService->log($request->user(), 'CREATE_USER', $user, null, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna baru berhasil ditambahkan.',
            'data' => $user,
        ], 201);
    }

    /**
     * Update user role or profile (Admin only).
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['sometimes', Rule::in(['admin', 'operator', 'customer'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $old = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role];

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        $this->auditService->log($request->user(), 'UPDATE_USER', $user, $old, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui.',
            'data' => $user,
        ], 200);
    }

    /**
     * Delete user (Admin only, cannot delete self).
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        if ((int) $user->id === (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun yang sedang digunakan.',
            ], 422);
        }

        $old = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role];
        $user->delete();

        $this->auditService->log($request->user(), 'DELETE_USER', $user, $old, null);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus.',
        ], 200);
    }
}
