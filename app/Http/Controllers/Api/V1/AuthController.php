<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\DeleteAccountRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Requests\Api\VerifyResetCodeRequest;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Check if email already exists (more specific error)
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => 'error',
                'code' => 'EMAIL_EXISTS',
                'message' => 'An account with this email already exists.',
                'errors' => [
                    'email' => ['The email has already been taken.']
                ]
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'code' => 'REGISTRATION_SUCCESS',
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $this->formatUser($user),
        ], 201);
    }

    /**
     * Login user and create token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // User not found
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'code' => 'USER_NOT_FOUND',
                'message' => 'No account found with this email address.',
            ], 404);
        }

        // Wrong password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_PASSWORD',
                'message' => 'The password you entered is incorrect.',
            ], 401);
        }

        // Account blocked
        if ($user->isBlocked()) {
            return response()->json([
                'status' => 'error',
                'code' => 'ACCOUNT_BLOCKED',
                'message' => 'Your account has been blocked. Please contact support.',
            ], 403);
        }

        // Revoke all existing tokens (single device login)
        // Uncomment if you want single device login only
        // $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'code' => 'LOGIN_SUCCESS',
            'message' => 'Login successful',
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Logout user (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'code' => 'LOGOUT_SUCCESS',
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens).
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'code' => 'LOGOUT_ALL_SUCCESS',
            'message' => 'Logged out from all devices successfully',
        ]);
    }

    /**
     * Get current authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'user' => $this->formatUser($request->user()),
        ]);
    }

    /**
     * Update user profile.
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();

        // Check if email is being changed and already exists
        if ($request->has('email') && $request->email !== $user->email) {
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'EMAIL_EXISTS',
                    'message' => 'This email is already in use by another account.',
                    'errors' => [
                        'email' => ['The email has already been taken.']
                    ]
                ], 422);
            }
        }

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'status' => 'success',
            'code' => 'PROFILE_UPDATED',
            'message' => 'Profile updated successfully',
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Request password reset code.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'code' => 'USER_NOT_FOUND',
                'message' => 'No account found with this email address.',
            ], 404);
        }

        if ($user->isBlocked()) {
            return response()->json([
                'status' => 'error',
                'code' => 'ACCOUNT_BLOCKED',
                'message' => 'This account has been blocked. Please contact support.',
            ], 403);
        }

        // Generate reset code
        $resetCode = PasswordResetCode::generateFor($request->email);

        // Send email with code (in production, use proper mail template)
        try {
            Mail::raw(
                "Your password reset code is: {$resetCode->code}\n\nThis code will expire in 15 minutes.\n\nIf you didn't request this, please ignore this email.",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Password Reset Code - Kiasi Daily');
                }
            );
        } catch (\Exception $e) {
            // Log the error but don't expose it to user
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'code' => 'RESET_CODE_SENT',
            'message' => 'A password reset code has been sent to your email.',
            // Include code in response for development/testing (remove in production)
            'debug_code' => config('app.debug') ? $resetCode->code : null,
        ]);
    }

    /**
     * Verify password reset code.
     */
    public function verifyResetCode(VerifyResetCodeRequest $request): JsonResponse
    {
        $resetCode = PasswordResetCode::findValidCode($request->email, $request->code);

        if (!$resetCode) {
            // Check if code exists but expired
            $expiredCode = PasswordResetCode::where('email', $request->email)
                ->where('code', $request->code)
                ->where('used', false)
                ->first();

            if ($expiredCode && $expiredCode->expires_at->isPast()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'CODE_EXPIRED',
                    'message' => 'This reset code has expired. Please request a new one.',
                ], 400);
            }

            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_CODE',
                'message' => 'Invalid reset code. Please check and try again.',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'code' => 'CODE_VALID',
            'message' => 'Reset code is valid. You can now reset your password.',
        ]);
    }

    /**
     * Reset password with code.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'code' => 'USER_NOT_FOUND',
                'message' => 'No account found with this email address.',
            ], 404);
        }

        $resetCode = PasswordResetCode::findValidCode($request->email, $request->code);

        if (!$resetCode) {
            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_CODE',
                'message' => 'Invalid or expired reset code.',
            ], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Mark code as used
        $resetCode->markAsUsed();

        // Revoke all existing tokens (force re-login)
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'code' => 'PASSWORD_RESET_SUCCESS',
            'message' => 'Password has been reset successfully. Please login with your new password.',
        ]);
    }

    /**
     * Change password (for logged in users).
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_CURRENT_PASSWORD',
                'message' => 'The current password you entered is incorrect.',
                'errors' => [
                    'current_password' => ['The current password is incorrect.']
                ]
            ], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Revoke all other tokens (keep current session)
        $currentTokenId = $request->user()->currentAccessToken()->id;
        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        return response()->json([
            'status' => 'success',
            'code' => 'PASSWORD_CHANGED',
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * Delete user account.
     */
    public function deleteAccount(DeleteAccountRequest $request): JsonResponse
    {
        $user = $request->user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_PASSWORD',
                'message' => 'The password you entered is incorrect.',
                'errors' => [
                    'password' => ['The password is incorrect.']
                ]
            ], 400);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Delete user (this will cascade delete transactions due to FK)
        $user->delete();

        return response()->json([
            'status' => 'success',
            'code' => 'ACCOUNT_DELETED',
            'message' => 'Your account has been deleted successfully.',
        ]);
    }

    /**
     * Check if email exists (for registration validation).
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json([
            'status' => 'success',
            'email' => $request->email,
            'exists' => $exists,
            'message' => $exists ? 'This email is already registered.' : 'This email is available.',
        ]);
    }

    /**
     * Format user data for response.
     */
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
            'created_at' => $user->created_at,
        ];
    }
}
