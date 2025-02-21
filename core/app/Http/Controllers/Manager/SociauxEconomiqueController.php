<?php

namespace App\Http\Controllers\Manager;

use App\Models\Campagne;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use App\Models\Agroespecesarbre;
use App\Http\Controllers\Controller;

class SociauxEconomiqueController extends Controller
{
    public function index($id)
    {
        $pageTitle = "Gestion Socio-Economique";
        $manager = auth()->user();
        $id = decrypt($id);
        // $equipements = Equipement::all()->where('producteur_id', $id);
        return view('manager.economique.index', compact('pageTitle', 'id'));
        
    }
    public function create($id){
        $pageTitle = "Enregistrement de la situation Socio-economique du producteur";
        $manager   = auth()->user();
        $id = decrypt($id); 
        $producteurs  = Producteur::joinRelationship('localite.section')
        ->where([['cooperative_id', $manager->cooperative_id],['producteurs.status', 1]])->with('localite')->get();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $campagnes = Campagne::active()->pluck('nom', 'id');
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $arbres = Agroespecesarbre::all();
        return view('manager.economique.create', compact('pageTitle', 'id','producteurs','sections','localites','cooperative','campagnes','arbres'));
    }
    public function store(){

    }
}
