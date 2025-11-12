@extends('layouts.layout-sinhvien')

@section('title', 'Hướng dẫn nộp học phí')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hướng dẫn nộp học phí</h3>
                    <p class="text-subtitle text-muted">Các phương thức nộp học phí</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sinh-vien.hoc-phi.index') }}">Học phí</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Hướng dẫn</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h4>Các phương thức nộp học phí</h4>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Vui lòng nộp học phí đúng hạn để tránh bị ảnh hưởng đến quá trình học tập.
                    </div>

                    <h5 class="mt-4">1. Nộp trực tiếp tại Phòng Tài vụ</h5>
                    <p>- Địa chỉ: Phòng Tài vụ - Tầng 1, Nhà A</p>
                    <p>- Thời gian: 8h00 - 17h00 (Thứ 2 - Thứ 6)</p>

                    <h5 class="mt-4">2. Chuyển khoản ngân hàng</h5>
                    <p><strong>Thông tin tài khoản:</strong></p>
                    <ul>
                        <li>Tên tài khoản: TRƯỜNG ĐẠI HỌC ABC</li>
                        <li>Số tài khoản: 0123456789</li>
                        <li>Ngân hàng: Vietcombank - Chi nhánh XYZ</li>
                        <li>Nội dung: MSSV - Họ tên - Học phí HK1 2024-2025</li>
                    </ul>

                    <h5 class="mt-4">3. Thanh toán qua QR Code</h5>
                    <p>Quét mã QR để thanh toán (Đang cập nhật)</p>
                </div>
            </div>
        </section>
    </div>
@endsection

