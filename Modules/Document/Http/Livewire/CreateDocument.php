<?php

namespace Modules\Document\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Document\Entities\Document;
use Modules\Document\Entities\DocumentVersion;
use Modules\Document\Entities\Procedure;
use Modules\Document\Entities\ProcedureTemplate;
use Modules\Document\Entities\IsoPolicy;
use Modules\Document\Entities\IsoInstruction;
use Modules\Document\Entities\Sample;
use Modules\Document\Entities\IsoSystem;
use Modules\Setting\Entities\Department;
use App\Traits\TenantFileManager;

class CreateDocument extends Component
{
    use WithFileUploads;
    use TenantFileManager;

    public $title;
    public $document_number;
    public $document_type = 'custom';
    public $related_process;
    public $department;
    public $content;
    public $file;
    public $template_id;
    public $procedure_setup_data;
    public $version = '1.0';
    public $isoSystems;
    public $departments;
    public $templates = [];
    public $showTemplateSection = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'document_number' => 'nullable|string|max:50',
        'document_type' => 'required|in:procedure,policy,instruction,sample,custom',
        'department' => 'required',
        'version' => 'required|string',
        'content' => 'nullable|string',
        'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // 10MB max
        'procedure_setup_data' => 'nullable|string',
    ];

    public function mount()
    {
        $this->isoSystems = IsoSystem::where('status', true)->get();
        $this->departments = Department::get();
        $this->document_number = Str::uuid()->toString();
    }

    public function updatedDocumentType($value)
    {
        $this->resetTemplates();
        $this->showTemplateSection = in_array($value, ['procedure', 'policy', 'instruction', 'sample']);
        
        if ($this->showTemplateSection) {
            $this->loadTemplates();
        }
    }

    public function resetTemplates()
    {
        $this->templates = [];
        $this->template_id = null;
        $this->procedure_setup_data = null;
    }

    public function loadTemplates()
    {
        switch ($this->document_type) {
            case 'procedure':
                $this->templates = Procedure::get();
                break;
            case 'policy':
                $this->templates = IsoPolicy::get();
                break;
            case 'instruction':
                $this->templates = IsoInstruction::get();
                break;
            case 'sample':
                $this->templates = Sample::get();
                break;
            default:
                $this->templates = [];
                break;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();
            
            // Create document record
            $document = Document::create([
                'title' => $this->title,
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'related_process' => $this->related_process,
                'status_id' => 11, // Draft status
                'department' => $this->department,
                'created_by' => auth('tenant')->id(),
                'creation_date' => now(),
            ]);

            // Handle procedure template if applicable
            if ($this->document_type === 'procedure' && $this->procedure_setup_data && $this->template_id) {
                $procedureData = json_decode($this->procedure_setup_data, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid procedure data format: ' . json_last_error_msg());
                }
                
                $procedure = Procedure::where('id', $this->template_id)->first();
                if (!$procedure) {
                    throw new \Exception('Invalid procedure ID');
                }
                
                // Save in ProcedureTemplate
                $procedureTemplate = ProcedureTemplate::create([
                    'title' => $this->title,
                    'procedure_id' => $this->template_id,
                    'content' => $procedureData,
                    'parent_id' => 1
                ]);
            }

            // Handle file uploads
            $fileName = $this->generateFileName($this->document_number, $this->title);
            $content = $this->file ? $this->file : $this->content;

            $filePath = $this->saveDocument(
                auth('tenant')->user()->id,
                $this->document_type,
                $fileName,
                $content,
                'draft'
            );

            $issueDate = now();
            $validYears = 3;
            $expiryDate = Carbon::parse($issueDate)->addYears($validYears);
            $reviewDate = Carbon::parse($issueDate)->addYears($validYears - 1);

            // Create document version
            $documentVersion = DocumentVersion::create([
                'document_id' => $document->id,
                'version' => $this->version,
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'review_due_date' => $reviewDate,
                'status_id' => 17, // Draft status for version
                'storage_path' => $filePath,
                'file_path' => $filePath,
                'is_active' => true,
            ]);

            DB::commit();

            // Reset form after successful submission
            $this->reset(['title', 'document_type', 'related_process', 'department', 'content', 'file', 'template_id', 'procedure_setup_data']);
            $this->document_number = Str::uuid()->toString();
            
            session()->flash('success', __('Document created successfully'));
            return redirect()->route('tenant.document.index');

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', __('Error creating document: ') . $e->getMessage());
        }
    }

    public function render()
    {
        return view('document::livewire.create-document');
    }
}
