<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->get();
        $userAddress = $user->userAddress;

        return view('pages.site.profile.profile', [
            'user' => $user,
            'orders' => $orders,
            'userAddress' => $userAddress,
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($request->user()),
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route('profile.index') . '#edit-profile')
                ->withErrors($validator)
                ->withInput();
        }

        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->to(route('profile.index') . '#edit-profile')->with('success', 'Profile updated successfully');
    }

    public function updateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route('profile.index') . '#edit-profile')
                ->withErrors($validator)
                ->withInput();
        }
        
        UserAddress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
            ], 
            [
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state ?? null,
                'zip' => $request->zip,
                'country' => $request->country,
                'phone' => $request->phone ?? null,
            ]);

        return redirect()->to(route('profile.index') . '#edit-profile')->with('success', 'Address updated successfully');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'current_password'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route('profile.index') . '#edit-profile')
                ->withErrors($validator, 'userDeletion')
                ->withInput();
        }

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}