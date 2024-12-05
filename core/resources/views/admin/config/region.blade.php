@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Délégation Régionale')</th>
                                    <th>@lang('Région')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Last Update')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($regions as $region)
                                    <tr>

                                        <td>
                                            <span class="fw-bold">{{ __($region->delegation->libelle) }}</span>
                                        </td>

                                        <td>
                                            <span>{{ __($region->libelle) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $region->statusBadge;
                                            @endphp
                                        </td>

                                        <td>
                                            <span class="d-block">{{ showDateTime($region->updated_at) }}</span>
                                            <span>{{ diffForHumans($region->updated_at) }}</span>
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline--primary updateRegion"
                                                data-id="{{ $region->id }}" data-libelle="{{ $region->libelle }}"
                                                class="las la-pen"></>@lang('Edit')</button>

                                            @if ($region->status == Status::DISABLE)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.config.region.status', $region->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir activer cette région ?')">
                                                    <i class="la la-eye"></i> @lang('Activé')
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.config.region.status', $region->id) }}"
                                                    data-question="@lang('Etes-vous sûr de vouloir désactiver cette région ?')">
                                                    <i class="la la-eye-slash"></i>@lang('Désactivé')
                                                </button>
                                            @endif
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
                @if ($regions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($regions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div id="unitModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Ajouter une région')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i> </button>
                </div>
                <form action="{{ route('admin.config.region.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label>@lang('Délégation régionale')</label>
                                <select class="form-control" name="delegation" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($delegations as $data)
                                        <option value="{{ $data->id }}" @selected(old('delegation'))>
                                            {{ __($data->libelle) }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                {{ Form::label(__('Nom de la région'), null, ['class' => 'control-label required']) }}
                                {!! Form::text('libelle', null, [
                                    'placeholder' => __('Saissisez le nom de la région'),
                                    'class' => 'form-control',
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45 ">@lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="updateRegionModel" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Mise à jour de la région')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.config.region.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <label>@lang('Délégation régionale')</label>
                                <select class="form-control" name="delegation" required>
                                    <option value="">@lang('Selectionner une option')</option>
                                    @foreach ($delegations as $data)
                                        <option value="{{ $data->id }}" @selected(old('delegation',$data->id))>
                                            {{ __($data->libelle) }}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                {{ Form::label(__('Nom de la région'), null, ['class' => 'control-label required']) }}
                                {!! Form::text('libelle', null, [
                                    'placeholder' => __('Saissisez le nom de la région'),
                                    'class' => 'form-control',
                                    'required',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Envoyer')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-confirmation-modal />
@endsection



@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--primary addregion"><i class="las la-plus"></i>@lang('Ajouter nouveau')</button>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.addregion').on('click', function() {
                $('#unitModel').modal('show');
            });

            $('.updateRegion').on('click', function() {
                var modal = $('#updateRegionModel');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=libelle]').val($(this).data('libelle'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
