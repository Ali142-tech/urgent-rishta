<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

use App\MasterData;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function renderModal($title, $body, $buttons) {
        return view("layouts.modal", ['title' => $title, 'body' => $body, 'buttons' => $buttons])->render();
    }

    public function states($country) {
        if (request()->ajax()) {
            return [
                'code' => '200',
                'options' => MasterData::where(['type' => 'STATE', 'subtype'=>$country])->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get()
            ];
        } else return [
            'code' => '200'
        ];
    }

    public function cities($subtype, $isCountry) {
        if (request()->ajax()) {
            return [
                'code' => '200',
                'options' => $isCountry ?
                    MasterData::where('type', 'CITY')->whereIn('subtype', MasterData::where(['type' => 'STATE', 'subtype'=>$subtype])->select('dataid')->get())->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get()
                    : MasterData::where(['type' => 'CITY', 'subtype'=>$subtype])->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get()
            ];
        } else return [
            'code' => '200'
        ];
    }

    /**
     * Community/caste options for a given religion — same subtype-as-parent
     * convention as states()/cities() above (CASTE.subtype = RELIGION.dataid).
     * Castes with no subtype at all (currently just "Other", dataid 369)
     * are universal fallbacks shown for every religion, so the list is
     * never empty even for a religion with no dedicated entries yet.
     */
    public function castes($religion) {
        if (request()->ajax()) {
            return [
                'code' => '200',
                // "Other" (and any future no-subtype fallback) always sorts
                // last, regardless of where it'd otherwise land alphabetically.
                'options' => MasterData::where('type', 'CASTE')
                    ->where(function ($q) use ($religion) {
                        $q->where('subtype', $religion)->orWhereNull('subtype')->orWhere('subtype', '');
                    })
                    ->orderByRaw("name = 'Other' ASC")
                    ->orderBy('order', 'DESC')->orderBy('name', 'ASC')->get()
            ];
        } else return [
            'code' => '200'
        ];
    }
}
