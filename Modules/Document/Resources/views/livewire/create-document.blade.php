<div>
    <div class="card">
        <div class="card-header">
            <h5>{{ __('Create New Document') }}</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title" class="form-label">{{ __('Document Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" wire:model.lazy="title">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="document_number" class="form-label">{{ __('Document Number') }}</label>
                            <input type="text" class="form-control @error('document_number') is-invalid @enderror" id="document_number" wire:model.lazy="document_number" readonly>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="document_type" class="form-label">{{ __('Document Type') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('document_type') is-invalid @enderror" id="document_type" wire:model="document_type">
                                <option value="custom">{{ __('Custom Document') }}</option>
                                <option value="procedure">{{ __('Procedure') }}</option>
                                <option value="policy">{{ __('Policy') }}</option>
                                <option value="instruction">{{ __('Instruction') }}</option>
                                <option value="sample">{{ __('Sample') }}</option>
                            </select>
                            @error('document_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="department" class="form-label">{{ __('Department') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('department') is-invalid @enderror" id="department" wire:model.lazy="department">
                                <option value="">{{ __('Select Department') }}</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="related_process" class="form-label">{{ __('Related Process') }}</label>
                            <select class="form-select @error('related_process') is-invalid @enderror" id="related_process" wire:model.lazy="related_process">
                                <option value="">{{ __('Select Process') }}</option>
                                @foreach($isoSystems as $system)
                                    <option value="{{ $system->id }}">{{ $system->name }}</option>
                                @endforeach
                            </select>
                            @error('related_process')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="version" class="form-label">{{ __('Version') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('version') is-invalid @enderror" id="version" wire:model.lazy="version">
                            @error('version')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Template selection section -->
                @if($showTemplateSection)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="template_id" class="form-label">{{ __('Template') }}</label>
                            <select class="form-select @error('template_id') is-invalid @enderror" id="template_id" wire:model.lazy="template_id">
                                <option value="">{{ __('Select Template') }}</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name ?? $template->title }}</option>
                                @endforeach
                            </select>
                            @error('template_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- For procedure type, add procedure data field -->
                @if($document_type === 'procedure' && $template_id)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="procedure_setup_data" class="form-label">{{ __('Procedure Setup Data (JSON)') }}</label>
                            <textarea class="form-control @error('procedure_setup_data') is-invalid @enderror" id="procedure_setup_data" wire:model.lazy="procedure_setup_data" rows="5"></textarea>
                            @error('procedure_setup_data')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif
                @endif

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="content" class="form-label">{{ __('Document Content') }}</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" wire:model.lazy="content" rows="5"></textarea>
                            <small class="form-text text-muted">{{ __('Enter the document content or upload a file below') }}</small>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="file" class="form-label">{{ __('Upload Document File') }}</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" wire:model="file">
                            <div wire:loading wire:target="file">{{ __('Uploading...') }}</div>
                            <small class="form-text text-muted">{{ __('Accepted formats: PDF, Word, Excel, PowerPoint (max 10MB)') }}</small>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('Create Document') }}</span>
                        <span wire:loading>{{ __('Processing...') }}</span>
                    </button>
                    <a href="{{ route('tenant.document.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
