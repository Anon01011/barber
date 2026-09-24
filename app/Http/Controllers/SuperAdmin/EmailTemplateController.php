<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::whereNull('salon_id')->get();
        return view('super-admin.email-templates.index', compact('templates'));
    }

    public function edit($id)
    {
        $template = EmailTemplate::whereNull('salon_id')->findOrFail($id);
        return view('super-admin.email-templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::whereNull('salon_id')->findOrFail($id);

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template->update([
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }
}
