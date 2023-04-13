@extends('adminlte::page')

@section('title', ' - ' . $schedule->title)

@section('content')

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-fw fa-calendar-check"></i> Agendamento #{{ $schedule->id }} - Criado por
                        {{ $schedule->user->name }} em {{ date('d/m/Y H:i', strtotime($schedule->created_at)) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reading-schedule.index') }}">Agendamentos de
                                Leituras</a></li>
                        <li class="breadcrumb-item active">Agendamento</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    @include('components.alert')

                    <div class="card">
                        <div class="card-header bg-{{ $schedule->color }}">
                            <h3 class="card-title">Dados Cadastrais do Agendamento de Leitura</h3>
                        </div>

                        <div class="card-body">

                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="col-12 col-md-6 form-group px-0 pr-md-2">
                                    <label for="title">Agendamento</label>
                                    <input type="text" class="form-control bg-white" id="title" name="title"
                                        value="{{ $schedule->title }}" disabled>
                                </div>
                                <div class="col-12 col-md-3 form-group px-0 px-md-2">
                                    <label for="start">Início</label>
                                    <input type="datetime-local" class="form-control bg-white" id="start" name="start"
                                        value="{{ $schedule->start }}" disabled>
                                </div>
                                <div class="col-12 col-md-3 form-group px-0 pl-md-2">
                                    <label for="end">Término</label>
                                    <input type="datetime-local" class="form-control bg-white" id="end" name="end"
                                        value="{{ $schedule->end }}" disabled>
                                </div>
                            </div>

                            @if ($schedule->guests->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Participantes</th>
                                                <th scope="col" class="text-center">Visto</th>
                                                <th scope="col" class="text-center">Executado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($schedule->guests as $guest)
                                                <tr>
                                                    <th>{{ $guest->user->name }} ({{ $guest->user->email }})</th>
                                                    <th class="text-center">{!! $guest->visualized == 0
                                                        ? '<i class="fas fa-lg fa-thumbs-down text-danger"></i>'
                                                        : '<i class="fas fa-lg fa-thumbs-up text-success"></i>' !!}
                                                    </th>
                                                    <th class="text-center">{!! $guest->executed == 0
                                                        ? '<i class="fas fa-lg fa-thumbs-down text-danger"></i>'
                                                        : '<i class="fas fa-lg fa-thumbs-up text-success"></i>' !!}
                                                    </th>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </div>

                        @if ($schedule->user_id == Auth::user()->id)
                            <div class="card-footer d-flex flex-wrap justify-content-start">
                                <a href="{{ route('admin.reading-schedule.edit', ['reading_schedule' => $schedule->id]) }}"
                                    class="btn btn-primary">Editar</a>
                                <form method="POST"
                                    action="{{ route('admin.reading-schedule.destroy', ['reading_schedule' => $schedule->id]) }}"
                                    class="ml-2">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
