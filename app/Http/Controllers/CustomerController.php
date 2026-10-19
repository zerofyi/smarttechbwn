<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('mobile', 'like', "%{$search}%")
                             ->orWhere('address', 'like', "%{$search}%");
            })
            ->latest('customer_id')
            ->paginate(30);

        return view('distributor.customers', compact('customers', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        Customer::create($request->all());

        return back()->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::where('customer_id', $id)->firstOrFail();
        $customer->update($request->only('name', 'mobile', 'address'));

        return back()->with('success', 'Customer updated successfully.');
    }
}
