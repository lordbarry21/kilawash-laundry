<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $services = Service::orderBy('category')->orderBy('price_per_unit')->get();

        return view('customer.index', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name'   => 'required|string|max:255',
            'whatsapp_number' => ['required', 'regex:/^\+?[0-9]{8,15}$/'],
            'items'           => 'required|array',
            'items.*'         => 'nullable|integer|min:0|max:2147483647',
        ]);

        $items = array_filter($data['items'], fn ($quantity) => $quantity > 0);

        if (empty($items)) {
            return back()->withInput()->with('error', 'Pilih minimal satu layanan!');
        }

        DB::transaction(function () use ($data, $items) {
            $services = Service::whereKey(array_keys($items))->sharedLock()->get()->keyBy('id');

            if (array_diff_key($items, $services->all())) {
                throw ValidationException::withMessages(['items' => 'Ada layanan yang sudah tidak tersedia.']);
            }

            $order = Order::create([
                'customer_name'   => $data['customer_name'],
                'whatsapp_number' => $data['whatsapp_number'],
                'total_price'     => 0,
                'status'          => 'Antrean',
            ]);

            $total = 0;

            foreach ($items as $serviceId => $quantity) {
                $subtotal = $services[$serviceId]->price_per_unit * $quantity;
                $total += $subtotal;

                if ($total > 2_147_483_647) {
                    throw ValidationException::withMessages(['items' => 'Jumlah pesanan terlalu besar.']);
                }

                $order->orderDetails()->create([
                    'service_id'    => $serviceId,
                    'qty_or_weight' => $quantity,
                    'subtotal'      => $subtotal,
                ]);
            }

            $order->update(['total_price' => $total]);
        });

        return redirect()->route('customer.index')->with('success', 'Pesanan berhasil dibuat dan masuk antrean.');
    }

    public function adminDashboard(Request $request): View
    {
        $statusFilter = $request->query('status', '');
        $search       = trim((string) $request->query('q', ''));

        $query = Order::with('orderDetails.service')->latest();

        if (in_array($statusFilter, ['Antrean', 'Dicuci', 'Selesai'], true)) {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        $orders = $query->get();

        $allOrders = Order::all();
        $stats = [
            'total_orders'      => $allOrders->count(),
            'active_orders'     => $allOrders->whereIn('status', ['Antrean', 'Dicuci'])->count(),
            'completed_orders'  => $allOrders->where('status', 'Selesai')->count(),
            'total_revenue'     => $allOrders->sum('total_price'),
            'completed_revenue' => $allOrders->where('status', 'Selesai')->sum('total_price'),
        ];

        $services = Service::withCount('orderDetails')->orderBy('price_per_unit')->get();

        return view('dashboard', compact('orders', 'stats', 'services', 'statusFilter', 'search'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:Antrean,Dicuci,Selesai']);
        $order->update($data);

        return back()->with('success', 'Status pesanan #' . $order->id . ' berhasil diperbarui menjadi ' . $data['status'] . '!');
    }

    public function receipt(Order $order): View
    {
        $order->load('orderDetails.service');

        return view('admin.orders.receipt', compact('order'));
    }

    public function report(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $from   = (string) $request->query('from', '');
        $to     = (string) $request->query('to', '');

        $orderQuery = Order::with('orderDetails.service')->latest();

        if (in_array($status, ['Antrean', 'Dicuci', 'Selesai'], true)) {
            $orderQuery->where('status', $status);
        }
        if ($from !== '') {
            $orderQuery->whereDate('created_at', '>=', $from);
        }
        if ($to !== '') {
            $orderQuery->whereDate('created_at', '<=', $to);
        }

        $orders = $orderQuery->get();

        $orderIds = $orders->pluck('id');
        $serviceBreakdown = OrderDetail::query()
            ->join('services', 'order_details.service_id', '=', 'services.id')
            ->whereIn('order_details.order_id', $orderIds)
            ->select(
                'services.service_name',
                'services.category',
                'services.unit_type',
                'services.price_per_unit',
                DB::raw('COUNT(order_details.id) as jumlah_item'),
                DB::raw('SUM(order_details.qty_or_weight) as total_qty'),
                DB::raw('SUM(order_details.subtotal) as total_omzet')
            )
            ->groupBy('services.id', 'services.service_name', 'services.category', 'services.unit_type', 'services.price_per_unit')
            ->orderByDesc('total_omzet')
            ->get();

        $summary = [
            'total_orders'      => $orders->count(),
            'total_revenue'     => $orders->sum('total_price'),
            'completed_revenue' => $orders->where('status', 'Selesai')->sum('total_price'),
            'completed_count'   => $orders->where('status', 'Selesai')->count(),
        ];

        return view('admin.report', compact('orders', 'serviceBreakdown', 'summary', 'status', 'from', 'to'));
    }
}
