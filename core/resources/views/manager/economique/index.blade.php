@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">

                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Type de materiel')</th>
                                    <th>@lang('Nom du matériel')</th>
                                    <th>@lang('Quantité')</th>
                                    <th>@lang('Quantité en bon état')</th>
                                    <th>@lang('Quantité en mauvais état')</th>
                                    <th>@lang('Ajouté le')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @forelse($equipements as $info)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $info->producteur->nom }}
                                                {{ $info->producteur->prenoms }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $info->type }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $info->libelle }}</span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ $info->quantite }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $info->etat_bon_quantite }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $info->etat_mauvais_quantite }}</span>
                                        </td>
                                        <td>
                                            {{ showDateTime($info->created_at) }} <br>
                                            {{ diffForHumans($info->created_at) }}
                                        </td> --}}

                                        {{-- <td>
                                            <a href="{{ route('manager.traca.producteur.showinfosproducteur', encrypt($info->id)) }}"
                                                class="icon-btn btn--info ml-1">@lang('Détails')</a>

                                            <a href="{{ route('manager.traca.producteur.editinfo', encrypt($info->id)) }}"
                                                class="icon-btn btn--primary ml-1">@lang('Edit')</a>
                                        </td> --}}
                                    {{-- </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse --}}

                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- @if ($equipements->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($equipements) }}
                    </div>
                @endif --}}

            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here ..." />
    <a href="{{ route('manager.traca.producteur.economiquecreate', encrypt($id)) }}" class="btn  btn-outline--primary box--shadow1  h-45"><i
                class="las la-plus"></i>@lang('Enregistrer le profil socio-economique')</a>

    <x-back route="{{ route('manager.traca.producteur.index') }}" />
@endpush

@push('script')
    <script>
        "use strict";
        $('.addNewBrach').on('click', function() {
            $('#cooperativeModel').modal('show');
        });


        $(document).ready(function() {
            $('#cooperativeModel .btnFermer').click(function() {
                $('#cooperativeModel').modal('hide');
            });
        });

        $(document).ready(function() {
            $('#cooperativeModel').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            $(document).ready(function() {

                var productCount = $("#product_area tr").length + 1;
                $(document).on('click', '#addRow', function() {

                    //---> Start create table tr
                    var html_table = '<tr>';
                    html_table +=
                        '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Information Culture ' +
                        productCount +
                        '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group row"><label for="Type de culture" class="control-label">Type de culture</label><input placeholder="Riz, Maïs, Igname, Banane, ..." class="form-control" id="typeculture-' +
                        productCount +
                        '" name="typeculture[]" type="text"></div></div><div class="col-xs-12 col-sm-12"><div class="form-group row"><label for="superficieculture" class="control-label">Superficie de culture</label><input type="text" name="superficieculture[]" placeholder="Superficie de culture" id="superficieculture-' +
                        productCount +
                        '" class="form-control " value=""></div></div><div class="col-xs-12 col-sm-12"><button type="button" id="' +
                        productCount +
                        '" class="removeRow btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                    html_table += '</tr>';
                    //---> End create table tr

                    productCount = parseInt(productCount) + 1;
                    $('#product_area').append(html_table);

                });

                $(document).on('click', '.removeRow', function() {

                    var row_id = $(this).attr('id');

                    // delete only last row id
                    if (row_id == $("#product_area tr").length) {

                        $(this).parents('tr').remove();

                        productCount = parseInt(productCount) - 1;

                        //    console.log($("#product_area tr").length);

                        //  productCount--;

                    }
                });

            });

            $(document).ready(function() {

                var productCount = $("#activity_area tr").length + 1;

                $(document).on('click', '#addRowActivite', function() {

                    //---> Start create table tr
                    var html_table = '<tr>';

                    html_table +=
                        '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Information Activité ' +
                        productCount +
                        '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group"><label for="" class="control-label">Type D\'activité</label><input placeholder="Elevage, Commerce, Prestation de service, ..." class="form-control" id="typeactivite-' +
                        productCount +
                        '" name="typeactivite[]" type="text"></div></div><div class="col-xs-12 col-sm-12 col-md-12"><button type="button" id="' +
                        productCount +
                        '" class="removeRowActivite btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';

                    html_table += '</tr>';
                    //---> End create table tr

                    productCount = parseInt(productCount) + 1;


                    $('#activity_area').append(html_table);

                });

                $(document).on('click', '.removeRowActivite', function() {

                    var row_id = $(this).attr('id');

                    // delete only last row id
                    if (row_id == $("#activity_area tr").length) {

                        $(this).parents('tr').remove();

                        productCount = parseInt(productCount) - 1;

                        //    console.log($("#product_area tr").length);

                        //  productCount--;

                    }
                });


            });

            $(document).ready(function() {

                var productCount = $("#compagny_area tr").length + 1;
                $(document).on('click', '#addRowOperateur', function() {

                    //---> Start create table tr
                    var html_table = '<tr>';
                    html_table +=
                        '<td class="row"><div class="col-xs-12 col-sm-12 bg-success"><badge class="btn  btn-outline--warning h-45 btn-sm text-white">Information mobile monnaie ' +
                        productCount +
                        '</badge></div><div class="col-xs-12 col-sm-12"><div class="form-group row"><label for="Type de culture" class="control-label">Opérateur</label><select name="operateurMM[]" id="operateurMM-' +
                        productCount +
                        '" class="form-control"><option value="MTN">MTN</option><option value="ORANGE">ORANGE</option><option value="MOOV">MOOV</option><option value="Wave">Wave</option><option value="Push">Push</option></select></div></div><div class="col-xs-12 col-sm-12"><div class="form-group row"><label for="" class="control-label">Numéro</label><input type="text" name="numeros[]" placeholder="Numéro opérateur" id="numeros-' +
                        productCount +
                        '" class="form-control " value=""></div></div><div class="col-xs-12 col-sm-12"><button type="button" id="' +
                        productCount +
                        '" class="removeRowOperateur btn btn-danger btn-sm"><i class="fa fa-minus"></i></button></div></td>';
                    html_table += '</tr>';
                    //---> End create table tr

                    productCount = parseInt(productCount) + 1;
                    $('#compagny_area').append(html_table);

                });

                $(document).on('click', '.removeRowOperateur', function() {

                    var row_id = $(this).attr('id');

                    // delete only last row id
                    if (row_id == $("#compagny_area tr").length) {

                        $(this).parents('tr').remove();

                        productCount = parseInt(productCount) - 1;

                        //    console.log($("#product_area tr").length);

                        //  productCount--;

                    }
                });

            });

        });
        $('#listecultures,#gardePapiersChamps,#numeroCompteMM,#listeactivites,#nomBanque,#autreBanque,#travailleurFamilial,#societe')
            .hide();

        $('.autresCultures').change(function() {
            var autresCultures = $('.autresCultures').val();
            if (autresCultures == 'oui') {
                $('#listecultures').show('slow');
            } else {
                $('#listecultures').hide('slow');
                $('.listecultures').val('');
            }
        });
        $('.mainOeuvreFamilial').change(function() {
            var mainOeuvreFamilial = $('.mainOeuvreFamilial').val();
            if (mainOeuvreFamilial == 'oui') {
                $('#travailleurFamilial').show('slow');
                $('.travailleurFamilial').show('slow');
            } else {
                $('#travailleurFamilial').hide('slow');
                $('.travailleurFamilial').val('');
            }
        });
        $('.societeTravail').change(function() {
            var societeTravail = $('.societeTravail').val();
            if (societeTravail == 'oui') {
                $('#societe').show('slow');
                $('#nombrePersonne').prop('required', true);
            } else {
                $('#societe').hide('slow');
                $('#nombrePersonne').prop('required', false);
                $('.nombrePersonne').val('');
            }
        });

        $('.nomBanque').change(function() {
            var nomBanque = $('.nomBanque').val();
            if (nomBanque == 'Autre') {
                $('#autreBanque').show('slow');
                $('.autreBanque').show('slow');
            } else {
                $('#autreBanque').hide('slow');
                $('.autreBanque').val('');
            }
        });
        $('.compteBanque').change(function() {
            var compteBanque = $('.compteBanque').val();
            if (compteBanque == 'oui') {
                $('#nomBanque').show('slow');
                $('.nomBanque').show('slow');
            } else {
                $('#nomBanque').hide('slow');
                $('.nomBanque').val('');
            }
        });
        $('.autreActivite').change(function() {
            var autreActivite = $('.autreActivite').val();
            if (autreActivite == 'oui') {
                $('#listeactivites').show('slow');
            } else {
                $('#listeactivites').hide('slow');
                $('.listeactivites').val('');
            }
        });

        $('.mobileMoney').change(function() {
            var mobileMoney = $('.mobileMoney').val();
            if (mobileMoney == 'oui') {
                $('#numeroCompteMM').show('slow');
                $('.numeroCompteMM').css('display', 'block');
            } else {
                $('#numeroCompteMM').hide('slow');
                $('.numeroCompteMM').val('');
            }
        });
    </script>
    <script type="text/javascript">
        $('#superficie').hide();
        $('.foretsjachere').change(function() {
            var foretsjachere = $('.foretsjachere').val();
            if (foretsjachere == 'oui') {
                $('#superficie').show('slow');
            } else {
                $('#superficie').hide('slow');
                $('.superficie').val('');
            }
        });
    </script>
@endpush
