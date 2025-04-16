<?php

namespace Modules\Report\Providers;

use App\Providers\BaseModuleServiceProvider;

class ReportServiceProvider extends BaseModuleServiceProvider
{
    protected $moduleName = 'Report';
    protected $moduleNamespace = 'Modules\Report\Http\Controllers';
}
