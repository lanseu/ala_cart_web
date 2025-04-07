<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index()
    {
        return response()->json($this->messageService->getAllMessages());
    }

    public function show($id)
    {
        return response()->json($this->messageService->getMessageById($id));
    }

    public function store(MessageRequest $request)
    {
        $message = $this->messageService->createMessage($request->validated());

        return response()->json($message, 201);
    }

    public function update(Request $request, $id)
    {
        $message = $this->messageService->updateMessage($id, $request->all());

        return response()->json([
            'message' => 'Message updated successfully',
            'data' => $message,
        ]);
    }

    public function destroy($id)
    {
        $this->messageService->deleteMessage($id);

        return response()->json(['message' => 'Message deleted successfully']);
    }

    public function getReplies($id)
    {
        return response()->json($this->messageService->getReplies($id));
    }

    public function replyToMessage(Request $request, $messageId)
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        if (empty($messageId)) {
            return response()->json(['error' => 'Message ID cannot be empty'], 400);
        }

        \Log::info('Replying to Message ID: '.json_encode($messageId));

        try {
            $chatContent = $request->input('chat');
            $fullName = trim($user->first_name.' '.($user->middle_name ? $user->middle_name.' ' : '').$user->last_name);

            $reply = $this->messageService->replyToMessage($messageId, $user, $fullName, $chatContent);

            return response()->json($reply, 201);
        } catch (\Exception $e) {
            \Log::error('Error in replyToMessage: '.$e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getMessagesByUserId($userId)
    {
        return $this->messageService->getMessagesByUserId($userId);
    }
}
