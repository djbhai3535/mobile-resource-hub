<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $query = ContactMessage::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('email', 'like', $keyword)
                    ->orWhere('subject', 'like', $keyword)
                    ->orWhere('message', 'like', $keyword);
            });
        }

        $messages = $query->latest()->paginate(20)->withQueryString();
        $unreadCount = ContactMessage::unread()->count();

        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    public function show(ContactMessage $message): View
    {
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function toggleRead(ContactMessage $message): RedirectResponse
    {
        $newStatus = $message->status === 'unread' ? 'read' : 'unread';
        $message->update(['status' => $newStatus]);

        ActivityLogger::log('update', $message, ['status' => $newStatus]);

        return back()->with('success', "Message marked as {$newStatus}.");
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $subject = $message->subject;
        $message->delete();

        ActivityLogger::log('delete', $message, ['subject' => $subject]);

        return redirect()->route('admin.messages.index')->with('success', "Inquiry '{$subject}' deleted.");
    }
}
