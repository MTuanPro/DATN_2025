@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
@endphp

{{-- Minimal chatbot floating widget component (safe: checks Route::has before using route()) --}}
<div id="chatbot-widget" style="position:fixed;right:20px;bottom:20px;z-index:1050;">
    <div class="card shadow-sm" style="width:240px;">
        <div class="card-body p-2">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <strong>AI Chat Bot</strong>
                    <div class="small text-muted">Trợ lý ảo</div>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('chatbot-widget').style.display='none'">×</button>
                </div>
            </div>

            <hr class="my-2" />

            <div class="d-grid gap-1">
                {{-- Student entry point --}}
                @if(Route::has('sinh-vien.chatbot.index') && Auth::check() && in_array('sinh_vien', Auth::user()->vaiTro()->pluck('ma_vai_tro')->toArray()))
                    <a href="{{ route('sinh-vien.chatbot.index') }}" class="btn btn-primary btn-sm">Mở Chat</a>
                @endif

                {{-- Admin shortcuts if available --}}
                @if(Auth::check() && in_array('admin', Auth::user()->vaiTro()->pluck('ma_vai_tro')->toArray()))
                    @if(Route::has('admin.ai-chatbot.knowledge-base.index'))
                        <a href="{{ route('admin.ai-chatbot.knowledge-base.index') }}" target="_blank" class="btn btn-outline-primary btn-sm">KB quản trị</a>
                    @endif
                    @if(Route::has('admin.ai-chatbot.feedback.index'))
                        <a href="{{ route('admin.ai-chatbot.feedback.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm">Phản hồi</a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
