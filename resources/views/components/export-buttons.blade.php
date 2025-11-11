{{-- Component: Nút xuất báo cáo --}}
{{-- Usage: <x-export-buttons report-type="sinh-vien" /> --}}
@props(['reportType' => ''])

<div class="btn-group">
    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-download me-1"></i> Xuất báo cáo
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="#" onclick="exportReport(event, 'excel')">
                <i class="bi bi-file-earmark-excel text-success me-2"></i>
                Xuất Excel
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" onclick="exportReport(event, 'pdf')">
                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                Xuất PDF
            </a>
        </li>
    </ul>
</div>
