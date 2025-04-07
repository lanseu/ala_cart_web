<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function getAll()
    {
        return Notification::all();
    }

    public function getById(string $id)
    {
        return Notification::findOrFail($id);
    }

    public function create(array $data)
    {
        return Notification::create($data);
    }

    public function update(string $id, array $data)
    {
        $notification = Notification::findOrFail($id);
        $notification->update($data);

        return $notification;
    }

    public function delete(string $id): void
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
    }
}
