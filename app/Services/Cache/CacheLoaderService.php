<?php
namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use App\Models\Procedure;
use App\Models\IsoReference;

class CacheLoaderService
{
    protected $procedureCacheKey = 'ProcedureDictionary';
    protected $isoSystemReferenceKey = 'IsoSystemReferenceDictionary';
    
    public function getOriginalProcedures()
    {
        return Cache::remember($this->procedureCacheKey, now()->hours(24), function () {
            return Procedure::on('iso_dic')->where('category_id', '1')->with('isoSystems')->get();
        });
    }

    public function clearOriginalProceduresCache()
    {
        Cache::forget($this->procedureCacheKey);
    }

    public function refreshOriginalProcedures()
    {
        $this->clearOriginalProceduresCache();
        return $this->getOriginalProcedures();
    }

    public function getIsoSystemReference()
    {
        return Cache::remember($this->isoSystemReferenceKey, now()->minutes(1), function () {
            return IsoReference::on('iso_dic')->with('isoSystems')->whereHas('isoSystems', function ($query) {
                $query->where('iso_system_id', currentISOSystem());
            })->get();
        });
    }

    public function clearIsoSystemReferenceCache()
    {
        Cache::forget($this->isoSystemReferenceKey);
    }

    public function refreshIsoSystemReference()
    {
        $this->clearIsoSystemReferenceCache();
        return $this->getIsoSystemReference();
    }
}
