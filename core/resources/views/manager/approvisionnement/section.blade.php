@extends('manager.layouts.app')
@section('panel')
<?php 
use Illuminate\Support\Arr;
 ?>
    <div class="row">
        <div class="col-lg-12">
        <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <input type="hidden" name="table" value="approvisionnements"/>
                            <div class="flex-grow-1">
                                <label>@lang('Recherche par Mot(s) clé(s)')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Localité')</label>
                                <select name="localite" class="form-control">
                                    <option value="">@lang('Toutes')</option>
                                    @foreach($localites as $local)
                                    <option value="{{ $local->id }}">{{ $local->nom }}</option>
                                    @endforeach 
                                </select>
                            </div> 
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" class="dates form-control" placeholder="@lang('Date de début - Date de fin')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card b-radius--10 mb-3">
                <div class="card-body">
                    <div class="row">
                            <div class="col-md-4">
                                <div class="alert alert-success text-center">
                                <div class="fw-bold">{{ $total }}</div>
                                    @lang('TOTAL ARBRES')
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info text-center">
                                <div class="fw-bold">{{ $total_section }}</div>
                                    @lang('TOTAL ARBRES DISTRIBUES')
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning text-center">
                                <div class="fw-bold">{{ $total-$total_section }}</div>
                                    @lang('TOTAL ARBRES RESTANTS')
                                </div>
                            </div>
                    </div>
                    
                </div>
            </div>
            <div class="card b-radius--10 ">
                <div class="card-body  p-0">
                    <div class="table-responsive--sm table-responsive">
                        
                        <table class="table table--light style--two">
                            <thead>
                                <tr> 
                                    <th>@lang('Cooperative')</th> 
                                    <th>@lang('Section')</th>
                                    <th>@lang('Quantite')</th>
                                    <th>@lang('Bon de livraison')</th> 
                                    <th>@lang('Ajoutée le')</th> 
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvisionnements as $approvisionnement)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ @$approvisionnement->section->cooperative->name }}</span>
                                        </td> 
                                        <td>
                                            <span class="fw-bold">{{ @$approvisionnement->section->libelle }}</span>
                                        </td> 
                                        <td>
                                            <span>{{ $approvisionnement->total }}</span>
                                        </td>
                                        <td>
                                            <span>
                                            <a href="{{ asset('core/storage/app/' . $approvisionnement->bon_livraison) }}" target="_blank">@lang('Bon de livraison')</a>
                                            </span>
                                        </td> 
                                        <td>
                                            <span class="d-block">{{ showDateTime($approvisionnement->created_at) }}</span>
                                            <span>{{ diffForHumans($approvisionnement->created_at) }}</span>
                                        </td> 
                                        <td>
                                      
                                        <a href="{{ route('manager.agro.approvisionnement.show-section', encrypt($approvisionnement->id)) }}"
                                                class="btn btn-sm btn-outline--primary"><i
                                                    class="las la-pen"></i>@lang('Details')</a>
                                             
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
                @if ($approvisionnements->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($approvisionnements) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    
    <a href="{{ route('manager.agro.approvisionnement.create-section',['id'=>request()->id]) }}" class="btn  btn-outline--primary h-45 addNewCooperative">
        <i class="las la-plus"></i>@lang("Ajouter nouveau")
    </a>
     
    <a href="{{ route('manager.agro.approvisionnement.exportExcel.approvisionnementAll') }}" class="btn  btn-outline--warning h-45"><i class="las la-cloud-download-alt"></i> @lang('Exporter en Excel')</a>
    <x-back route="{{ route('manager.agro.approvisionnement.index') }}" class="btn  btn-outline--warning h-45" />
@endpush
@push('style')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endpush
@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/fcadmin/css/vendor/datepicker.min.css') }}">
@endpush
@push('script')
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/fcadmin/js/vendor/datepicker.fr.js') }}"></script>
<script src="{{ asset('assets/fcadmin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";

            $('.addType').on('click', function() {
                $('#typeModel').modal('show');
            });
            
            $('.dates').datepicker({
                maxDate:new Date(),
                range:true,
                multipleDatesSeparator:"-",
                language:'en'
            });

            let url=new URL(window.location).searchParams;
            if(url.get('localite') != undefined && url.get('localite') != ''){
                $('select[name=localite]').find(`option[value=${url.get('localite')}]`).attr('selected',true);
            }
            if(url.get('payment_status') != undefined && url.get('payment_status') != ''){
                $('select[name=payment_status]').find(`option[value=${url.get('payment_status')}]`).attr('selected',true);
            }

        })(jQuery)
    </script>
@endpush

