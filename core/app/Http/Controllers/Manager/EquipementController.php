<?php

namespace App\Http\Controllers\Manager;

use App\Models\Section;
use App\Models\Localite;
use App\Models\Equipement;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EquipementController extends Controller
{
    public function index($id)
    {
        $pageTitle = "Gestion des materiels agricoles";
        $manager = auth()->user();
        $id = decrypt($id);
        $equipements = Equipement::all()->where('producteur_id', $id);
        return view('manager.equipement.index', compact('pageTitle', 'equipements', 'id'));
        $validationRule = [
            'section' => 'required',
            'localite' => 'required',
            'producteur_id' => 'required',
            'anneeCreation' => 'required',
            'ageMoyenCacao' => 'required',
            'parcelleRegenerer' => 'required',
            'anneeRegenerer' => 'required_if:parcelleRegenerer,==,oui',
            'superficieConcerne' => 'required_if:parcelleRegenerer,==,oui',
            'typeDoc' => 'required',
            'presenceCourDeau' => 'required',
            'courDeau' => 'required_if:presenceCourDeau,==,oui',
            'autreCourDeau' => 'required_if:courDeau,==,Autre',
            'existeMesureProtection' => 'required',
            'existePente' => 'required',
            'superficie' => 'required',
            'nbCacaoParHectare' => 'required|numeric',
            'erosion' => 'required',
            'items.*.arbre'     => 'required|integer',
            'items.*.nombre'     => 'required|integer',
            'longitude' => 'numeric|nullable',
            'latitude' => 'numeric|nullable',
            'typedeclaration' => 'required',
        ];
    }
    public function create($id){
        $pageTitle = "Ajouter un équipement matériel";
        $manager   = auth()->user();
        // $sections = Section::where('cooperative_id', $manager->cooperative_id)->get();
        // $localites = Localite::active()->with('section')->get();
        $id = decrypt($id); 
        return view('manager.equipement.create', compact('pageTitle', 'id'));
    }
    public function store(){

    }
}
