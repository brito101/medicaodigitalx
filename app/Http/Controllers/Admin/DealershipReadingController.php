<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DealershipReadingRequest;
use App\Http\Requests\Admin\Settings\DealershipRequest;
use App\Models\Apartment;
use App\Models\ApartmentReport;
use App\Models\Block;
use App\Models\Complex;
use App\Models\DealershipReading;
use App\Models\Notification;
use App\Models\Settings\Dealership;
use App\Models\Views\Apartment as ViewsApartment;
use App\Models\Views\ApartmentReport as ViewsApartmentReport;
use App\Models\Views\DealershipReading as ViewsDealershipReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DataTables;

class DealershipReadingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()->hasPermissionTo('Listar Leitura das Concessionárias')) {
            abort(403, 'Acesso não autorizado');
        }

        $readings = ViewsDealershipReading::query();
        if ($request->ajax()) {
            return Datatables::of($readings)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-xs btn-primary mx-1 shadow" title="Editar" href="dealerships-readings/' . $row->id . '/edit"><i class="fa fa-lg fa-fw fa-pen"></i></a>' . '<a class="btn btn-xs btn-danger mx-1 shadow" title="Excluir" href="dealerships-readings/destroy/' . $row->id . '" onclick="return confirm(\'Confirma a exclusão desta leitura?\')"><i class="fa fa-lg fa-fw fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.dealerships-readings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->hasPermissionTo('Criar Leitura das Concessionárias')) {
            abort(403, 'Acesso não autorizado');
        }

        $complexes = Complex::all('id', 'alias_name');
        $dealerships = Dealership::where('service', 'Água e Esgoto')->get();

        return view('admin.dealerships-readings.create', compact('complexes', 'dealerships'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DealershipReadingRequest $request)
    {
        if (!Auth::user()->hasPermissionTo('Criar Leitura das Concessionárias')) {
            abort(403, 'Acesso não autorizado');
        }

        $complex = Complex::where('id', $request['complex_id'])->first();
        if (empty($complex->id)) {
            abort(403, 'Acesso não autorizado');
        }

        $dealership = Dealership::where('id', $request['dealership_id'])->first();
        if (empty($dealership->id)) {
            abort(403, 'Acesso não autorizado');
        }

        $data = $request->all();

        $data['editor'] = Auth::user()->id;

        $reading = DealershipReading::create($data);

        if ($reading->save()) {
            return redirect()
                ->route('admin.dealerships-readings.index')
                ->with('success', 'Cadastro realizado!');
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar!');
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        if (!Auth::user()->hasPermissionTo('Editar Leitura das Concessionárias')) {
            abort(403, 'Acesso não autorizado');
        }

        $reading = DealershipReading::where('id', $id)->first();
        if (empty($reading->id)) {
            abort(403, 'Acesso não autorizado');
        }

        $complexes = Complex::all('id', 'alias_name');
        $dealerships = Dealership::where('service', 'Água e Esgoto')->get();

        $reports = ViewsApartmentReport::where('dealership_reading_id', $id)->get();
        if ($request->ajax()) {
            return Datatables::of($reports)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.dealerships-readings.edit', compact('reading', 'complexes', 'dealerships', 'reports'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DealershipReadingRequest $request, $id)
    {
        if (!Auth::user()->hasPermissionTo('Editar Leitura das Concessionárias')) {
            abort(403, 'Acesso não autorizado');
        }

        $reading = DealershipReading::where('id', $id)->first();
        if (empty($reading->id)) {
            abort(403, 'Acesso não autorizado');
        }

        $complex = Complex::where('id', $request['complex_id'])->first();
        if (empty($complex->id)) {
            abort(403, 'Acesso não autorizado');
        }

        $dealership = Dealership::where('id', $request['dealership_id'])->first();
        if (empty($dealership->id)) {
            abort(403, 'Acesso não autorizado');
        }

        $data = $request->all();

        $data['editor'] = Auth::user()->id;

        if ($reading->update($data)) {
            return redirect()
                ->route('admin.dealerships-readings.index')
                ->with('success', 'Cadastro realizado!');
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao cadastrar!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermissionTo('Excluir Leitura das Concessionárias')) {
            abort(403, 'Acesso não autorizado');
        }

        $reading = DealershipReading::where('id', $id)->first();

        if (empty($reading->id)) {
            abort(403, 'Acesso não autorizado');
        }

        $reports = $reading->apartmentReports;

        if ($reading->delete()) {
            if ($reports->count() > 0) {
                foreach ($reports as $report) {
                    $report->delete();
                }
            }
            return redirect()
                ->back()
                ->with('success', 'Exclusão realizada!');
        } else {
            return redirect()
                ->back()
                ->with('error', 'Erro ao excluir!');
        }
    }

    public function batchDelete(Request $request)
    {
        if (!Auth::user()->hasPermissionTo('Excluir Leitura das Concessionárias')) {
            abort(403, 'Acesso não autorizado');
        }

        if (!$request->ids) {
            return redirect()
                ->back()
                ->with('error', 'Selecione ao menos uma linha!');
        }

        $ids = explode(",", $request->ids);

        foreach ($ids as $id) {
            $reading = DealershipReading::where('id', $id)->first();

            if (empty($reading->id)) {
                abort(403, 'Acesso não autorizado');
            }

            $reading->delete();
            ApartmentReport::where('dealership_reading_id', $id)->delete();
            Notification::where('apartment_id', $id)->delete();
        }

        return redirect()
            ->route('admin.dealerships-readings.index')
            ->with('success', 'Contas das Concessionárias excluídas!');
    }
}
