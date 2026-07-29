<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::query()
            ->withCount('orders')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(fn ($q) => $q
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request): CustomerResource
    {
        $this->authorize('create', Customer::class);

        return new CustomerResource(Customer::create($request->validated()));
    }

    public function show(Customer $customer): CustomerResource
    {
        $this->authorize('view', $customer);

        return new CustomerResource($customer->loadCount('orders'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $this->authorize('update', $customer);

        $customer->update($request->validated());

        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $this->authorize('delete', $customer);

        if ($customer->orders()->exists()) {
            return response()->json([
                'message' => __('This customer has orders and cannot be deleted.'),
            ], 409);
        }

        $customer->delete();

        return response()->json(null, 204);
    }
}
