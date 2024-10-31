<!-- admin/partials/logs_table.blade.php -->
@foreach ($logs as $log)
    <tr>
        <td>{{ $log->id }}</td>
        <td><span class="badge {{ $log->status === 'success' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($log->status) }}</span></td>
        <td>{{ $log->message }}</td>
        <td>{{ $log->error_details ?? 'N/A' }}</td>
        <td>{{ $log->request_time }}</td>
    </tr>
@endforeach
