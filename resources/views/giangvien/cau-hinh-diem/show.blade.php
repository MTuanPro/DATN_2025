@extends('layouts.layout-giangvien')

@section('title', 'Chi tiết cấu hình điểm')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Cấu hình đầu điểm</h3>
                <p class="text-subtitle text-muted">{{ $lopHocPhan->ma_lop_hp }} - {{ $lopHocPhan->monHoc->ten_mon }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('giangvien.cau-hinh-diem.index') }}">Cấu hình điểm</a></li>
                        <li class="breadcrumb-item active">Chi tiết</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Thông tin lớp -->
    <section class="section">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="bi bi-info-circle"></i> Thông tin lớp học phần</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Mã lớp:</th>
                                <td><strong>{{ $lopHocPhan->ma_lop_hp }}</strong></td>
                            </tr>
                            <tr>
                                <th>Môn học:</th>
                                <td>{{ $lopHocPhan->monHoc->ma_mon }} - {{ $lopHocPhan->monHoc->ten_mon }}</td>
                            </tr>
                            <tr>
                                <th>Số tín chỉ:</th>
                                <td>{{ $lopHocPhan->monHoc->so_tin_chi }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Học kỳ:</th>
                                <td>{{ $lopHocPhan->hocKy->ten_hoc_ky }} - {{ $lopHocPhan->hocKy->nam_hoc }}</td>
                            </tr>
                            <tr>
                                <th>Tổng tỷ lệ:</th>
                                <td>
                                    @if($tongTyLe == 100)
                                        <span class="badge bg-success">{{ $tongTyLe }}%</span>
                                    @elseif($tongTyLe > 0)
                                        <span class="badge bg-warning">{{ $tongTyLe }}%</span>
                                    @else
                                        <span class="badge bg-secondary">0%</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    @if($hoanThien)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Hoàn thiện</span>
                                    @else
                                        <span class="badge bg-warning"><i class="bi bi-exclamation-triangle"></i> Chưa hoàn thiện</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Danh sách cấu hình -->
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Danh sách đầu điểm</h5>
                @if($tongTyLe < 100)
                    <a href="{{ route('giangvien.cau-hinh-diem.create', $lopHocPhan->id) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm đầu điểm
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($cauHinhs->isEmpty())
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Chưa có cấu hình đầu điểm nào. Nhấn "Thêm đầu điểm" để bắt đầu.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">STT</th>
                                    <th>Tên đầu điểm</th>
                                    <th width="120" class="text-center">Tỷ lệ (%)</th>
                                    <th width="120" class="text-center">Số cột</th>
                                    <th width="180" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $stt = 0;
                                @endphp
                                @foreach($cauHinhs as $ch)
                                    @if($ch->so_cot > 1)
                                        @for($cot = 1; $cot <= $ch->so_cot; $cot++)
                                            @php
                                                $stt++;
                                                $tyLeMoi = number_format($ch->ty_le / $ch->so_cot, 2);
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $stt }}</td>
                                                <td><strong>{{ $ch->ten_dau_diem }} {{ $cot }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">{{ $tyLeMoi }}%</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">1 cột</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($cot == 1)
                                                        <a href="{{ route('giangvien.cau-hinh-diem.edit', $ch->id) }}" 
                                                            class="btn btn-sm btn-warning" title="Sửa">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete({{ $ch->id }})" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                        <form id="delete-form-{{ $ch->id }}" 
                                                            action="{{ route('giangvien.cau-hinh-diem.destroy', $ch->id) }}" 
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                    @else
                                        @php
                                            $stt++;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $stt }}</td>
                                            <td><strong>{{ $ch->ten_dau_diem }}</strong></td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $ch->ty_le }}%</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $ch->so_cot }} cột</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('giangvien.cau-hinh-diem.edit', $ch->id) }}" 
                                                    class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete({{ $ch->id }})" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $ch->id }}" 
                                                    action="{{ route('giangvien.cau-hinh-diem.destroy', $ch->id) }}" 
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="2" class="text-end">Tổng cộng:</th>
                                    <th class="text-center">
                                        <span class="badge {{ $hoanThien ? 'bg-success' : 'bg-warning' }}">
                                            {{ $tongTyLe }}%
                                        </span>
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif

                @if(!$hoanThien && !$cauHinhs->isEmpty())
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Lưu ý:</strong> Tổng tỷ lệ hiện tại là {{ $tongTyLe }}%. 
                        Cần đủ 100% để hoàn thiện cấu hình. Còn lại: <strong>{{ 100 - $tongTyLe }}%</strong>
                    </div>
                @endif

                @if($hoanThien)
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle"></i> 
                        <strong>Hoàn thiện!</strong> Cấu hình đầu điểm đã đủ 100%. Bạn có thể bắt đầu nhập điểm.
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('giangvien.cau-hinh-diem.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Bạn có chắc muốn xóa cấu hình này không?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
@endsection
