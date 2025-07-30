@extends('layouts.app')

@section('title', 'التقارير اليومية')

@push('styles')
<style>
    .report-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px #0001;
    }
    .report-table th, .report-table td {
        padding: 12px 16px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }
    .report-table th {
        background: linear-gradient(90deg, #667eea, #764ba2);
        color: #fff;
        font-weight: bold;
    }
    .report-table tr:last-child td {
        border-bottom: none;
    }
    .report-date-row {
        background: #f3f4f6;
        font-weight: bold;
        color: #764ba2;
        font-size: 1.1em;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6 text-center">التقارير اليومية (يومية الصندوق)</h1>
    <div class="overflow-x-auto">
        <table class="report-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>المبلغ</th>
                    <th>الرقم المرجعي</th>
                </tr>
            </thead>
            <tbody>
            @forelse($grouped as $date => $rows)
                <tr class="report-date-row">
                    <td colspan="5">{{ $date }}</td>
                </tr>
                @foreach($rows as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['type'] }}</td>
                    <td>{{ $row['subtype'] }}</td>
                    <td>{{ number_format($row['amount'], 2) }}</td>
                    <td>{{ $row['ref'] }}</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="5">لا توجد بيانات لعرضها.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
