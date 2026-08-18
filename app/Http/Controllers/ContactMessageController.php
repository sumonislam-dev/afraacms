<?php

namespace App\Http\Controllers;

use App\CMS\Services\ContactService;
use App\Http\Requests\StoreContactMessageRequest;
use Illuminate\Http\RedirectResponse;

class ContactMessageController extends Controller
{
    public function __construct(private readonly ContactService $contact) {}

    /**
     * Store a newly submitted contact message.
     */
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $this->contact->submit($request->safe()->except('website'), $request->ip());

        return back()->with('success', __("Thanks for reaching out! We'll get back to you soon."));
    }
}
