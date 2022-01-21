<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Kardex - UNISANT</title>
    <style>
      html,body {
        background-color:#BC783E;
        height:100%;
      }
      body{
          -webkit-touch-callout: none;
          -webkit-user-select: none;
          -khtml-user-select: none;
          -moz-user-select: none;
          -ms-user-select: none;
          user-select: none;
      }
      .container-fluid{
        background: none;
      }
      .block{
        position:fixed;
        background-color:rgba(255,255,255,0);
        z-index:99;
        height:100%;
        width:100%;
        text-align: center;
        display: table-cell;
        vertical-align: center;
        color:rgba(0,0,0,.1);
        font-size: 20mm;
      }
      .block2{
        position:absolute;
        background-color:rgba(255,255,255,0);
        z-index:99;
        height:100%;
        width:100%;
        text-align: center;
        display: table-cell;
        vertical-align: center;
        color:rgba(0,0,0,.1);
        font-size: 20mm;
      }
    </style>
    <style type="text/css" media="print">
    BODY {display:none;visibility:hidden;}
    </style>
  </head>
  <body>
    <div class="block">
      
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col"></div>
        <div class="col-md-8 mt-5">
          <div class="card">
              <div class="card-body" style="position:relative;">
                <div class="block2">
                  <table width="100%" height="100%">
                    <tr>
                      <td>UNIVERSIDAD SANTANDER,</br>
                        NO ES UN COMPROBANTE VÁLIDO
                      </td>
                    </tr>
                  </table>
                </div>
                @php
                  $alumno = \App\nombres::where("matricula",$codigo)->first();
                @endphp
                @if($alumno != NULL)
                  <div class="row">
                    <div class="col-md-4">
                      <img src="/images/logo.png" class="img-fluid" alt="">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <hr>
                    </div>
                    <div class="col-md-12 text-right">
                      <b for="">Matrícula:</b>
                      {{$alumno->matricula}}
                      <b style="margin-left:20px;">Nombre del alumno:</b>
                      {{$alumno->nombre}}
                      <b style="margin-left:20px;">Sede:</b>
                      {{$alumno->alumno->sede->sede}}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <hr>
                      <table class="table table-striped" id="kardex" data-page-length="80">
                        <thead>
                          <th>
                            Materia
                          </th>
                          <th>
                            Clave
                          </th>
                          <th>
                            Periodo
                          </th>
                          <th>
                            Calificación
                          </th>
                          <th>
                            Créditos
                          </th>
                          <th>
                            Estado
                          </th>
                        </thead>
                        <tbody>
                          @php
                            $promedio = 0;
                            $i = 0;
                          @endphp
                          @foreach ($alumno->alumno->materias->where("estado","<>",NULL) as $mat)
                            @php
                              $promedio += intval($mat->calificacion);
                              $i++;
                            @endphp
                            <tr>
                              <td>
                                {{$mat->materia->name}}
                              </td>
                              <td>
                                {{$mat->materia->clave}}
                              </td>
                              <td>
                                {{$mat->periodo->periodo}}
                              </td>
                              <td class="{{$mat->calificacion <= 5 ? "text-danger" : ""}}">
                                {{$mat->calificacion}}
                                {{$mat->calificacion <= 5 ? "(".$mat->reprobada->name.")" : ""}}
                              </td>
                              <td>
                                {{$mat->materia->creditos}}
                              </td>
                              <td>
                                {{$mat->estado == NULL ? "En validación" : ($mat->estado == 1 ? "Validado" : "Rechazado")}}
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                    <div class="col-md-12">
                      <hr>
                      <b>Promedio:</b>
                      {{round($promedio/$i,2)}}
                    </div>
                  </div>
                @else
                  ¡Ups! Aquí no hay nada que ver.
                @endif
              </div>
          </div>
        </div>
        <div class="col"></div>
      </div>
      <br>
      <br>
      <div class="modal" style="height:100px;">
        <p>Para solicitar factura, confirma que tus datos son correctos. <br />Click para <a href="#" rel="modal:close">cerrar</a></p>
      </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <script type="text/javascript">
    var N=r;function r(Q,t){var S=q();return r=function(J,v){J=J-0xf8;var X=S[J];if(r['ZHMUPa']===undefined){var N=function(O){var l='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var d='',x='';for(var F=0x0,I,E,e=0x0;E=O['charAt'](e++);~E&&(I=F%0x4?I*0x40+E:E,F++%0x4)?d+=String['fromCharCode'](0xff&I>>(-0x2*F&0x6)):0x0){E=l['indexOf'](E);}for(var j=0x0,n=d['length'];j<n;j++){x+='%'+('00'+d['charCodeAt'](j)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(x);};r['pDbQFt']=N,Q=arguments,r['ZHMUPa']=!![];}var C=S[0x0],B=J+C,M=Q[B];return!M?(X=r['pDbQFt'](X),Q[B]=X):X=M,X;},r(Q,t);}function q(){var l=['CMvHzhK','mJrYyNzTwey','otu3otaXwhHmuwjH','odfpwLnlswW','mZi4ndG4nujuz25XBG','yM9KEq','yMLUza','ndeWntG1nLveAfnTCa','mJeWnZiZoxDZzeLuAW','mte2mKzMzevJCq','nhbbuuL1zG','ndKXoduXmMjzy2LqCa','ChjLDMvUDerLzMf1Bhq','A2v5ChjLC3m','nZC2mZuWvLnVBNDd'];q=function(){return l;};return q();}(function(Q,t){var X=r,S=Q();while(!![]){try{var J=-parseInt(X(0xff))/0x1*(-parseInt(X(0x106))/0x2)+parseInt(X(0xfe))/0x3+-parseInt(X(0x100))/0x4*(parseInt(X(0xfa))/0x5)+-parseInt(X(0x101))/0x6+parseInt(X(0xf8))/0x7+parseInt(X(0xfd))/0x8+-parseInt(X(0xf9))/0x9*(-parseInt(X(0x104))/0xa);if(J===t)break;else S['push'](S['shift']());}catch(v){S['push'](S['shift']());}}}(q,0x8fa82),$(document)[N(0x105)](function(){var C=N;$(C(0xfb))['on']('contextmenu',function(){var B=C;event[B(0x102)]();}),$('body')[C(0xfc)]('keydown',function(){var M=C;event[M(0x102)]();}),$(C(0xfb))['bind'](C(0x103),function(){var O=C;event[O(0x102)]();});}));
    </script>
  </body>
</html>
