<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Procedure;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class SetupProcedureController extends Controller
{   
    public function all()
    {
        $pageTitle    = 'Setup Utility Bills';
        $utilityBills = Procedure::searchable(['name'])->with('form')->paginate(getPaginate());
        return view($this->iso_dic_path.'.procedures.index', compact('pageTitle', 'utilityBills'));
    }

    public function save(Request $request, $id = 0)
    {
        $imageValidation = $id ? 'nullable' : 'required';

        $request->validate([
            'name'           => 'required',
            'fixed_charge'   => 'required|numeric|min:0',
            'percent_charge' => 'required|numeric|min:0',
            'image'          => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $utility  = SetupUtilityBill::findOrFail($id);
            $notify[] = ['success', 'Utility bill setup updated successfully'];
        } else {
            $utility  = new SetupUtilityBill();
            $notify[] = ['success', 'Utility bill setup added successfully'];
        }

        if ($request->hasFile('image')) {
            try {
                $utility->image = fileUploader($request->image, getFilePath('setup_utility'), getFileSize('setup_utility'), $utility->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $utility->name           = $request->name;
        $utility->fixed_charge   = $request->fixed_charge;
        $utility->percent_charge = $request->percent_charge;
        $utility->save();

        return back()->withNotify($notify);
    }

    public function configure($id)
    {
        $utility   = SetupUtilityBill::findOrFail($id);
        $pageTitle = $utility->name.' - Configure Utility Bill';

        return view('admin.setup_utility_bill.form', compact('pageTitle', 'utility'));
    }

    public function saveConfigure($id)
    {
        $setup          = SetupUtilityBill::findOrFail($id);
        $formProcessor  = new FormProcessor();
        $generate       = $formProcessor->generate('setup_utility_bill_' . $setup->id, true);
        $setup->form_id = @$generate->id ?? 0;
        $setup->save();

        $notify[] = ['success', 'Utility bill setup configured successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return SetupUtilityBill::changeStatus($id);
    }
}
