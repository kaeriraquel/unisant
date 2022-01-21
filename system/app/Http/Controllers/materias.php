<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class materias extends Controller
{
  public function addmateria(Request $r){
    \App\listamaterias::create([
      "name" => $r->name,
      "creditos" => $r->creditos
    ]);
    return redirect("/academicos/listamaterias");
  }

  public function borrar(Request $r)
  {
    $eliminar=\App\listamaterias::findOrFail($r->id);
    $eliminar->delete();
    return redirect("/academicos/listamaterias");
  }
}
