@extends('manager.layouts.app')
@section('panel')
    <x-setting-sidebar :activeMenu="$activeSettingMenu" />
    <x-setting-card>
        <x-slot name="header">
            <div class="s-b-n-header" id="tabs">
                <h2 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    @lang($pageTitle)</h2>
            </div>
        </x-slot>
        <div class="col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4 ">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($localite, [
                        'method' => 'POST',
                        'route' => ['manager.settings.localite-settings.store', $localite->id],
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    @method('POST')
                    <input type="hidden" name="id" value="{{ $localite->id }}">
                    <input type="hidden" name="codeLocal" value="{{ $localite->codeLocal }}">
                    <div class="form-group row">
                        <label class="col-xs-12 col-sm-4">@lang('Select Cooperative')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" disabled>
                                <option value="">{{ __($manager->cooperative->name) }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-xs-12 col-sm-4">@lang('Select section')</label>
                        <div class="col-xs-12 col-sm-8">
                            <select class="form-control" name="section_id">
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected($section->id == $localite->section_id)>
                                        {{ __($section->libelle) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php echo Form::label(__('Nom de la localite'), null, ['class' => 'control-label col-xs-12 col-sm-4']); ?>
                        <div class="col-xs-12 col-sm-8">
                            <?php echo Form::text('nom', null, ['placeholder' => __('Nom de la localite'), 'class' => 'form-control', 'required']); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" id="save-form" class="btn btn--primary w-100 h-45">
                            @lang('app.save')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </x-setting-card>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('manager.settings.localite-settings.index') }}" />
@endpush
@push('script')
    <script type="text/javascript">
        $('#nombrecole, #etatpompehydrau,#centreSante,#jourmarches').hide();
        $('.marche').change(function() {
            var marche = $('.marche').val();
            if (marche == 'non') {
                $('#kmmarcheproches').show('slow');
                $('#jourmarches').hide('slow');

            } else {
                $('#kmmarcheproches').hide('slow');
                $('.kmmarcheproche').val('');
                $('#jourmarches').show('slow');

            }
        });
        if ($('.marche').val() == 'non') {
            $('#kmmarcheproches').show('slow');
            $('#jourmarches').hide('slow');

        } else {
            $('#kmmarcheproches').hide('slow');
            $('.kmmarcheproche').val('');
            $('#jourmarches').show('slow');

        }
        $('.centresante').change(function() {
            var centresante = $('.centresante').val();
            if (centresante == 'non') {
                $('#nonCentreSante').show('slow');
                $('#centreSante').hide('slow');
            } else {
                $('#nonCentreSante').hide('slow');
                $('.kmCentresante').val('');
                $('.nomCentresante').val('');
                $('#centreSante').show('slow');
            }

        });
        if ($('.centresante').val() == 'non') {
            $('#nonCentreSante').show('slow');
            $('#centreSante').hide('slow');
        } else {
            $('#nonCentreSante').hide('slow');
            $('.kmCentresante').val('');
            $('.nomCentresante').val('');
            $('#centreSante').show('slow');
        }

        $('.ecole').change(function() {
            var ecole = $('.ecole').val();
            if (ecole == 'oui') {
                $('#nombrecole').show('slow');
                $('#nonEcolePrimaire').hide('slow');
                $('.kmEcoleproche').val('');
                $('.nomEcoleproche').val('');
                $('#kmEcoleproche').prop('required', false);
                $('#nomEcoleproche').prop('required', false);

            } else {
                $('#nombrecole').hide('slow');
                $('#nonEcolePrimaire').show('slow');
                $('.kmEcoleproche').val('');
                $('.nomEcoleproche').val('');
                $('#kmEcoleproche').prop('required', true);
                $('#nomEcoleproche').prop('required', true);
            }
        });
        if ($('.ecole').val() == 'oui') {
            $('#nombrecole').show('slow');
            $('#nonEcolePrimaire').hide('slow');
            $('.kmEcoleproche').val('');
            $('.nomEcoleproche').val('');
            $('#kmEcoleproche').prop('required', false);
            $('#nomEcoleproche').prop('required', false);

        } else {
            $('#nombrecole').hide('slow');
            $('#nonEcolePrimaire').show('slow');
            $('.kmEcoleproche').val('');
            $('.nomEcoleproche').val('');
            $('#kmEcoleproche').prop('required', true);
            $('#nomEcoleproche').prop('required', true);
        }

        $('.eauxPotables').change(function() {
            var eauxPotables = $('.eauxPotables').find(":selected").map((key, item) => {
                return item.textContent.trim();
            }).get();
            if (eauxPotables.includes("Pompe Hydraulique Villageoise")) {

                $('#etatpompehydrau').show('slow');
                $('.etatpompehydrau').show('slow');

            } else {

                $('#etatpompehydrau').hide('slow');
                $('.etatpompehydrau').show('slow');

            }
        });
        if($('.eauxPotables').find(":selected").map((key, item) => {
                return item.textContent.trim();
            }).get().includes("Pompe Hydraulique Villageoise")){
                $('#etatpompehydrau').show('slow');
                $('.etatpompehydrau').show('slow');
        }else{
            $('#etatpompehydrau').hide('slow');
            $('.etatpompehydrau').show('slow');
        }

        $(document).ready(function() {
            var maladiesCount = $("#maladies tr").length + 1;
            $(document).on('click', '#addRowMal', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn btn-warning btn-sm">Nom Ecole Primaire ' +
                    maladiesCount +
                    '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><input placeholder="Nom école primaire" class="form-control" id="nomecolesprimaires-' +
                    maladiesCount +
                    '" name="nomecolesprimaires[]" type="text"></div></div>' +
                    '<div class="col-xs-12 col-sm-12"><div class="form-group row"><input type="text" name="latitude[]" placeholder="Latitude Ex : 14 " id="latitude-' +
                    maladiesCount +
                    '" class="form-control" value="{{ old('latitude') }}"></div></div>' +
                    '<div class="col-xs-12 col-sm-12"><div class="form-group row"><input type="text" name="longitude[]" placeholder="Longitude Ex : -14" id="longitude-' +
                    maladiesCount +
                    '" class="form-control" value="{{ old('longitude') }}"></div></div>' +
                    '<div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    maladiesCount +
                    '" class="removeRowMal btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';


                maladiesCount = parseInt(maladiesCount) + 1;
                $('#maladies').append(html_table);

            });

            $(document).on('click', '.removeRowMal', function() {

                var row_id = $(this).attr('id');

                // delete only last row id
                if (row_id == $("#maladies tr").length) {

                    $(this).parents('tr').remove();

                    maladiesCount = parseInt(maladiesCount) - 1;

                }
            });

        });


        $(document).ready(function() {

            var maladiesCount = $("#centreSantes tr").length + 1;
            $(document).on('click', '#addRowMalSante', function() {

                //---> Start create table tr
                var html_table = '<tr>';
                html_table +=
                    '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn btn-warning btn-sm">Nom Centre de Santé ' +
                    maladiesCount +
                    '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><input placeholder="Nom du centre de santé" class="form-control" id="nomcentresantes-' +
                    maladiesCount +
                    '" name="nomcentresantes[]" type="text"></div></div>' +
                    '<div class="col-xs-12 col-sm-12"><div class="form-group row"><input type="text" name="latitude[]" placeholder="Latitude Ex : 14 " id="latitude-' +
                    maladiesCount +
                    '" class="form-control" value="{{ old('latitude') }}"></div></div>' +
                    '<div class="col-xs-12 col-sm-12"><div class="form-group row"><input type="text" name="longitude[]" placeholder="Longitude Ex : -14" id="longitude-' +
                    maladiesCount +
                    '" class="form-control" value="{{ old('longitude') }}"></div></div>' +
                    '<div class="col-xs-12 col-sm-8"><button type="button" id="' +
                    maladiesCount +
                    '" class="removeRowSante btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                html_table += '</tr>';


                maladiesCount = parseInt(maladiesCount) + 1;
                $('#centreSantes').append(html_table);

            });

            $(document).on('click', '.removeRowSante', function() {

                var row_id = $(this).attr('id');

                // delete only last row id
                if (row_id == $("#centreSantes tr").length) {

                    $(this).parents('tr').remove();

                    maladiesCount = parseInt(maladiesCount) - 1;

                }
            });

        });

        $('#save-form').click(function() {
            var url = "{{ route('manager.settings.localite-settings.store') }}";

            $.easyAjax({
                url: url,
                container: '#editSettings',
                type: "POST",
                disableButton: true,
                blockUI: true,
                redirect: true,
                buttonSelector: "#save-form",
                data: $('#editSettings').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            })
        });
    </script>
@endpush
