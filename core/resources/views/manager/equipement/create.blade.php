@extends('manager.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-30">
            <div class="card">
                <div class="card-body">
                    {!! Form::open([
                        'route' => ['manager.traca.producteur.equipementstore'],
                        'method' => 'POST',
                        'class' => 'form-horizontal',
                        'id' => 'flocal',
                        'enctype' => 'multipart/form-data',
                    ]) !!}
                    <div class="col-xs-12 col-sm-12">
                        <table class="table table-striped table-bordered">
                            <tbody id="insectesParasites_area">

                                <tr>
                                    <td class="row">
                                        <div class="col-xs-12 col-sm-12 bg-success">
                                            <badge class="btn  btn-outline--warning h-45 btn-sm text-white">
                                                @lang('Autres arbres à ombrages ne figurant pas dans la liste précedente')
                                            </badge>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="form-group row">
                                                {{ Form::label(__('Nom'), null, ['class' => 'control-label']) }}
                                                <input type="text" name="arbreStrate[0][nom]" id="nom-1"
                                                    class="form-control" placeholder="Nom de l'arbre à ombrage">
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="form-group row">
                                                <label>Strate</label>
                                                <select class="form-control strate" name="arbreStrate[0][strate]"
                                                    id="strate-1">
                                                    <option value="">Selectionne une strate</option>
                                                    <option value="1">Strate 1</option>
                                                    <option value="2">Strate 2</option>
                                                    <option value="3">Strate 3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="form-group row">
                                                {{ Form::label(__('Nombre'), null, ['class' => 'control-label']) }}
                                                <input type="number" name="arbreStrate[0][qte]" id="qte-1"
                                                    class="form-control" placeholder="Saisissez le nombre d' arbre observé">
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
    <x-back route="{{ route('manager.suivi.menage.index') }}" />
@endpush

@push('script')
    <script type="text/javascript">
        $("#localite").chained("#section");
        $("#producteur").chained("#localite");
        $(document).ready(function() {
            $('#avoirMachine').hide();

        });
    </script>
@endpush
