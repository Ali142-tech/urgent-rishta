<?php

namespace App\Http\Controllers;

use App\MasterData;
use App\OnlinePackage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    /**
     * Show package details (online or premium). Fetches from online_packages first, then masterdata.
     */
    public function showPackageDetails($id)
    {
        $package = OnlinePackage::find($id);
        if (!$package) {
            $package = MasterData::find($id);
        }

        if (!$package) {
            abort(404, 'Package not found');
        }

        $userHasActiveOnlinePackage = false;
        $userOnlineExpiresAtFormatted = null;
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasActiveOnlinePackage()) {
                $userHasActiveOnlinePackage = true;
                $exp = $user->online_package_expires_at;
                $userOnlineExpiresAtFormatted = $exp ? Carbon::parse($exp)->format('j M Y') : null;
            }
        }

        return view('package-details', compact('package', 'userHasActiveOnlinePackage', 'userOnlineExpiresAtFormatted'));
    }
}
