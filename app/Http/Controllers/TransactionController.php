namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->get();
        return view('event.tabs.transaksi', compact('transactions'));
    }

    public function create()
    {
        return view('event.tabs.tambahtransaksi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pembelian' => 'required|string|max:255',
            'qty' => 'required|integer',
            'harga' => 'required|numeric',
            'struk' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama_pembelian', 'qty', 'harga']);

        if ($request->hasFile('struk')) {
            $data['struk'] = $request->file('struk')->store('struk', 'public');
        }

        Transaction::create($data);

        // ✅ Redirect ke halaman transaksi (bukan API)
        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }
}
