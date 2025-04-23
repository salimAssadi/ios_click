<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'setting::backup';
        $this->routePrefix = 'settings.backup';
        $this->moduleName = 'Setting';
    }

    public function index()
    {
        $backups = Storage::disk('backups')->files();
        return $this->view('index', compact('backups'));
    }

    public function create()
    {
        try {
            $process = new Process(['php', 'artisan', 'backup:run']);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \Exception('Backup failed: ' . $process->getErrorOutput());
            }

            return $this->success('Backup created successfully.');
        } catch (\Exception $e) {
            return $this->error('Backup failed: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = Storage::disk('backups')->path($filename);
        return response()->download($path);
    }

    public function delete($filename)
    {
        Storage::disk('backups')->delete($filename);
        return $this->success('Backup deleted successfully.');
    }
}
