<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Preferencia ID</th>
      <th>Estado</th>
      <th>Método</th>
      <th>Tipo</th>
      <th>Monto</th>
      <th>Estudiante</th>
      <th>Grado/Grupo</th>
      <th>Matrícula</th>
      <th>Creado</th>
      <th>Actualizado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($payments as $key => $payment)
      <tr>
        <td>{{$key+1}}</td>
        <td>{{$payment->preference_id}}</td>
        <td>{{$payment->status}}</td>
        <td>{{$payment->payment_method}}</td>
        <td>{{$payment->payment_type}}</td>
        <td>{{$payment->amount}}</td>
        <td>{{$payment->student->last_name_father}} {{$payment->student->last_name_mother}}, {{$payment->student->name}}</td>
        <td>{{$payment->student->academicGroup->grade->description}} {{$payment->student->academicGroup->section->description}}</td>
        <td>{{$payment->student->enrollment}}</td>
        <td>{{$payment->created_at}}</td>
        <td>{{$payment->updated_at}}</td>
      </tr>
    @endforeach
  </tbody>
</table>
