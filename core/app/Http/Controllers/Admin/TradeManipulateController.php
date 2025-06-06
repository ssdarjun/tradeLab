<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manipulation;
use Illuminate\Http\Request;
use App\Models\CryptoCurrency;

class TradeManipulateController extends Controller
{
    /**
     * Display the manipulation list page.
     */
    public function index()
    {
        $pageTitle = "Trade Manipulate";
        $cryptos   = CryptoCurrency::orderBy('rank', 'ASC')->get();
        $games     = Manipulation::latest('id')->with('crypto')->paginate(getPaginate());
        return view('admin.manipulate.index', compact('games', 'cryptos', 'pageTitle'));
    }

    /**
     * Store or update a manipulation.
     */
    public function save(Request $request, $id = 0)
    {
        $now = now()->format('Y-m-d H:i');
        $request->validate([
            'crypto_id'           => 'required|exists:crypto_currencies,id',
            'start_time'          => 'required|date|after_or_equal:' . $now,
            'end_time'            => 'required|date|after_or_equal:start_time',
            'prediction_override' => 'required|in:1,2',
            'min'                 => 'required|numeric',
            'max'                 => 'required|numeric|gte:min', // max >= min
        ]);

        if ($id) {
            $manipulation = Manipulation::findOrFail($id);
            $message      = "Updated successfully";
        } else {
            $manipulation = new Manipulation();
            $message      = "Created successfully";
        }

        $manipulation->crypto_id           = $request->crypto_id;
        $manipulation->start_time          = $request->start_time;
        $manipulation->end_time            = $request->end_time;
        $manipulation->prediction_override = $request->prediction_override;
        $manipulation->min                 = $request->min;
        $manipulation->max                 = $request->max;
        $manipulation->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    /**
     * Delete a manipulation record.
     */
    public function delete($id)
    {
        $manipulation = Manipulation::findOrFail($id);
        $manipulation->delete();

        $notify[] = ['success', 'Deleted successfully'];
        return back()->withNotify($notify);
    }
}
