<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kenad\AuthKit\Contracts\DeviceServiceInterface;
use Kenad\AuthKit\Support\ResponseFormatter;

class DeviceController extends Controller
{
    public function __construct(
        private readonly DeviceServiceInterface $deviceService,
    ) {}

    /**
     * GET /auth/devices  — list all active devices for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $devices = $this->deviceService->list($request->user());

        return ResponseFormatter::success($devices, 'Devices retrieved successfully.');
    }

    /**
     * DELETE /auth/devices/{id}  — revoke a specific device session.
     */
    public function revoke(Request $request, int $id): JsonResponse
    {
        $revoked = $this->deviceService->revoke($request->user(), $id);

        if (! $revoked) {
            return ResponseFormatter::error('Device not found.', 404);
        }

        return ResponseFormatter::success(null, 'Device session revoked successfully.');
    }
}
