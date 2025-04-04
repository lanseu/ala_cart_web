<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        return response()->json($this->notificationService->getAll());
    }

    public function store(NotificationRequest $request)
    {
        $notification = $this->notificationService->create($request->validated());
        return response()->json($notification, 201);
    }

    public function show(string $id)
    {
        return response()->json($this->notificationService->getById($id));
    }

    public function update(NotificationRequest $request, string $id)
    {
        $notification = $this->notificationService->update($id, $request->validated());
        return response()->json($notification);
    }

    public function destroy(string $id)
    {
        $this->notificationService->delete($id);
        return response()->json(null, 204);
    }
}
