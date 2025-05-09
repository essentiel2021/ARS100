<?php

namespace App\Http\Controllers\Manager;

use App\Models\Menage;
use App\Models\Section;
use App\Models\Localite;
use App\Constants\Status;
use App\Models\Producteur;
use App\Models\Cooperative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ExportMenages;
use App\Http\Controllers\Controller;


class MenageController extends Controller
{

    public function index($id)
    {
        $pageTitle = "Gestion des ménages";
        $manager = auth()->user();
        // $cooperative = Cooperative::with('sections.localites')->find($manager->cooperative_id);
        // $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
        //     return $localite->active();
        // });
        // searchable([
        //     "quartier", "boisChauffe",
        //     "separationMenage", "eauxToillette", "eauxVaisselle", "wc",
        //     "menages.sources_eaux", "type_machines", "garde_machines", "equipements",
        //     "traitementChamps", "activiteFemme", "nomActiviteFemme", "champFemme", "nombreHectareFemme"
        // ])
        $producteurId = decrypt($id);
        $producteur = Producteur::where('id', $producteurId)->firstOrFail();
        // $cooperative = Cooperative::with('sections.localites')->find($manager->cooperative_id);
        // $sections = Section::where('cooperative_id', $manager->cooperative_id)->get();
        // $localites = Localite::joinRelationship('section')
        //     ->where('cooperative_id', $manager->cooperative_id)
        //     ->when(request()->section, function ($query, $section) {
        //         $query->where('section_id', $section);
        //     })
        //     ->get();
        // $producteurs = Producteur::joinRelationship('localite.section')->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])
        //     ->where('cooperative_id', $manager->cooperative_id)
        //     ->when(request()->localite, function ($query, $localite) {
        //         $query->where('localite_id', $localite);
        //     })
        //     ->get();

        // $menages = Menage::dateFilter()->latest('id')
        //     ->joinRelationship('producteur.localite.section')
        //     ->where('cooperative_id', $manager->cooperative_id)
           
        //     ->when(request()->section, function ($query, $section) {
        //         $query->where('localites.section_id', $section);
        //     })
        //     ->when(request()->localite, function ($query, $localite) {
        //         $query->where('producteurs.localite_id', $localite);
        //     })
        //     ->when(request()->producteur, function ($query, $producteur) {
        //         $query->where('producteur_id', $producteur);
        //     })
        //     ->with(['producteur.localite', 'producteur.localite.section']); 

        //     $menagesFiltre = $menages->get();
       // Charger les relations nécessaires pour éviter les requêtes N+1
        $menages = Menage::with('producteur')->where('producteur_id', $producteurId)->paginate(getPaginate());
        $producteurs = Producteur::with('localite')->joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])
            ->get();
        $localites = Localite::with('section')->joinRelationship('section')
            ->where('cooperative_id', $manager->cooperative_id)
            ->when(request()->section, function ($query, $section) {
                $query->where('section_id', $section);
            })
            ->get();

        return view('manager.menage.index', compact('pageTitle', 'menages', 'producteurs', 'localites'));
    }

    public function create()
    {
        $pageTitle = "Ajouter un ménage";
        $manager = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $sections = $cooperative->sections;
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->with('localite')->get();

        return view('manager.menage.create', compact('pageTitle', 'producteurs', 'sections', 'localites'));
    }

    public function store(Request $request)
    {
        $localite = Localite::where('id', $request->localite)->first();

        if ($localite->status == Status::NO) {
            $notify[] = ['error', 'Cette localité est désactivé'];
            return back()->withNotify($notify)->withInput();
        }

        if ($request->id) {
            $menage = Menage::findOrFail($request->id);
            $rules = [
                'producteur_id'    => 'required|exists:producteurs,id',

            ];
            $attributes = [
                'producteur' => 'Producteur',

            ];
            $messages = [];
            $this->validate($request, $rules, $messages, $attributes);
            $message = "Le menage a été mise à jour avec succès";
        } else {
            $menage = new Menage();
            $rules = [
                'producteur_id'    => 'required|exists:producteurs,id',
            ];
            $attributes = [
                'producteur' => 'Producteur',

            ];
            $messages = [
                'producteur.required' => 'Le champ producteur est obligatoire',
            ];
            $this->validate($request, $rules, $messages, $attributes);

            $message = "Le menage a été crée avec succès";
        }
    
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
        $menage->save();

        $notify[] = ['success', isset($message) ? $message : 'Le menage a été crée avec succès.'];
        //return back()->withNotify($notify);

        return redirect()->route('suivi.menage.index', ['id' =>encrypt($request->producteur_id)])->withNotify($notify);
    }


    public function edit($id)
    {
        $pageTitle = "Mise à jour de le menage";
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs  = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->with('localite')->get();
        $sections = $cooperative->sections;
        $menage   = Menage::findOrFail($id);
        return view('manager.menage.edit', compact('pageTitle', 'localites', 'menage', 'producteurs', 'sections'));
    }
    public function show($id)
    {
        $pageTitle = "Détails du menage";
        $manager   = auth()->user();
        $cooperative = Cooperative::with('sections.localites', 'sections.localites.section')->find($manager->cooperative_id);
        $localites = $cooperative->sections->flatMap->localites->filter(function ($localite) {
            return $localite->active();
        });
        $producteurs  = Producteur::joinRelationship('localite.section')
            ->where([['cooperative_id', $manager->cooperative_id], ['producteurs.status', 1]])->with('localite')->get();
        $sections = $cooperative->sections;
        $menage   = Menage::findOrFail($id);
        $ordures = $menage->menage_ordure->pluck('ordure_menagere')->toArray();
        $energies = $menage->menage_sourceEnergie->pluck('source_energie')->toArray();
        return view('manager.menage.show', compact('pageTitle', 'localites', 'menage', 'producteurs', 'sections', 'ordures', 'energies'));
    }

    public function status($id)
    {
        return Menage::changeStatus($id);
    }

    public function exportExcel()
    {
        return (new ExportMenages())->download('menages.xlsx');
    }

    public function delete($id)
    {
        Menage::where('id', decrypt($id))->delete();
        $notify[] = ['success', 'Le contenu supprimé avec succès'];
        return back()->withNotify($notify);
    }
}
