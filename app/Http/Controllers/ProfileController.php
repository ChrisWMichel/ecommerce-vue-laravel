<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Enums\AddressType;
use Illuminate\Http\Request;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\CountryService;

class ProfileController extends Controller
{
    public function view(Request $request){

        /** @var \App\Models\User $user */
        $user = $request->user();

        /** @var \App\Models\Customer $customer */
        $customer = $user->customer;

        if (!$customer) {
            $customer = \App\Models\Customer::create([
                'user_id' => $user->id,
                'first_name' => $user->name ?? '',
                'last_name' => '',
                'phone' => '',
                'status' => 'active',
            ]);
            
            // Refresh user to load the new customer relationship
            $user->refresh();
        }
    
    $shippingAddress = $customer->shippingAddress ?: new CustomerAddress(['type' => AddressType::Shipping]);
    $billingAddress = $customer->billingAddress ?: new CustomerAddress(['type' => AddressType::Billing]);
        
        /** @var \App\Services\CountryService $countryService */
    $countryService = app(CountryService::class);
    $countries = $countryService->getCountries();

        return view('profile.view', compact('user', 'customer', 'shippingAddress', 'billingAddress', 'countries'));
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the customer's profile information.
     */
    public function updateCustomer(ProfileRequest $request): RedirectResponse
    {
             

        $customerData = $request->validated();
        $shippingData = $customerData['shipping'];
        $billingData = $customerData['billing'];

        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Customer $customer */
        $customer = $user->customer;

        DB::beginTransaction();
        try {
            $customer->update($customerData);

            if ($customer->shippingAddress) {
                $customer->shippingAddress->update($shippingData);
            } else {
                $shippingData['customer_id'] = $customer->user_id;
                $shippingData['type'] = AddressType::Shipping->value;
                CustomerAddress::create($shippingData);
            }
            if ($customer->billingAddress) {
                $customer->billingAddress->update($billingData);
            } else {
                $billingData['customer_id'] = $customer->user_id;
                $billingData['type'] = AddressType::Billing->value;
                CustomerAddress::create($billingData);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::critical(__METHOD__ . ' method does not work. '. $e->getMessage());
            throw $e;
        }

        DB::commit();

        //session()->flash('flash_message', 'Profile was successfully updated.');

        return redirect()->route('profile.view')->with('flash_message', 'Profile was successfully updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password),
        ]);

        return Redirect::route('profile.view')->with('flash_message', 'Password was successfully updated.');
    }
    /** Update user with verified email */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
