<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Yoeunes\Toastr\Facades\Toastr;

class GeneralController extends Controller
{
    // clear cache
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Toastr::success(__('messages.cache_cleared'), __('status.success'));
        return redirect()->back();
    }
}
