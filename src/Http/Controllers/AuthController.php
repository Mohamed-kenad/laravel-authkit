<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kenad\AuthKit\Actions\ResetPassword;
use Kenad\AuthKit\Actions\SendPasswordResetLink;
use Kenad\AuthKit\Actions\VerifyEmail;
use Kenad\AuthKit\Contracts\AuthServiceInterface;
use Kenad\AuthKit\DTOs\LoginData;
use Kenad\AuthKit\DTOs\RegisterData;
use Kenad\AuthKit\Exceptions\AuthKitException;
use Kenad\AuthKit\Exceptions\InvalidCredentialsException;
use Kenad\AuthKit\Exceptions\TooManyAttemptsException;
use Kenad\AuthKit\Http\Requests\ForgotPasswordRequest;
use Kenad\AuthKit\Http\Requests\LoginRequest;
use Kenad\AuthKit\Http\Requests\RegisterRequest;
use Kenad\AuthKit\Http\Requests\ResetPasswordRequest;
use Kenad\AuthKit\Support\ResponseFormatter;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface  $authService,
        private readonly SendPasswordResetLink $sendResetLink,
        private readonly ResetPassword         $resetPassword,
        private readonly VerifyEmail           $verifyEmail,
    ) {}

    /**
     * POST /auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register(
                RegisterData::fromArray($request->validated())
            );

            return ResponseFormatter::created(
                $user->only(['id', 'name', 'email', 'created_at']),
                'Account created successfully. Please verify your email.'
            );
        } catch (AuthKitException $e) {
            return ResponseFormatter::error($e->getMessage());
        }
    }

    /**
     * POST /auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                LoginData::fromArray($request->validated()),
                $request->input('device_name'),
            );

            return ResponseFormatter::success([
                'user'         => $result['user']->only(['id', 'name', 'email']),
                'access_token' => $result['token'],
                'token_type'   => 'Bearer',
                'expires_in'   => config('authkit.token_expiration'),
            ], 'Login successful.');
        } catch (InvalidCredentialsException $e) {
            return ResponseFormatter::unauthorized($e->getMessage());
        } catch (TooManyAttemptsException $e) {
            return ResponseFormatter::tooManyRequests($e->getMessage());
        }
    }

    /**
     * POST /auth/logout  (requires sanctum)
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ResponseFormatter::success(null, 'Logged out successfully.');
    }

    /**
     * POST /auth/logout-all  (requires sanctum)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logoutAll($request->user());

        return ResponseFormatter::success(null, 'Logged out from all devices.');
    }

    /**
     * GET /auth/me  (requires sanctum)
     */
    public function me(Request $request): JsonResponse
    {
        return ResponseFormatter::success($request->user());
    }

    /**
     * GET /auth/email/verify/{id}/{hash}  (requires signed URL)
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $this->verifyEmail->execute($request->user());

            return ResponseFormatter::success(null, 'Email verified successfully.');
        } catch (AuthKitException $e) {
            return ResponseFormatter::error($e->getMessage());
        }
    }

    /**
     * POST /auth/forgot-password
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->sendResetLink->execute($request->validated('email'));

            return ResponseFormatter::success(null, 'Password reset link sent to your email.');
        } catch (AuthKitException $e) {
            return ResponseFormatter::error($e->getMessage());
        }
    }

    /**
     * POST /auth/reset-password
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $this->resetPassword->execute(
                $validated['token'],
                $validated['email'],
                $validated['password'],
            );

            return ResponseFormatter::success(null, 'Password has been reset. Please log in again.');
        } catch (AuthKitException $e) {
            return ResponseFormatter::error($e->getMessage());
        }
    }
}
