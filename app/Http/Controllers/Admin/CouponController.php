<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Services\CouponService;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index()
    {
        $coupons = $this->couponService->getAll();
        return view('admin.coupon.index', compact('coupons'));
    }

    // create
    public function create()
    {
        return view('admin.coupon.create');
    }

    public function store(CouponRequest $request)
    {
        $data = $request->validated();
        $coupon = $this->couponService->store($data);

        Toastr::success(__('messages.coupon_created_successfully'), __('status.success'));

        return redirect()->route('admin.coupons.index');
    }

    public function edit($id)
    {
        $coupon = $this->couponService->find($id);
        return view('admin.coupon.edit', compact('coupon'));
    }

    public function update(CouponRequest $request, $id)
    {
        $data = $request->validated();

        $this->couponService->update($id, $data);

        Toastr::success(__('messages.coupon_updated_successfully'), __('status.success'));

        return redirect()->route('admin.coupons.index');
    }

    public function destroy($id)
    {
        $this->couponService->delete($id);

        Toastr::success(__('messages.coupon_deleted_successfully'), __('status.success'));

        return redirect()->route('admin.coupons.index');
    }

    public function status(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $this->couponService->changeStatus($id, $data['status']);

        return redirect()->route('admin.coupons.index');
    }
}
