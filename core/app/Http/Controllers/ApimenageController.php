<?php

namespace App\Http\Controllers;

use App\Models\Menage;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\Producteur;
use App\Models\DebugMobile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Menage_ordure;


use App\Exports\ExportMenages;
use Illuminate\Support\Facades\DB;
use App\Rules\Enfants0A5PasExtrait;
use App\Rules\Enfants6A17Scolarise;
use App\Http\Controllers\Controller;
use App\Models\Menage_sourceEnergie;
use App\Rules\Enfants6A17PasExtrait;
use App\Rules\NbreEnft6A17Scolarise;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreMenageRequest;

class ApimenageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $debug = new DebugMobile();
        $debug->content = json_encode($request->all());
        $debug->save();
        
        if ($request->id != null) {
            $menage = Menage::find($request->id);
            
        } else {
            $menage = new Menage();
           
        }
        // if ($menage->producteur_id != $request->producteur_id) {
        //     $hasMenage = Menage::where('producteur_id', $request->producteur_id)->exists();
        //     if ($hasMenage) {
        //         return response()->json("Ce producteur a déjà un menage enregistré", 501);
        //     }
        // }
       
        $menage->producteur_id  = $request->producteur_id;
        $menage->nom = $request->nom;
        $menage->prenoms = $request->prenoms;
        $menage->sexe = $request->sexe;
        $menage->statutMatrimonial = $request->statutMatrimonial;
        $menage->autre_lien_parente = $request->autre_lien_parente;
        $menage->dateNaiss = $request->dateNaiss;
        $menage->phone1 = $request->phone1;
        $menage->niveau_etude = $request->niveau_etude;
        $menage->autre_instruction = $request->autre_instruction;
        $menage->autre_instruction = $request->statut_scolaire;
        $menage->categorie_ethnique    = $request->categorie_ethnique;
        //dd(json_encode($request->all()));

        $menage->save();
        // if ($menage != null) {
        //     $id = $menage->id;
        //     $datas  = $data2 = [];
        //     if (($request->sourcesEnergie != null)) {
        //         Menage_sourceEnergie::where('menage_id', $id)->delete();
        //         $i = 0;
        //         foreach ($request->sourcesEnergie as $sourceEnergie) {
        //             if (!empty($sourceEnergie)) {
        //                 $datas[] = [
        //                     'menage_id' => $id,
        //                     'source_energie' => $sourceEnergie,
        //                 ];
        //             }

        //             $i++;
        //         }
        //     }
        //     if (($request->ordureMenagere != null)) {
        //         Menage_ordure::where('menage_id', $id)->delete();
        //         foreach ($request->ordureMenagere as $data) {
        //             //dd($ordureMenagere);

        //             $data2[] = [
        //                 'menage_id' => $id,
        //                 'ordure_menagere' => $data,
        //             ];
        //         }
        //     }

        //     Menage_sourceEnergie::insert($datas);
        //     Menage_ordure::insert($data2);
        // }

        // if ($menage == null) {
        //     return response()->json("Le ménage n'a pas été enregistré", 501);
        // }

        return response()->json($menage, 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        //
    }
}
