<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::where('user_id', Auth::id());
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  ->orWhere('country', 'like', "%$search%")
                  ->orWhere('city', 'like', "%$search%") ;
            });
        }
        $clients = $query->paginate(10);
        $clientsCount = Client::where('user_id', Auth::id())->count();
        $invoicesCount = \App\Models\Invoice::where('user_id', Auth::id())->count();
        return view('dashboard.clients-list', compact('clients', 'clientsCount', 'invoicesCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.create-client');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
        ]);
        $data['user_id'] = Auth::id();
        Client::create($data);
        return redirect()->route('clients.index')->with('success', 'تم إضافة العميل بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $client = Client::where('user_id', Auth::id())->findOrFail($id);
        return view('dashboard.edit-client', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $client = Client::where('user_id', Auth::id())->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
        ]);
        $client->update($data);
        return redirect()->route('clients.index')->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $client = Client::where('user_id', Auth::id())->findOrFail($id);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'تم حذف العميل بنجاح');
    }

    public function autocomplete(Request $request)
    {
        $search = $request->input('q');
        $userId = Auth::id();
        $results = [];
        if ($search) {
            $results = \App\Models\Client::where('user_id', $userId)
                ->where('name', 'like', "%$search%")
                ->limit(20)
                ->get(['id', 'name']);
        }
        return response()->json($results);
    }

    /**
     * جلب فواتير العميل مقسمة حسب الحالة
     */
    public function getInvoices($clientId)
    {
        $userId = Auth::id();

        // التحقق من أن العميل يتبع للمستخدم الحالي
        $client = Client::where('user_id', $userId)->findOrFail($clientId);

        // جلب الفواتير المقبوضة
        $receivedInvoices = \App\Models\Invoice::where('user_id', $userId)
            ->where('client_id', $clientId)
            ->where('status', 'مقبوضة')
            ->select('id', 'invoice_number', 'type', 'total', 'date', 'status')
            ->orderBy('date', 'desc')
            ->get();

        // جلب الفواتير غير المقبوضة
        $unreceivedInvoices = \App\Models\Invoice::where('user_id', $userId)
            ->where('client_id', $clientId)
            ->where('status', 'غير مقبوضة')
            ->select('id', 'invoice_number', 'type', 'total', 'date', 'status')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'received' => $receivedInvoices,
            'unreceived' => $unreceivedInvoices
        ]);
    }
}
