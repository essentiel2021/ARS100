@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.suivi.menage.store'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section" id="section" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}"
                                        {{ old('section') == $section->id ? 'selected' : '' }}>
                                        {{ $section->libelle }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner une localite')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="localite" id="localite" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($localites as $localite)
                                    <option value="{{ $localite->id }}" data-chained="{{ $localite->section->id }}"
                                        {{ old('localite') == $localite->id ? 'selected' : '' }}>
                                        {{ $localite->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-sm-4 control-label">@lang('Selectionner un producteur')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="producteur_id" id="producteur" required>
                                <option value="">@lang('Selectionner une option')</option>
                                @foreach ($producteurs as $producteur)
                                    <option value="{{ $producteur->id }}" data-chained="{{ $producteur->localite->id }}"
                                        {{ old('producteur_id') == $producteur->id ? 'selected' : '' }}>
                                        {{ stripslashes($producteur->nom) }} {{ stripslashes($producteur->prenoms) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr class="panel-wide">
                    <div class="form-group row">
                        <?php echo Form::label(__('Nom'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('nom', null, ['placeholder' => __('Saisissez le Nom'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Prenoms'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('prenoms', null, ['placeholder' => __('Saisissez le Prenoms'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Sexe'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('sexe', ['' => 'Selectionner une option', 'H' => 'H', 'F' => 'F'], null, ['class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Famille(Lien de parenté)'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('statutMatrimonial', ['' => 'Selectionner une option', 'Chef de menage' => 'Chef de ménage', 'Conjoint' => 'Conjoint', 'Enfant ' => 'Enfant', 'Autre' => 'Autre'], null, ['class' => 'form-control statutMatrimonial', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id= "autre_famille">
                        <?php echo Form::label(__('Préciser'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('autre_lien_parente', null, ['id' => 'autre_lien_parente', 'placeholder' => __('Autre lien de parenté'), 'class' => 'form-control autre_lien_parente']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Date de naissance'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::date('dateNaiss', null, ['class' => 'form-control naiss', 'id' => 'datenais', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Numero de téléphone'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('phone1', null, ['class' => 'form-control phone']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__("Niveau d'instruction"), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('niveau_etude', ['Préscolaire' => 'Préscolaire', 'Primaire' => 'Primaire', 'Secondaire' => 'Secondaire', 'Supérieur' => 'Supérieur', 'Aucun' => 'Aucun', 'Autre' => 'Autre'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control niveau_etude', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row" id="autre_niveau_etude">
                        <?php echo Form::label(__('Autre Niveau d\'instruction'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('autre_instruction', null, ['id' => 'autre_instruction', 'placeholder' => __('Autre niveau d\'instruction'), 'class' => 'form-control autre_instruction']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Statut Scolaire'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('statut_scolaire', ['Scolarise' => 'Scolarisé', 'Déscolarise' => 'Déscolarisé'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <?php echo Form::label(__('Catégorie ethnique'), null, ['class' => 'col-sm-4 control-label']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::select('categorie_ethnique', ['Autochtone' => 'Autochtone', 'Allochtone' => 'Allochtone', 'Allogène' => 'Allogène'], null, ['placeholder' => __('Selectionner une option'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>
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
    {{-- <x-back route="{{ route('manager.suivi.menage.index') }}" /> --}}
@endpush

@push('script')
    <script type="text/javascript">
        $("#localite").chained("#section");
        $("#producteur").chained("#localite");
        $(document).ready(function() {
            $('#autre_famille,#autre_niveau_etude').hide();
            $('.statutMatrimonial').change(function() {
                var statutMatrimonial = $('.statutMatrimonial').val();
                if (statutMatrimonial == 'Autre') {
                    $('#autre_famille').show('slow');
                } else {
                    $('#autre_famille').hide('slow');
                    $('.autre_lien_parente').val('');
                }
            });
            $('.niveau_etude').change(function() {
                var typeCarteSecuriteSociale = $('.niveau_etude').val();

                if (typeCarteSecuriteSociale == 'Autre') {
                    $('#autre_niveau_etude').show('slow');
                    $('.autre_instruction').show('slow');
                    $("#autre_instruction").prop("required", true);
                } else {
                    $('#autre_niveau_etude').hide('slow');
                    $('.autre_instruction').val('');
                    $("#autre_instruction").prop("required", false);
                }
            });
        });
    </script>
@endpush
