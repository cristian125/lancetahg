@extends('template')

@section('body')
<div class="container my-5">
    <h1 class="text-center mb-4" style="font-weight: bold; color: #2c3e50;">Alcaldías y Municipios Envío Local</h1>
    <div class="table-responsive">
        <table class="table table-hover table-bordered text-center">
            <thead style="background-color: #3498db; color: white;">
                <tr>
                    <th scope="col">ZONA</th>
                    <th scope="col">ALCALDÍA-MUNICIPIO</th>
                    <th scope="col">ESTADO</th>
                    <th scope="col">MÍNIMO DE COMPRA</th>
                    <th scope="col">COSTO FLETE</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #ecf0f1;">
                    <td>1A</td>
                    <td>
                        <ul class="list-unstyled mb-0">
                            <li>Benito Juárez</li>
                            <li>Cuauhtémoc</li>
                            <li>Iztacalco</li>
                            <li>Venustiano Carranza</li>
                        </ul>
                    </td>
                    <td>CDMX</td>
                    <td>$800</td>
                    <td>$150 + IVA</td>
                </tr>
                <tr>
                    <td>1B</td>
                    <td>
                        <ul class="list-unstyled mb-0">
                            <li>Azcapotzalco</li>
                            <li>Coyoacán</li>
                        </ul>
                    </td>
                    <td>CDMX</td>
                    <td>$1,500</td>
                    <td>$150 + IVA</td>
                </tr>
                <tr style="background-color: #ecf0f1;">
                    <td>1C</td>
                    <td>
                        <ul class="list-unstyled mb-0">
                            <li>Álvaro Obregón</li>
                            <li>Gustavo A. Madero</li>
                            <li>Miguel Hidalgo</li>
                        </ul>
                    </td>
                    <td>CDMX</td>
                    <td>$2,000</td>
                    <td>$230 + IVA</td>
                </tr>
                <tr>
                    <td>1D</td>
                    <td>
                        <ul class="list-unstyled mb-0">
                            <li>Magdalena C.</li>
                            <li>Tlalpan</li>
                        </ul>
                    </td>
                    <td>CDMX</td>
                    <td>$2,500</td>
                    <td>$230 + IVA</td>
                </tr>
                <tr style="background-color: #ecf0f1;">
                    <td>1E</td>
                    <td>
                        <ul class="list-unstyled mb-0">
                            <li>Cuajimalpa</li>
                            <li>Xochimilco</li>
                            <li>Huixquilucan</li>
                            <li>Naucalpan</li>
                            <li>Tlalnepantla</li>
                        </ul>
                    </td>
                    <td>CDMX<br>EdoMex</td>
                    <td>$3,000</td>
                    <td>$250 + IVA</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
