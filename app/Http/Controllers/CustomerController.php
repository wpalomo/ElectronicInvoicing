<?php

namespace ElectronicInvoicing\Http\Controllers;

use ElectronicInvoicing\{Company, Customer, IdentificationType, User};
use ElectronicInvoicing\Http\Requests\StoreCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class CustomerController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->hasPermissionTo('delete_hard_customers')) {
            $customers = Customer::withTrashed()->get()->sortBy(['social_reason']);
        } else {
            $customers = Customer::all()->sortBy(['social_reason']);
        }
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $companies = Company::all();
        } else {
            $companies = CompanyUser::getCompaniesAllowedToUser($user);
        }
        $identificationTypes = IdentificationType::all();
        return view('customers.create', compact(['companies', 'identificationTypes']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        if (Customer::where('identification', '=', $request->identification)->exists()) {
            $customer = Customer::where('identification', '=', $request->identification)->first();
            Validator::make($request->all(), [
                'identification' => 'uniquecustomer:company_customers,company_id,' . $request->company . ',customer_id,' . $customer->id
            ], array('uniquecustomer' => 'The :attribute has already been taken.'))->validate();
        }
        $input = $request->except(['company', 'identification_type']);
        $input['identification_type_id'] = $request->identification_type;
        $customer = Customer::create($input);
        $customer->companies()->save(Company::where('id', $request->company)->first());
        if (!User::where('email', '=', $request->email)->exists()) {
            $input['name'] = $request->social_reason;
            $input['email'] = $request->email;
            $input['password'] = Hash::make($request->identification);
            $user = User::create($input);
            $user->assignRole('customer');
        } else {
            $user = User::where('email', '=', $request->email)->first();
        }
        foreach (Customer::where('identification', '=', $request->identification)->get() as $customer) {
            if (!$user->customers()->where('id', $customer->id)->exists()) {
                $user->customers()->save($customer);
            }
        }
        return redirect()->route('customers.index')->with(['status' => 'Customer added successfully. Remember that for the login, the customer must enter the first email provided and the identification as password.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \ElectronicInvoicing\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \ElectronicInvoicing\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \ElectronicInvoicing\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        $customer->fill($request->except(['ruc', 'company', 'identification_type_name', 'identification_type', 'identification']))->save();
        return redirect()->route('customers.index')->with(['status' => 'Customer updated successfully.']);
    }

    /**
     * Deactivate the specified resource.
     *
     * @param  \ElectronicInvoicing\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function delete(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with(['status' => 'Customer deactivated successfully.']);
    }

    /**
     * Restore the specified resource.
     *
     * @param  $customer
     * @return \Illuminate\Http\Response
     */
    public function restore($customer)
    {
        Customer::withTrashed()->where('id', $customer)->restore();
        return redirect()->route('customers.index')->with(['status' => 'Customer activated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \ElectronicInvoicing\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($customer)
    {
        $customerOld = Customer::withTrashed()->where('id', $customer)->first();
        $customerOld->forceDelete();
        return redirect()->route('customers.index')->with(['status' => 'Customer deleted successfully.']);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function customers(Request $request) {
        if (is_string($request->id)) {
            $customer = Customer::where('id', $request->id)->with('identificationType')->get();
            return $customer->toJson();
        }
    }
}