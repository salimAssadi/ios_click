<?php

namespace Modules\DocumentControl\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Modules\DocumentControl\Models\Document;
use Illuminate\Http\Request;

class DocumentControlController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'documentcontrol::documents';
        $this->routePrefix = 'documents';
        $this->moduleName = 'DocumentControl';
    }

    public function index()
    {
        $documents = Document::with(['category', 'creator', 'approver'])->paginate(10);
        return $this->view('index', compact('documents'));
    }

    public function create()
    {
        return $this->view('create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required',
            'category_id' => 'required|exists:document_categories,id',
        ]);

        Document::create($validated);

        return $this->success('Document created successfully.');
    }

    public function show(Document $document)
    {
        return $this->view('show', compact('document'));
    }

    public function edit(Document $document)
    {
        return $this->view('edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required',
            'category_id' => 'required|exists:document_categories,id',
        ]);

        $document->update($validated);

        return $this->success('Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        $document->delete();
        return $this->success('Document deleted successfully.');
    }
}
