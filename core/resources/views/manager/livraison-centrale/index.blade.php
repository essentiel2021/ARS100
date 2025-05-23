@extends('manager.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4"> 
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) clé(s)')</label>
                                <input type="text" name="search"  value="{{ request()->search }}" class="form-control">
                            </div>
                            
                            <div class="flex-grow-1">
                                <label>@lang('Magasin de Section')</label>
                                <select name="magasin" class="form-control">
                                    <option value="">@lang('Tous')</option>
                                    @foreach ($magasins as $local)
                                        <option value="{{ $local->id }}" {{ request()->magasin == $local->id ? 'selected' : '' }}>{{ $local->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                            <label>@lang('Type de produit')</label>
                            <select class="form-control" name="produit">
                                                    <option  value="">@lang('Tous')</option> 
                                                        <option value="{{ __('Certifie') }}"
                                                        @selected(request()->produit=='Certifie')>{{ __('Certifie') }}</option>
                                                        <option value="{{ __('Ordinaire') }}"
                                                        @selected(request()->produit=='Ordinaire')>{{ __('Ordinaire') }}</option>
                                                </select> 
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="form-control dates"
                                    placeholder="@lang('Date de début - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                <th>@lang("Campagne")</th>
                                <th>@lang("Periode")</th>
                                    <th>@lang("Magasin Central")</th>
                                    <th>@lang('Magasin Section')</th>
                                    <th>@lang('Producteur')</th>
                                    <th>@lang('Type Produit')</th> 
                                    <th>@lang('Quantite')</th>
                                    <th>@lang('Date de livraison')</th>  
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($livraisonProd as $produit)
                                    <tr>
                                    <td>
                                            {{ $produit->campagne->nom }} 
                                        </td>
                                        <td>
                                            {{ @$produit->campagnePeriode->nom }} 
                                        </td> 
                                        <td>
                                        <span class="fw-bold">{{ __($produit->stockMagasinCentral->magasinCentral->nom) }}</span> 
                                        </td>
                                        <td> 
                                            <span class="fw-bold">{{ __($produit->stockMagasinCentral->magasinSection->nom) }}</span> 
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $produit->producteur->nom }} {{ $produit->producteur->prenoms }}</span>
                                        </td>
                                        <td>
                                            {{ $produit->type_produit }} 
                                        </td>
                                        
                                        <td>
                                            {{ $produit->quantite }} 
                                        </td>
                                        <td>
                                            {{ showDateTime($produit->stockMagasinCentral->estimate_date, 'd M Y') }}<br>
                                            {{ diffForHumans($produit->stockMagasinCentral->estimate_date) }}
                                        </td>
  
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($livraisonProd->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($livraisonProd) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins') 

<a href="{{ route('manager.livraison.exportExcel.magcentralAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> Exporter en Excel</a>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush 
@push('script')
<script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.fr.js') }}"></script>
<script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
    <script>
        (function($) {
            "use strict";

            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });

            $('.dates').datepicker({
                maxDate: new Date(),
                range: true,
                multipleDatesSeparator: "-",
                language: 'fr'
            });

            let url = new URL(window.location).searchParams;
            if (url.get('localite') != undefined && url.get('localite') != '') {
                $('select[name=localite]').find(`option[value=${url.get('localite')}]`).attr('selected', true);
            }
            if (url.get('status') != undefined && url.get('status') != '') {
                $('select[name=status]').find(`option[value=${url.get('status')}]`).attr('selected', true);
            }

        })(jQuery)

        $('form select').on('change', function(){
    $(this).closest('form').submit();
});
    </script>
@endpush