<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Transaction;
use Carbon\Carbon;


class EventDashboardController extends Controller
{
    public function index()
        {
        // return blade; the SPA will fetch data via AJAX
        return view('event.dashboard');
        }

        // API: dashboard summary
    public function apiDashboard()
        {
        $totalItems = Item::count();
        $totalIn = Transaction::where('type', 'in')->sum('qty');
        $totalOut = Transaction::where('type', 'out')->sum('qty');


        // low stock (threshold can be tuned)
        $lowStockItems = Item::where('current_stock', '<', 10)
        ->select('id','name','current_stock','min_stock_manual')
        ->get();


        // chart: daily out for last 14 days
        $from = Carbon::today()->subDays(13)->toDateString();
        $chartData = Transaction::selectRaw('DATE(created_at) as date, SUM(qty) as total')
        ->where('type','out')
        ->whereDate('created_at','>=',$from)
        ->groupBy('date')
        ->orderBy('date','asc')
        ->get();


        // recent purchases
        $recentPurchases = Transaction::with('item')
        ->where('type','in')
        ->orderBy('created_at','desc')
        ->take(6)
        ->get();


        return response()->json(compact('totalItems','totalIn','totalOut','lowStockItems','chartData','recentPurchases'));
        }

        // API: all items (for gudang table)
        // API: transactions by date (yyyy-mm-dd)
    public function apiTransactionsByDate($date)
        {
        try {
        $d = Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid date'], 422);
        }


        $transactions = Transaction::with('item')
        ->whereDate('created_at', $d)
        ->orderBy('created_at','asc')
        ->get();


        return response()->json($transactions);
        }


        // API: recommendations (heuristic A + turnover B)
    public function apiRecommendations()
        {
        $items = Item::with('transactions')->get();
        $recs = [];


        foreach ($items as $item) {
        $totalIn = $item->transactions->where('type','in')->sum('qty');
        $totalOut = $item->transactions->where('type','out')->sum('qty');


        // turnover rate guard
        $avgStock = max(1, ($totalIn + $item->current_stock) / 2);
        $turnover = $totalOut / $avgStock; // simple


        // heuristic reorder point
        $periodDays = 30; // period used to compute avg daily usage
        $avgDailyUsage = $periodDays ? ($totalOut / $periodDays) : 0;
        $leadTimeDays = 3; // default lead time
        $safetyStock = ceil(max(1, $avgDailyUsage) * 2); // simple safety
        $reorderPoint = ceil($leadTimeDays * $avgDailyUsage + $safetyStock);


        $shouldRecommend = false;
        $reason = [];


        if ($item->current_stock <= $reorderPoint) {
        $shouldRecommend = true;
        $reason[] = 'Stock at/below reorder point';
        }


        if ($turnover > 0.5) { // arbitrary threshold - fast moving
        $shouldRecommend = true;
        $reason[] = 'High turnover (fast-moving item)';
        }


        if ($shouldRecommend) {
        $recs[] = [
        'item_id' => $item->id,
        'name' => $item->name,
        'current_stock' => $item->current_stock,
        'reorder_point' => $reorderPoint,
        'turnover' => round($turnover,2),
        'reason' => implode(' & ', $reason),
        ];
        }
        }

        return response()->json($recs);
        }
    public function loadTab($name)
        {
            $allowed = ['dashboard', 'gudang', 'transaksi', 'laporan'];
            if (!in_array($name, $allowed)) {
                abort(404);
            }

            return view("event.tabs.$name");
        }

    public function getDashboardData()
        {
            // Barang minim stok (misal stok < 5)
            $minStock = \App\Models\Item::where('stock', '<', 5)->get(['id', 'name', 'stock']);

            // Riwayat transaksi terakhir (5 transaksi)
            $transactions = \App\Models\Transaction::latest()->take(5)->get(['created_at', 'item_id', 'price_per_unit']);

            // Data chart (ambil 7 hari terakhir)
            $chartData = \App\Models\Transaction::where('type', 'out')
                ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->take(7)
                ->get();

            // Rekomendasi otomatis
            $recommendations = [];
            foreach ($minStock as $item) {
                $recommendations[] = "Stok <b>{$item->name}</b> tinggal {$item->stock} pcs, segera restock!";
            }

            // Barang fast moving (threshold dummy)
            $fastMoving = \App\Models\Transaction::where('type', 'out')
                ->selectRaw('item_id, COUNT(*) as count')
                ->groupBy('item_id')
                ->orderBy('count', 'desc')
                ->take(3)
                ->get();

            foreach ($fastMoving as $move) {
                $itemName = \App\Models\Item::find($move->item_id)->name ?? 'Barang Tidak Diketahui';
                $recommendations[] = "Barang <b>{$itemName}</b> termasuk fast moving, pastikan stok aman.";
            }

            // Format data untuk chart
            $labels = $chartData->pluck('date');
            $values = $chartData->pluck('total');

            return response()->json([
                'min_stock' => $minStock,
                'transactions' => $transactions->map(function ($t) {
                    return [
                        'date' => $t->created_at->format('d M Y'),
                        'item' => optional($t->item)->name ?? 'N/A',
                        'price' => number_format($t->price_per_unit, 0, ',', '.')
                    ];
                }),
                'recommendations' => $recommendations,
                'chart' => [
                    'labels' => $labels,
                    'values' => $values
                ]
            ]);
        }

        // ======================================================
        // 📦 API GUDANG (CRUD BARANG)
        // ======================================================
    public function getItems()
    {
        $items = Item::select('id', 'name', 'stock', 'unit')->get();
        return response()->json($items);
    }

    public function storeItem(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'nullable|string|max:255',
                'stock' => 'required|integer|min:0',
                'unit' => 'nullable|string|max:50'
            ]);

            $item = \App\Models\Item::create($data);
            return response()->json(['success' => true, 'item' => $item]);
        }

    public function updateItem(Request $request, $id)
        {
            $item = \App\Models\Item::findOrFail($id);
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'nullable|string|max:255',
                'stock' => 'required|integer|min:0',
                'unit' => 'nullable|string|max:50'
            ]);
            $item->update($data);
            return response()->json(['success' => true]);
        }

    public function deleteItem($id)
        {
            $item = \App\Models\Item::findOrFail($id);
            $item->delete();
            return response()->json(['success' => true]);
        }

        // ======================================================
        // 💸 API TRANSAKSI (BARANG MASUK / KELUAR)
        // ======================================================
    public function getTransactions()
    {
        $transactions = Transaction::with('item:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json($transactions);
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:in,out,purchase',
            'qty' => 'required|integer|min:1',
            'price_per_unit' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'item_id' => $validated['item_id'],
            'type' => $validated['type'],
            'qty' => $validated['qty'],
            'price_per_unit' => $validated['price_per_unit'] ?? null,
            'note' => $validated['note'] ?? null,
            'user_id' => auth()->id(),
        ]);

        return response()->json($transaction, 201);
    }

        // ==============================
    // 🔹 API UNTUK TAB LAPORAN
    // ==============================
    public function getReport()
    {
        $summary = [
            'total_items' => Item::count(),
            'total_in' => Transaction::where('type', 'in')->sum('qty'),
            'total_out' => Transaction::where('type', 'out')->sum('qty'),
            'total_purchase' => Transaction::where('type', 'purchase')->sum('qty'),
            'recent' => Transaction::with('item:id,name')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
        ];

        return response()->json($summary);
    }




}