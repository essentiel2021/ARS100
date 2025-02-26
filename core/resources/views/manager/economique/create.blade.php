@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.traca.producteur.economiquestore'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Informations sur le chef du ménage</h5>
                        </legend>

                        {{-- <div class="form-group row">
                            <?php echo Form::label(__('Campagne'), null, ['class' => 'col-sm-4 control-label required']); ?>
                            <div class="col-xs-12 col-sm-8">
                                <?php echo Form::select('campagne_id', $campagnes, null, ['class' => 'form-control campagnes', 'id' => 'campagnes', 'required' => 'required']); ?>
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Section')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="section" id="section" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" @selected(old('section'))>
                                            {{ $section->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Localite')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="localite" id="localite" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($localites as $localite)
                                        <option value="{{ $localite->id }}"
                                            data-chained="{{ $localite->section->id }}"@selected(old('localite'))>
                                            {{ $localite->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 control-label">@lang('Producteur')</label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" name="producteur" id="producteur" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($producteurs as $producteur)
                                        <option value="{{ $producteur->id }}"
                                            data-chained="{{ $producteur->localite->id }}"@selected(old('producteur'))>
                                            {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Informations sur les dépenses courantes
                                du foyer</h5>
                        </legend>
                        <div class="form-group row">
                            <?php echo Form::label(__('Quels sont les dépenses courantes du foyer ? '), null, ['class' => 'col-sm-12 control-label pt-3']); ?>
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="pesticidesAnneDerniere_area">
                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                        @lang('Dépenses')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        <label class="control-label">Nom</label>
                                                        <select name="pesticidesAnneDerniere[0][nom]"
                                                            id="pesticidesAnneDerniere-1" class="form-control">
                                                            <option value="">Selectionner une option</option>
                                                            <option value="Scolarité">Scolarité</option>
                                                            <option value="Nourriture">Nourriture</option>
                                                            <option value="Santé">Santé</option>
                                                            <option value="Electricité">Electricité</option>
                                                            <option value="Eau courante">Eau courante</option>
                                                            <option value="Funérailles">Funérailles</option>
                                                            <option value="Mariages">Mariages</option>
                                                            <option value="Baptême">Baptême</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        <label class="control-label">Périodicité</label>
                                                        <select class="form-control unite"
                                                            name="pesticidesAnneDerniere[0][unite]" id="unite-1">
                                                            <option value="">Selectionner une option</option>
                                                            <option value="Année">Année</option>
                                                            <option value="Mois">Mois</option>
                                                            <option value="2 Mois">2 Mois</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        <label class="control-label">Montant moyen/an</label>

                                                        <input type="number" name="pesticidesAnneDerniere[0][quantite]"
                                                            id="quantite-1" class="form-control quantite"
                                                            placeholder="Montant moyen/an">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Observation'), null, ['class' => '']) }}
                                                        <input type="texte" name="pesticidesAnneDerniere[0][frequence]"
                                                            id="frequence-1" class="form-control frequence"
                                                            placeholder="Observation">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                            <td colspan="3">
                                                <button id="addRowPesticidesAnneDerniere" type="button"
                                                    class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                        <div class="form-group row">
                            <?php echo Form::label(__('Quels sont les autres dépenses courantes'), null, ['class' => 'col-sm-12 control-label pt-3']); ?>

                            {{-- NPK   Compost   Biofertilisant/Bio stimulant Engrais organique préfabriqué --}}
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="intrantsAnneDerniere_area">
                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                        @lang('Autres dépenses')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        <label class="control-label">Nom</label>

                                                        <input type="text" name="intrantsAnneDerniere[0][nom]"
                                                            id="nom-1" class="form-control nom" placeholder="Nom">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        <label class="control-label">Périodicité</label>
                                                        <select class="form-control unite"
                                                            name="intrantsAnneDerniere[0][unite]" id="unite-1">
                                                            <option value="">Selectionner une option</option>
                                                            <option value="Année">Année</option>
                                                            <option value="Mois">Mois</option>
                                                            <option value="2 Mois">2 Mois</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        <label class="control-label">Montant Moyen/an</label>

                                                        <input type="number" name="intrantsAnneDerniere[0][quantite]"
                                                            id="quantite-1" class="form-control quantite"
                                                            placeholder="Montant Moyen/an">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Observation'), null, ['class' => '']) }}
                                                        <input type="text" name="intrantsAnneDerniere[0][frequence]"
                                                            id="frequence-1" class="form-control frequence"
                                                            placeholder="Observation">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                            <td colspan="3">
                                                <button id="addRowIntrantsAnneDerniere" type="button"
                                                    class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Informations sur le coût de main
                                d'oeuvre</h5>
                        </legend>
                        {{-- presence d'insecte  --}}
                        <div class="form-group row" id="presenceInsectesParasitesRavageurs">

                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="insectesParasites_area">

                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                        @lang('Main d\'oeuvre')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Statut de Main d\'oeuvre'), null, ['class' => 'control-label']) }}
                                                        <select name="insectesParasites[0][nom]" id="insectesParasites-1"
                                                            class="form-control">
                                                            <option value="">Selectionner une option</option>
                                                            <option value="Mo Permanent">Mo Permanent</option>
                                                            <option value="Mo Occasionnel">Mo Occasionnel</option>
                                                            <option value="Non Rémunérée">Non Rémunérée</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        <label>Type de travailleur</label>
                                                        <select class="form-control nombreinsectesParasites"
                                                            name="insectesParasites[0][nombreinsectesParasites]"
                                                            id="nombreinsectesParasites-1">
                                                            <option value="">Selectionne une option</option>
                                                            <option value="Particulier">Particulier</option>
                                                            <option value="Groupe de travail">Groupe de travail</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Nom/groupe de travail'), null, ['class' => 'control-label']) }}
                                                        <input type="text"
                                                            name="presenceAutreInsecte[0][nomTravailleur]"
                                                            id="nombreinsectesParasites-1"
                                                            class="form-control nombreinsectesParasites"
                                                            placeholder="Nom/groupe de travail">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        <label>Sexe</label>
                                                        <select class="form-control nombreinsectesParasites"
                                                            name="insectesParasites[0][sexe]"
                                                            id="nombreinsectesParasites-1">
                                                            <option value="">Selectionne une option</option>
                                                            <option value="M">M</option>
                                                            <option value="F">F</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Coût Annuel'), null, ['class' => 'control-label']) }}
                                                        <input type="number" name="insectesParasites[0][coutAnnuel]"
                                                            id="nombreinsectesParasites-1"
                                                            class="form-control autreInsecteNom"
                                                            placeholder="Coût Annuel">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Temps de travail'), null, ['class' => 'control-label']) }}
                                                        <input type="number" name="insectesParasites[0][temps]"
                                                            id="nombreinsectesParasites-1" class="form-control"
                                                            placeholder="Temps de travail">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>

                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                            <td colspan="3">
                                                <button id="addRowinsectesParasites" type="button"
                                                    class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        {{-- fin presence d'insecte --}}
                        {{-- <div class="form-group row" id="traite">

                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="traitement_area">

                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Traitement')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-group row">
                                                        <label class="control-label">Nom</label>
                                                        <select name="traitement[0][nom]" id="traitement-1"
                                                            class="form-control">
                                                            <option value="">Selectionner une option</option>
                                                            <option value="Herbicides">Herbicides</option>
                                                            <option value="Fongicides">Fongicides</option>
                                                            <option value="Compost">Compost</option>
                                                            <option value="Déchets animaux">Déchets animaux</option>
                                                            <option value="Fiente">Fiente</option>
                                                            <option value="Nematicide">Nematicide</option>
                                                            <option value="Insecticide">Insecticide</option>
                                                            <option value="Biofertilisant">Biofertilisant</option>
                                                            <option value="Engrais chimique">Engrais chimique</option>
                                                            <option value="Engrais foliaire">Engrais foliaire</option>
                                                            <option value="Bouse de vache">Bouse de vache</option>
                                                            <option value="NPK>">NPK</option>
                                                            <option value="Insecticide organique">Insecticide organique
                                                            </option>
                                                            <option value="Insecticide chimique">Insecticide chimique
                                                            </option>
                                                            <option value="Pesticides">Pesticides</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        <label class="control-label">Unité</label>
                                                        <select class="form-control unite" name="traitement[0][unite]"
                                                            id="unite-1">
                                                            <option value="">Selectionner une option</option>
                                                            <option value="Kg">Kg</option>
                                                            <option value="L">L</option>
                                                            <option value="g">g</option>
                                                            <option value="mL">mL</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        <label class="control-label">Quantité</label>

                                                        <input type="number" name="traitement[0][quantite]"
                                                            id="quantite-1" class="form-control quantite"
                                                            placeholder="Fréquence">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Type de contenant'), null, ['class' => '']) }}
                                                        <select class="form-control contenant"
                                                            name="traitement[0][contenant]" id="contenant-1">
                                                            <option value="">Selectionner une option</option>
                                                            <option value="Sac">Sac</option>
                                                            <option value="Sachet">Sachet</option>
                                                            <option value="Boîte">Boîte</option>
                                                            <option value="Pot">Pot</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Fréquence'), null, ['class' => '']) }}
                                                        <input type="number" name="traitement[0][frequence]"
                                                            id="frequence-1" class="form-control frequence"
                                                            placeholder="Fréquence">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                            <td colspan="3">
                                                <button id="addRowTraitement" type="button"
                                                    class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div> --}}
                    </div>

                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Informations sur la Production de cacao
                                des trois (3) dernières années </h5>
                        </legend>
                        {{-- autre insecte --}}
                        <div class="form-group row" id="presenceAutreInsecte">
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="presenceAutreInsecte_area">
                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                        @lang('Production de cacao ')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        <label>Année</label>
                                                        <select class="form-control" id="autreInsecteAnnée-1"
                                                            name="presenceAutreInsecte[0][autreInsecteAnnée]" required>
                                                            @foreach ($campagnes as $campagne)
                                                                <option value="{{ $campagne->id }}"
                                                                    data-price="{{ $campagne->prix_achat }}">
                                                                    {{ $campagne->nom }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        <label>Production Brute (KG)</label>
                                                        <input type="number"
                                                            name="presenceAutreInsecte[0][nombreAutreInsectesParasites]"
                                                            id="nombreAutreInsectesParasites-0"
                                                            placeholder="Production Brute (KG)">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Revenu Brute'), null, ['class' => 'control-label']) }}
                                                        <input type="number" name="presenceAutreInsecte[0][revenuBrute]"
                                                            id="revenuBrute-0" class="form-control revenuBrute"
                                                            placeholder="Revenu Brute">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Commentaire'), null, ['class' => 'control-label']) }}
                                                        <input type="text" name="presenceAutreInsecte[0][commentaire]"
                                                            id="autreInsecteNom-1" class="form-control autreInsecteNom"
                                                            placeholder="Commentaire">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>

                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                            <td colspan="3">
                                                <button id="addRowPresenceAutreInsecte" type="button"
                                                    class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        {{-- fin autre insecte --}}
                    </div>
                    <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Informations sur Sources de Revenus
                                autre que le cacao</h5>
                        </legend>
                        {{-- presenceAutreTypeInsecteAmi --}}
                        <div class="form-group row" id="autreInsectesAmis">

                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="insectesAmis_area">

                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                        @lang('Insectes amis')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-3">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Nom'), null, ['class' => '']) }}
                                                        <input type="text" name="insectesAmis[]"
                                                            placeholder="Saisissez le nom du revenu" id="insectesAmis-1"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Année'), null, ['class' => '']) }}
                                                        <input type="number" name="annee[]" placeholder="Année"
                                                            id="annee-1" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        <label>Production moyenne <br> annuelle</label>
                                                        <input type="number" name="production[]"
                                                            placeholder="Production moyenne annuelle" id="production-1"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Revenu Brute'), null, ['class' => '']) }}
                                                        <input type="number" name="revenu[]" placeholder="Revenu Brute"
                                                            id="revenu-1" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-2">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Commentaire'), null, ['class' => '']) }}
                                                        <input type="texte" name="commentaire[]"
                                                            placeholder="Commentaire" id="commentaire-1"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                            <td colspan="3">
                                                <button id="addRowinsectesAmis" type="button"
                                                    class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="fieldset-like">
                        <legend class="legend-center">
                            <h5 class="font-weight-bold text-decoration-underline">Evaluation de la faune sauvage dans la
                                parcelle</h5>
                        </legend>
                        <div class="form-group row">
                            <div class="col-xs-12 col-sm-12">
                                <table class="table table-striped table-bordered">
                                    <tbody id="animauxRencontres_area">

                                        <tr>
                                            <td class="row">
                                                <div class="col-xs-12 col-sm-12 bg-success">
                                                    <badge class="btn  btn-outline--warning h-45 btn-sm text-white">@lang('Animal')
                                                    </badge>
                                                </div>
                                                <div class="col-xs-12 col-sm-12">
                                                    <div class="form-group row">
                                                        {{ Form::label(__('Nom'), null, ['class' => '']) }}
                                                        <input type="text" name="animauxRencontres[]"
                                                            placeholder="..." id="animauxRencontres-1"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>

                                    </tbody>
                                    <tfoot style="background: #e3e3e3;">
                                        <tr>

                                            <td colspan="3">
                                                <button id="addRowanimauxRencontres" type="button"
                                                    class="btn btn-success btn-sm"><i class="fa fa-plus"></i></button>
                                            </td>
                                        <tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div> --}}
                    <hr class="panel-wide">

                    <div class="form-group row">
                        <button type="submit" class="btn btn--primary w-100 h-45"> @lang('Envoyer')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.suivi.parcelles.index') }}" />
@endpush
@push('style')
    <style type="text/css">
        /* Styles CSS personnalisés */
        .fieldset-like {
            border: 1px solid #ccc;
            /* Ajoutez une bordure */
            border-radius: 5px;
            /* Ajoutez des coins arrondis */
            padding: 10px;
            /* Ajoutez de l'espace intérieur */
            margin-bottom: 20px;
            /* Ajoutez une marge en bas */
            text-align: left;
            /* Alignez le contenu du div à gauche */
        }

        .legend-center {
            text-align: center;
            /* Centrez horizontalement la légende */
            margin-bottom: 10px;
            /* Ajoutez de l'espace sous la légende */
        }
    </style>
@endpush
@push('script')
    <script type="text/javascript">
        $(document).ready(function() {

            var agroforestiersCount = $("#agroforestiers_area tr").length + 1;
            $(document).on('click', '#addRowagroforestiers', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Arbre agro-forestier ' +
                    agroforestiersCount +
                    '</badge></div><div class="col-xs-12 col-sm-6"><div class="form-group"><label for="agroforestiers" class="">Type</label><input placeholder="Type arbre..." class="form-control" id="agroforestiers-' +
                    agroforestiersCount +
                    '" name="agroforestiers[]" type="text"></div></div><div class="col-xs-12 col-sm-6"><div class="form-group"><label for="nombreagroforestiers" class="">Nombre</label><input type="number" name="nombreagroforestiers[]" placeholder="Nombre d\'arbre" id="nombreagroforestiers-' +
                    agroforestiersCount +
                    '" class="form-control " min="1" value=""></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    agroforestiersCount +
                    '" class="removeRowagroforestiers btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';
                //---> End create table tr

                agroforestiersCount = parseInt(agroforestiersCount) + 1;
                $('#agroforestiers_area').append(html_table);

            });

            $(document).on('click', '.removeRowagroforestiers', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#agroforestiers_area tr").length) {
                    $(this).parents('tr').remove();
                    agroforestiersCount = parseInt(agroforestiersCount) - 1;
                }
            });

            //insectes amis
            var insectesAmisCount = $("#insectesAmis_area tr").length + 1;
            $(document).on('click', '#addRowinsectesAmis', function() {

                //---> Start create table tr
                var html_table = `
                        <tr>
                            <td class="row">
                                <div class="col-xs-12 col-sm-12 bg-success">
                                    <badge class="btn btn-outline--warning h-45 btn-sm text-white">
                                        Autre Revenu ${insectesAmisCount}
                                    </badge>
                                </div>

                                <div class="col-xs-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="insectesAmis" class="">Nom</label>
                                        <input placeholder="Saisissez le nom du revenu" class="form-control" id="insectesAmis-${insectesAmisCount}" name="insectesAmis[]" type="text">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-2">
                                    <div class="form-group">
                                        <label for="annee" class="">Année</label>
                                        <input class="form-control" placeholder="Saisissez l'année" type="number" name="annee[]" id="annee-${insectesAmisCount}">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-2">
                                    <div class="form-group">
                                        <label for="production" class="">Production moyenne <br> annuelle</label>
                                        <input class="form-control" placeholder="Production annuelle moyenne" type="number" name="production[]" id="production-${insectesAmisCount}">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-2">
                                    <div class="form-group">
                                        <label for="revenu" class="">Revenu Brut</label>
                                        <input class="form-control" placeholder="Revenu Brut" type="number" name="revenu[]" id="revenu-${insectesAmisCount}">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-2">
                                    <div class="form-group">
                                        <label for="commentaire" class="">Commentaire</label>
                                        <input class="form-control" placeholder="Commentaire" type="text" name="commentaire[]" id="commentaire-${insectesAmisCount}">
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-8">
                                    <button type="button" id="${insectesAmisCount}" class="removeRowinsectesAmis btn btn-danger btn-sm">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        `;

                //---> End create table tr

                insectesAmisCount = parseInt(insectesAmisCount) + 1;
                $('#insectesAmis_area').append(html_table);

            });

            $(document).on('click', '.removeRowinsectesAmis', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#insectesAmis_area tr").length) {
                    $(this).parents('tr').remove();
                    insectesAmisCount = parseInt(insectesAmisCount) - 1;
                }
            });
            //fin insectes amis

            var insectesParasitesCount = $("#insectesParasites_area tr").length;
            $(document).on('click', '#addRowinsectesParasites', function() {

                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Main d\'oeuvre ' +
                    insectesParasitesCount +
                    '</badge></div><div class="col-xs-12 col-sm-2"><div class="form-group"><label for="insectesParasites" class="">Statut de Main d\'oeuvre</label><select class="form-control" id="insectesParasites-' +
                    insectesParasitesCount +
                    '" name="insectesParasites[' + insectesParasitesCount +
                    '][nom]"><option value="">Selectionner une option</option><option value="Mo Permanent">Mo Permanent</option> <option value="Mo Occasionnel">Mo Occasionnel</option> <option value="Non rémunérée(famille)">Non rémunérée(famille)</option></select></div></div><div class="col-xs-12 col-sm-2"><div class="form-group"><label for="nombreinsectesParasites" class="">Type de Travailleur</label><select name="insectesParasites[' +
                    insectesParasitesCount +
                    '][nombreinsectesParasites]" class="form-control nombreinsectesParasites" id="nombreinsectesParasites-' +
                    insectesParasitesCount +
                    '" ><option value="">Selectionner une option</option><option value="Particulier">Particulier</option><option value="Groupe de travail">Groupe de travail</option></select></div></div><div class="col-xs-12 col-sm-2"><div class="form-group"><label for="" class="">Nom/groupe de travaille</label><input type="text" placeholder="Nom du travailleur/Groupe de travaille" class="form-control" id="insectesParasites-' +
                    insectesParasitesCount +
                    '" name="insectesParasites[' + insectesParasitesCount +
                    '][nomTravailleur]"></div></div> <div class="col-xs-12 col-sm-2"><div class="form-group"><label for="" class="">Sexe</label><select name="insectesParasites[' +
                    insectesParasitesCount +
                    '][sexe]" class="form-control nombreinsectesParasites" id="nombreinsectesParasites-' +
                    insectesParasitesCount +
                    '" ><option value="">Selectionner une option</option><option value="M">M</option><option value="F">F</option></select></div></div><div class="col-xs-12 col-sm-2"><div class="form-group"><label for="" class="">Coût Annuel</label><input type="number" placeholder="Coût Annuel" class="form-control" id="insectesParasites-' +
                    insectesParasitesCount +
                    '" name="insectesParasites[' + insectesParasitesCount +
                    '][coutAnnuel]"></div></div><div class="col-xs-12 col-sm-2"><div class="form-group"><label for="" class="">Temps de travaille</label><input type="number" placeholder="Temps de travaille sur la cacaoyère" class="form-control" id="insectesParasites-' +
                    insectesParasitesCount +
                    '" name="insectesParasites[' + insectesParasitesCount +
                    '][temps]"></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    insectesParasitesCount +
                    '" class="removeRowinsectesParasites btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';
                //---> End create table tr

                insectesParasitesCount = parseInt(insectesParasitesCount) + 1;
                $('#insectesParasites_area').append(html_table);

            });

            $(document).on('click', '.removeRowinsectesParasites', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#insectesParasites_area tr").length - 1) {
                    $(this).parents('tr').remove();
                    insectesParasitesCount = parseInt(insectesParasitesCount) - 1;
                }
            });

            //presenceAutreInsecte

            var presenceAutreInsecteCount = $("#presenceAutreInsecte_area tr").length;

            $(document).on('click', '#addRowPresenceAutreInsecte', function() {

                //---> Start create table tr'

                var html_table = `
                    <tr>
                        <td class="row">
                            <div class="col-xs-12 col-sm-12 bg-success">
                                <badge class="btn btn-outline--warning h-45 btn-sm text-white">Production de cacao ${presenceAutreInsecteCount}</badge>
                            </div>
                            <div class="col-md-3">
                                <label for="autreInsecteAnnée"  >Année</label>
                                <select class="form-control selected_type" name="presenceAutreInsecte[${presenceAutreInsecteCount}][autreInsecteAnnée]" id='presenceAutreInsecte[${presenceAutreInsecteCount}][autreInsecteAnnée]')>
                                    
                                    @foreach ($campagnes as $campagne)
                                        <option value="{{ $campagne->id }}" data-price="{{ $campagne->prix_achat }}">{{ __($campagne->nom) }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Production brute (KG) -->
                            <div class="col-xs-12 col-sm-3">
                                <div class="form-group">
                                    <label for="nombreAutreInsectesParasites" class="">Production (KG)</label>
                                    <input type="number" placeholder="Production (KG)" name="presenceAutreInsecte[${presenceAutreInsecteCount}][nombreAutreInsectesParasites]" 
                                        class="form-control" id="nombreAutreInsectesParasites-${presenceAutreInsecteCount}">
                                </div>
                            </div>

                            <!-- Revenu Brut -->
                            <div class="col-xs-12 col-sm-3">
                                <div class="form-group">
                                    <label for="revenuBrute" class="">Revenu Brut</label>
                                    <input type="number" placeholder="Revenu Brut" name="presenceAutreInsecte[${presenceAutreInsecteCount}][revenuBrute]" 
                                        class="form-control" id="revenuBrute-${presenceAutreInsecteCount}">
                                </div>
                            </div>

                            <!-- Commentaire -->
                            <div class="col-xs-12 col-sm-3">
                                <div class="form-group">
                                    <label for="commentaire" class="">Commentaire</label>
                                    <input type="text" placeholder="Commentaire" name="presenceAutreInsecte[${presenceAutreInsecteCount}][commentaire]" 
                                        class="form-control" id="commentaire-${presenceAutreInsecteCount}">
                                </div>
                            </div>

                            <!-- Bouton de suppression -->
                            <div class="col-xs-12 col-sm-8">
                                <button type="button" id="${presenceAutreInsecteCount}" 
                                    class="removeRowPresenceAutreInsecte btn btn-danger btn-sm">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    `;
                presenceAutreInsecteCount = parseInt(presenceAutreInsecteCount) + 1;
                $('#presenceAutreInsecte_area').append(html_table);

            });

            $(document).on('click', '.removeRowPresenceAutreInsecte', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#presenceAutreInsecte_area tr").length - 1) {
                    $(this).parents('tr').remove();
                    presenceAutreInsecteCount = parseInt(presenceAutreInsecteCount) - 1;
                }
            });
            //fin presenceAutreInsecte

            var traitementCount = $("#traitement_area tr").length;
            $(document).on('click', '#addRowTraitement', function() {

                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">traitement ' +
                    traitementCount +
                    '</badge></div><div class="col-xs-12 col-sm-4 pr-0"><div class="form-group"><label for="" class="">Nom</label><select class="form-control" id="traitement-' +
                    traitementCount +
                    '" name="traitement[' + traitementCount +
                    '][nom]"><option value="">Selectionner une option</option><option value="Herbicides">Herbicides</option><option value="Fongicides">Fongicides</option><option value="Compost">Compost</option><option value="Déchets animaux">Déchets animaux</option><option value="Fiente">Fiente</option><option value="Nematicide">Nematicide</option><option value="Insecticide">Insecticide</option><option value="Biofertilisant">Biofertilisant</option><option value="Engrais chimique">Engrais chimique</option><option value="Engrais foliaire">Engrais foliaire</option><option value="Bouse de vache">Bouse de vache</option><option>NPK</option><option value="Insecticide organique">Insecticide organique</option><option value="Insecticide chimique">Insecticide chimique</option><option value="Pesticides">Pesticides</option></select></div></div><div class="col-xs-12 col-sm-2"><div class="form-group row"><label>Unité</label><select class="form-control unite" name="traitement[' +
                    traitementCount + '][unite]" id="unite-' +
                    traitementCount +
                    '"><option value="">Selectionner une option</option><option value="Kg">Kg</option><option value="L">L</option><option value="g">g</option><option value="mL">mL</option></select></div></div> <div class="col-xs-12 col-sm-2"><div class="form-group row"><label for="" class="">Quantité</label><input type="number" name ="traitement[' +
                    traitementCount + '][quantite]" id="quantite-' +
                    traitementCount +
                    '" class="form-control quantite" placeholder="Quantité"></div></div><div class="col-xs-12 col-sm-2"><div class="form-group row"><label>Type contenant</label><select class="form-control contenant" name="traitement[' +
                    traitementCount + '][contenant]" id="contenant-' +
                    traitementCount +
                    '"><option value="">Selectionner une option</option><option value="Sac">Sac</option><option value="Sachet">Sachet</option><option value="Boîte">Boîte</option><option value="Pot">Pot</option></select></div></div> <div class="col-xs-12 col-sm-2"><div class="form-group row"><label for="" class="">Fréquence</label><input type="number" name="traitement[' +
                    traitementCount + '][frequence]" id="frequence-' +
                    traitementCount +
                    '" class="form-control frequence" placeholder="Fréquence"></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    traitementCount +
                    '" class="removeRowTraitement btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';
                html_table += '</tr>';
                //---> End create table tr

                traitementCount = parseInt(traitementCount) + 1;
                $('#traitement_area').append(html_table);

            });

            $(document).on('click', '.removeRowTraitement', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#traitement_area tr").length - 1) {
                    $(this).parents('tr').remove();
                    traitementCount = parseInt(traitementCount) - 1;
                }
            });

            //Pesticide lannee derniere 

            var pesticidesCount = $("#pesticidesAnneDerniere_area tr").length;
            $(document).on('click', '#addRowPesticidesAnneDerniere', function() {

                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Dépenses ' +
                    pesticidesCount +
                    '</badge></div><div class="col-xs-12 col-sm-3 pr-0"><div class="form-group"><label for="" class="">Nom</label><select class="form-control" id="pesticidesAnneDerniere-' +
                    pesticidesCount +
                    '" name="pesticidesAnneDerniere[' + pesticidesCount +
                    '][nom]"><option value="Scolarité">Scolarité</option><option value="Nourriture">Nourriture</option><option value="Santé">Santé</option><option value="Electricité">Electricité</option><option value="Eau courante">Eau courante</option><option value="Funérailles">Funérailles</option><option value="Mariages">Mariages</option><option value="Baptême">Baptême</option></select></div></div><div class="col-xs-12 col-sm-2"><div class="form-group row"><label>Périodicité</label><select class="form-control unite" name="pesticidesAnneDerniere[' +
                    pesticidesCount + '][unite]" id="unite-' +
                    pesticidesCount +
                    '"><option value="Année">Année</option><option value="Mois">Mois</option><option value="2 Mois">2 Mois</option></select></div></div> <div class="col-xs-12 col-sm-3"><div class="form-group row"><label for="" class="">Montant moyen/an</label><input type="number" name ="pesticidesAnneDerniere[' +
                    pesticidesCount + '][quantite]" id="quantite-' +
                    pesticidesCount +
                    '" class="form-control quantite" placeholder="Montant moyen/an"></div></div><div class="col-xs-12 col-sm-3"><div class="form-group row"><label for="" class="">Observation</label><input type="text" name="pesticidesAnneDerniere[' +
                    pesticidesCount + '][frequence]" id="frequence-' +
                    pesticidesCount +
                    '" class="form-control frequence" placeholder="Observation"></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    pesticidesCount +
                    '" class="removeRowPesticidesAnneDerniere btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';
                html_table += '</tr>';
                //---> End create table tr

                pesticidesCount = parseInt(pesticidesCount) + 1;
                $('#pesticidesAnneDerniere_area').append(html_table);

            });

            $(document).on('click', '.removeRowPesticidesAnneDerniere', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#pesticidesAnneDerniere_area tr").length - 1) {
                    $(this).parents('tr').remove();
                    pesticidesCount = parseInt(pesticidesCount) - 1;
                }
            });
            //fin pesticide lanne derniere

            //intrants lannee derniere
            var intrantsCount = $("#intrantsAnneDerniere_area tr").length;
            $(document).on('click', '#addRowIntrantsAnneDerniere', function() {

                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Autres dépenses ' +
                    intrantsCount +
                    '</badge></div><div class="col-xs-12 col-sm-3 pr-0"><div class="form-group"><label for="" class="">Nom</label><input type="text" placeholder="Nom" class="form-control" id="nom-' +
                    intrantsCount +
                    '" name="intrantsAnneDerniere[' + intrantsCount +
                    '][nom]"></div></div><div class="col-xs-12 col-sm-3"><div class="form-group row"><label>Périodicité</label><select class="form-control unite" name="intrantsAnneDerniere[' +
                    intrantsCount + '][unite]" id="unite-' +
                    intrantsCount +
                    '"><option value="Année">Année</option><option value="Mois">Mois</option><option value="2 Mois">2 Mois</option></select></div></div> <div class="col-xs-12 col-sm-3"><div class="form-group row"><label for="" class="">Quantité</label><input type="number" name ="intrantsAnneDerniere[' +
                    intrantsCount + '][quantite]" id="quantite-' +
                    intrantsCount +
                    '" class="form-control quantite" placeholder="Montant moyen/an"></div></div><div class="col-xs-12 col-sm-3"><div class="form-group row"><label for="" class="">Observation</label><input type="text" name="intrantsAnneDerniere[' +
                    intrantsCount + '][frequence]" id="frequence-' +
                    intrantsCount +
                    '" class="form-control frequence" placeholder="Observation"></div></div><div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    intrantsCount +
                    '" class="removeRowIntrantsAnneDerniere btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';
                html_table += '</tr>';
                //---> End create table tr

                intrantsCount = parseInt(intrantsCount) + 1;
                $('#intrantsAnneDerniere_area').append(html_table);
            });

            $(document).on('click', '.removeRowIntrantsAnneDerniere', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#intrantsAnneDerniere_area tr").length - 1) {
                    $(this).parents('tr').remove();
                    intrantsCount = parseInt(intrantsCount) - 1;
                }
            });
            //fin intrants lanne derniere
            var animauxRencontresCount = $("#animauxRencontres_area tr").length + 1;
            $(document).on('click', '#addRowanimauxRencontres', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Animal ' +
                    animauxRencontresCount +
                    '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><label for="animauxRencontres" class="">Animal</label><input placeholder="Nom animal..." class="form-control" id="animauxRencontres-' +
                    animauxRencontresCount +
                    '" name="animauxRencontres[]" type="text"></div></div><div class="col-xs-12 col-sm-12"><button type="button" id="' +
                    animauxRencontresCount +
                    '" class="removeRowanimauxRencontres btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';
                //---> End create table tr

                animauxRencontresCount = parseInt(animauxRencontresCount) + 1;
                $('#animauxRencontres_area').append(html_table);

            });

            $(document).on('click', '.removeRowanimauxRencontres', function() {
                var row_id = $(this).attr('id');
                if (row_id == $("#animauxRencontres_area tr").length) {
                    $(this).parents('tr').remove();
                    animauxRencontresCount = parseInt(animauxRencontresCount) - 1;
                }
            });

            $('#courseaux,#agroforestiersobtenus,#recu,#autrePesticides,#autrePresenceInsectesParasitesRavageurs,#traite')
                .hide();

            $('.arbresagroforestiers').change(function() {
                var arbresagroforestiers = $('.arbresagroforestiers').val();
                if (arbresagroforestiers == 'oui') {
                    $('#agroforestiersobtenus').show('slow');
                    $('#recu').show('slow');
                    $('.recuArbreAgroForestier').show('slow');
                    $('.recuArbreAgroForestier').attr('required', true);
                } else {
                    $('#agroforestiersobtenus').hide('slow');
                    $('#recu').hide('slow');
                    $('.recuArbreAgroForestier').hide('slow');
                    $('.recuArbreAgroForestier').attr('required', false);
                    $('.recuArbreAgroForestier').val('');
                }
            });

            $('.pesticideUtiliseAnne').change(function() {
                var pesticideUtiliseAnne = $('.pesticideUtiliseAnne').find(":selected").map((key, item) => {
                    return item.textContent.trim();
                }).get();
                if (pesticideUtiliseAnne.includes("Autre")) {
                    $('#autrePesticides').show('slow');
                    $('#autrePesticide').prop('required', true);
                    $('.autrePesticide').show('slow');

                } else {

                    $('#autrePesticides').hide('slow');
                    $('#autrePesticide').prop('required', false);
                    $('.autrePesticide').hide('slow');
                    $('.autrePesticide').val('');

                }
            });
            $('.presenceInsectesParasites').change(function() {
                var presenceInsectesParasites = $('.presenceInsectesParasites').val();
                if (presenceInsectesParasites == 'oui') {
                    $('#presenceInsectesParasitesRavageurs').show('slow');
                    $('.presenceInsectesParasitesRavageur').show('slow');
                } else {
                    $('#presenceInsectesParasitesRavageurs').hide('slow');
                    $('.presenceInsectesParasitesRavageur').val('');
                    $('#autrePresenceInsectesParasitesRavageurs').hide('slow');

                    var selectElements = $('select[name^="insectesParasites["]');

                    // Faire quelque chose avec chaque élément sélectionné
                    selectElements.each(function() {
                        // Vous pouvez accéder à chaque élément individuellement avec $(this)
                        $(this).val($(this).find('option:eq(0)').val());
                        console.log($(this))
                    });
                }
            });
            $('.autreInsecte').change(function() {
                var autreInsecte = $('.autreInsecte').val();
                if (autreInsecte == 'oui') {
                    $('#presenceAutreInsecte').show('slow');
                } else {
                    $('#presenceAutreInsecte').hide('slow');
                    $('#presenceAutreInsecte input').val('');
                    var selectElements = $('select[name^="presenceAutreInsecte["]');

                    // Faire quelque chose avec chaque élément sélectionné
                    selectElements.each(function() {
                        // Vous pouvez accéder à chaque élément individuellement avec $(this)
                        $(this).val($(this).find('option:eq(0)').val());
                        console.log($(this))
                    });

                }
            });


            $('.presenceAutreTypeInsecteAmi').change(function() {
                var presenceAutreTypeInsecteAmi = $('.presenceAutreTypeInsecteAmi').val();
                if (presenceAutreTypeInsecteAmi == 'oui') {

                    $('#autreInsectesAmis').show('slow');

                } else {
                    $('#autreInsectesAmis').hide('slow');
                    $('#autreInsectesAmis input').val('');
                }
            });
            $('#traiterParcelle').change(function() {
                var traiterParcelle = $('#traiterParcelle').val();
                if (traiterParcelle == 'oui') {
                    $('#traite').show('slow');
                } else {
                    $('#traite').hide('slow');
                    $('#traite input').val('');
                    var selectElements = $('select[name^="traitement["]');

                    // Faire quelque chose avec chaque élément sélectionné
                    selectElements.each(function() {
                        // Vous pouvez accéder à chaque élément individuellement avec $(this)
                        $(this).val($(this).find('option:eq(0)').val());
                        console.log($(this))
                    });
                    //traitement[0][nom]
                }
            });

            $('#localite').chained("#section")
            $("#producteur").chained("#localite");
            $("#parcelle").chained("#producteur");

        });
    </script>

    <script>
        "use strict";
        $(document).on('blur', '[id^="nombreAutreInsectesParasites-"]', function() {
            // Récupération de l'index à partir de l'ID du champ de production
            let index = $(this).attr('id').split('-')[1];

            // Récupérer la valeur de production saisie par l'utilisateur
            let productionKg = parseFloat($(this).val()) || 0;

            // Sélectionner l'option active et récupérer le data-price
            let selectedOption = $(
                `select[name="presenceAutreInsecte[${index}][autreInsecteAnnée]"] option:selected`);
            let prixAchat = parseFloat(selectedOption.data('price')) || 0;

            // Calcul du revenu brut
            let revenuBrut = productionKg * prixAchat;

            // Mise à jour du champ revenu brut
            $(`#revenuBrute-${index}`).val(revenuBrut.toFixed(2));
        });
    </script>
@endpush
