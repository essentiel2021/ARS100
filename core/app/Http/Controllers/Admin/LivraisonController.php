<?php

namespace App\Http\Controllers\Admin;
use App\Constants\Status;
use App\Models\LivraisonInfo;
use App\Http\Controllers\Controller;
use App\Models\Cooperative;
use App\Models\LivraisonPayment;
use Illuminate\Support\Facades\DB;

class LivraisonController extends Controller
{

    public function livraisonInfo()
    {
        $pageTitle    = "Information de Livraison";
        $livraisonInfos = LivraisonInfo::dateFilter()->searchable(['code'])->filter(['status','receiver_cooperative_id','sender_cooperative_id'])->where(function ($q) {
            $q->OrWhereHas('payment', function ($myQuery) {
                if(request()->payment_status != null){
                    $myQuery->where('status',request()->payment_status);
                }
            });
        })->orderBy('id', 'DESC')->with('senderCooperative', 'receiverCooperative', 'senderStaff', 'receiverStaff', 'paymentInfo','magasinSection')->paginate(getPaginate());
        return view('admin.livraison.index', compact('pageTitle', 'livraisonInfos'));
    }

    public function livraisonDetail($id)
    {
        $livraisonInfo = LivraisonInfo::with('senderCooperative', 'receiverCooperative', 'senderStaff', 'receiverStaff', 'paymentInfo','magasinSection')->findOrFail($id);
        $pageTitle   = "Detail de Livraison: " . $livraisonInfo->code;
        return view('admin.livraison.details', compact('pageTitle', 'livraisonInfo'));
    }

    public function invoice($id)
    {
        $livraisonInfo = LivraisonInfo::with('senderCooperative', 'receiverCooperative', 'senderStaff', 'receiverStaff', 'paymentInfo','magasinSection')->findOrFail($id);
        $pageTitle   = "Facture";
        return view('admin.livraison.invoice', compact('pageTitle', 'livraisonInfo'));
    }

    public function cooperativeIncome()
    {
        $pageTitle     = "Cooperative Historique des Revenus";
        $cooperatives      = Cooperative::active()->latest('id')->get();
        $cooperativeIncomes = LivraisonPayment::where('cooperative_id','!=',0)->dateFilter()->filter(['cooperative_id'])->where('status', Status::PAYE)->select(DB::raw("*,SUM(final_amount) as totalAmount"))
            ->groupBy('cooperative_id')->with('cooperative')->paginate(getPaginate());
        return view('admin.livraison.income', compact('pageTitle', 'cooperativeIncomes', 'cooperatives'));
    }
}
