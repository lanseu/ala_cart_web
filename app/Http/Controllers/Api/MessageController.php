<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $messageService;

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

    public function reply(MessageRequest $request, $parentId)
    {
        $reply = $this->messageService->replyToMessage($parentId, $request->validated());
        return response()->json($reply, 201);
    }
}
