<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ContactMessage::class, 'contactMessage');
    }

    /**
     * Display the inbox, newest messages first.
     */
    public function index(): View
    {
        $messages = ContactMessage::query()
            ->when(request('search'), fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.contact.index', compact('messages'));
    }

    /**
     * Display a single message, marking it as read.
     */
    public function show(ContactMessage $contactMessage): View
    {
        $contactMessage->markAsRead();

        return view('admin.contact.show', ['message' => $contactMessage]);
    }

    /**
     * Delete the given message.
     */
    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact.index')->with('success', __('Message deleted successfully.'));
    }
}
