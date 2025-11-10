@extends('layouts.layout-daotao')

@section('title', 'Báo cáo Đào tạo')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Báo cáo Đào tạo</h3>
                    <p class="text-subtitle text-muted">Hệ thống báo cáo và thống kê đầy đủ</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="row">
                    @foreach($reportTypes as $report)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xl bg-{{ $report['color'] }} me-3">
                                                <i class="{{ $report['icon'] }} text-white" style="font-size: 2rem;"></i>
                                            </div>
                                            <div class="name flex-grow-1">
                                                <h5 class="mb-1">{{ $report['title'] }}</h5>
                                                <p class="text-muted mb-0 small">{{ $report['description'] }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route($report['route']) }}" class="btn btn-{{ $report['color'] }} btn-block">
                                                <i class="bi bi-eye"></i> Xem báo cáo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
