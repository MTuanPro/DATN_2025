@extends('layouts.layout-sinhvien')

@section('title', 'Trợ lý ảo AI')

@section('content')
    <div class="page-heading">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3><i class="bi bi-robot text-primary"></i> Trợ lý ảo AI</h3>
                <p class="text-muted mb-0">Hỗ trợ sinh viên 24/7 - Hỏi mọi thứ về học tập</p>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm chatbot-main-card"
                    style="height: 700px; display: flex; flex-direction: column; border-radius: 15px; overflow: hidden;">
                    <div class="card-header"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <div class="d-flex justify-content-between align-items-center text-white">
                            <div>
                                <h5 class="mb-1">
                                    <i class="bi bi-chat-dots-fill"></i> Chat với Trợ lý ảo
                                </h5>
                                <small class="opacity-75">Powered by AI - Luôn sẵn sàng giúp đỡ bạn</small>
                            </div>
                            <button class="btn btn-sm btn-light" id="btn-new-conversation">
                                <i class="bi bi-plus-circle"></i> Chat mới
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0 chatbot-container" id="chat-container" style="flex: 1; overflow-y: auto;">
                        <div class="text-center py-5 chatbot-welcome" id="welcome-screen">
                            <div class="mb-4">
                                <div class="bot-avatar-large mx-auto mb-3"
                                    style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-robot text-white" style="font-size: 3rem;"></i>
                                </div>
                                <h4 class="chatbot-welcome-title">Xin chào! 👋</h4>
                                <p class="chatbot-welcome-text">Tôi là trợ lý ảo AI. Hãy hỏi tôi bất cứ điều gì!</p>
                            </div>

                            <div class="row g-2 px-4">
                                <div class="col-md-6">
                                    <div class="quick-action-card" data-topic="Chương trình đào tạo">
                                        <i class="bi bi-book"></i>
                                        <div>Chương trình đào tạo</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="quick-action-card" data-topic="Đăng ký môn học">
                                        <i class="bi bi-calendar-check"></i>
                                        <div>Đăng ký môn học</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="quick-action-card" data-topic="Học phí">
                                        <i class="bi bi-cash-coin"></i>
                                        <div>Học phí</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="quick-action-card" data-topic="Điểm & Kết quả học tập">
                                        <i class="bi bi-trophy"></i>
                                        <div>Điểm số</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="messages-container" class="p-3"></div>
                    </div>

                    <div class="card-footer bg-transparent border-0 chatbot-footer" id="chat-input-container"
                        style="display: none;">
                        <form id="chat-form">
                            <div class="mb-2">
                                <select id="chu-de" class="form-select form-select-sm">
                                    <option value="">💡 Chọn chủ đề câu hỏi...</option>
                                    <option value="Chương trình đào tạo">📚 Chương trình đào tạo</option>
                                    <option value="Đăng ký môn học">📝 Đăng ký môn học</option>
                                    <option value="Lịch học & Lịch thi">📅 Lịch học & Lịch thi</option>
                                    <option value="Học phí">💰 Học phí</option>
                                    <option value="Điểm & Kết quả học tập">🏆 Điểm & Kết quả học tập</option>
                                    <option value="Quy chế đào tạo">📋 Quy chế đào tạo</option>
                                    <option value="Thủ tục hành chính">📄 Thủ tục hành chính</option>
                                    <option value="Khác">❓ Khác</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <input type="text" id="message-input" class="form-control chatbot-input"
                                    placeholder="Nhập câu hỏi của bạn..." required style="border-radius: 25px 0 0 25px;">
                                <button type="submit" class="btn btn-primary"
                                    style="border-radius: 0 25px 25px 0; padding: 0 25px;">
                                    <i class="bi bi-send-fill me-1"></i> Gửi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Suggested Questions --}}
                <div class="card shadow-sm mb-3 chatbot-sidebar-card" style="border-radius: 15px;">
                    <div class="card-header bg-gradient"
                        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none;">
                        <h6 class="mb-0 text-white">
                            <i class="bi bi-lightbulb-fill"></i> Câu hỏi gợi ý
                        </h6>
                    </div>
                    <div class="card-body chatbot-sidebar-body">
                        <div id="suggested-questions">
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted small mt-2">Đang tải...</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chat History --}}
                <div class="card shadow-sm chatbot-sidebar-card" style="border-radius: 15px;">
                    <div class="card-header bg-gradient"
                        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none;">
                        <h6 class="mb-0 text-white">
                            <i class="bi bi-clock-history"></i> Lịch sử chat
                        </h6>
                    </div>
                    <div class="card-body p-2 chatbot-sidebar-body chatbot-scrollbar"
                        style="max-height: 400px; overflow-y: auto;">
                        @forelse($conversations as $conv)
                            <div class="conversation-item p-2 mb-2" data-id="{{ $conv->id }}"
                                style="cursor: pointer; border-radius: 10px; transition: all 0.3s;">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-2">
                                        <div
                                            class="avatar-sm bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-chat-text text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small text-truncate chatbot-conv-title"
                                            style="max-width: 200px;">
                                            {{ $conv->tieu_de_chat ?? 'Cuộc trò chuyện #' . $conv->id }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-clock"></i> {{ $conv->ngay_bat_dau->diffForHumans() }}
                                            <span class="badge bg-info bg-gradient ms-1">{{ $conv->messages_count }}</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-danger btn-delete-conv" data-id="{{ $conv->id }}"
                                        title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted small mb-0">Chưa có lịch sử chat</p>
                            </div>
                        @endforelse

                        @if ($conversations->count() > 0)
                            <a href="{{ route('sinh-vien.chatbot.history') }}"
                                class="btn btn-sm btn-outline-primary w-100 mt-2">
                                <i class="bi bi-box-arrow-up-right"></i> Xem tất cả
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="current-conversation-id" value="{{ $activeConversation->id ?? '' }}">
@endsection

@push('scripts')
    <script>
        let currentConversationId = $('#current-conversation-id').val();

        // Apply dark mode to chat container
        function applyThemeToChat() {
            const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const chatContainer = document.getElementById('chat-container');

            if (isDarkMode) {
                chatContainer.style.background = 'linear-gradient(to bottom, #1a1d29 0%, #212529 100%)';
            } else {
                chatContainer.style.background = 'linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%)';
            }
        }

        // Apply theme on load
        $(document).ready(function() {
            applyThemeToChat();

            // Watch for theme changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'data-bs-theme') {
                        applyThemeToChat();
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-bs-theme']
            });
        });

        // Load suggested questions
        function loadSuggestedQuestions(chuDe = '') {
            $.get('/sinh-vien/chatbot/suggested-questions', {
                chu_de: chuDe
            }, function(response) {
                if (response.success && response.questions.length > 0) {
                    let html = '';
                    response.questions.forEach(function(q) {
                        html += `<div class="mb-2">
                    <button class="btn btn-sm btn-outline-primary w-100 text-start suggested-q" 
                            data-question="${q.cau_hoi_mau}" 
                            style="border-radius: 10px; transition: all 0.3s;">
                        <i class="bi bi-patch-question"></i> ${q.cau_hoi_mau}
                    </button>
                </div>`;
                    });
                    $('#suggested-questions').html(html);
                } else {
                    $('#suggested-questions').html(
                        '<p class="text-muted text-center small">Không có gợi ý cho chủ đề này</p>');
                }
            });
        }

        // Quick action cards click
        $(document).on('click', '.quick-action-card', function() {
            const topic = $(this).data('topic');
            $('#chu-de').val(topic);
            $('#btn-new-conversation').click();
            $('#welcome-screen').hide();
            $('#messages-container').show();
            $('#chat-input-container').show();
        });

        // Create new conversation
        $('#btn-new-conversation').click(function() {
            $.post('/sinh-vien/chatbot/conversation/create', {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.success) {
                    currentConversationId = response.conversation_id;
                    $('#current-conversation-id').val(currentConversationId);
                    $('#welcome-screen').hide();
                    $('#messages-container').html('').show();
                    $('#chat-input-container').show();
                    toastr.success('Cuộc trò chuyện mới đã được tạo!');
                    $('#message-input').focus();
                }
            });
        });

        // Send message
        $('#chat-form').submit(function(e) {
            e.preventDefault();

            if (!currentConversationId) {
                toastr.error('Vui lòng tạo cuộc trò chuyện mới!');
                $('#btn-new-conversation').click();
                return;
            }

            const message = $('#message-input').val().trim();
            const chuDe = $('#chu-de').val();

            if (!message) return;

            // Add user message to chat
            appendMessage('user', message);
            $('#message-input').val('');

            // Show typing indicator
            showTypingIndicator();

            // Send to server
            $.post('/sinh-vien/chatbot/message/send', {
                _token: '{{ csrf_token() }}',
                conversation_id: currentConversationId,
                message: message,
                chu_de: chuDe
            }, function(response) {
                hideTypingIndicator();

                if (response.success) {
                    appendMessage('bot', response.bot_message.noi_dung, response.bot_message.id, response
                        .bot_message.similarity);
                }
            }).fail(function() {
                hideTypingIndicator();
                appendMessage('bot', 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau! 😔');
            });
        });

        // Append message to chat
        function appendMessage(sender, content, messageId = null, similarity = null) {
            const time = new Date().toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const isBot = sender === 'bot';

            let html = `<div class="mb-3 d-flex ${isBot ? '' : 'justify-content-end'} animate__animated animate__fadeIn">
        <div class="message-bubble ${isBot ? 'bot-message' : 'user-message'}" 
             style="max-width: 75%;">`;

            if (isBot) {
                html += `<div class="d-flex align-items-start">
            <div class="bot-avatar me-2">
                <i class="bi bi-robot"></i>
            </div>
            <div class="flex-grow-1">
                <div class="message-content">${content}</div>
                <div class="message-time">${time}</div>`;

                if (messageId) {
                    html += `<div class="feedback-section mt-2">
                <span class="text-muted small me-2">Câu trả lời này có hữu ích không?</span>
                <button class="btn btn-sm btn-outline-success btn-feedback" data-id="${messageId}" data-rating="huu_ich" title="Hữu ích">
                    <i class="bi bi-hand-thumbs-up"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger btn-feedback" data-id="${messageId}" data-rating="khong_huu_ich" title="Không hữu ích">
                    <i class="bi bi-hand-thumbs-down"></i>
                </button>`;

                    if (similarity) {
                        html += `<span class="badge bg-info ms-2">${similarity}% khớp</span>`;
                    }

                    html += `</div>`;
                }

                html += `</div></div>`;
            } else {
                html += `<div class="message-content">${content}</div>
            <div class="message-time text-end">${time}</div>`;
            }

            html += `</div></div>`;

            $('#messages-container').append(html);
            scrollToBottom();
        }

        // Typing indicator
        function showTypingIndicator() {
            const html = `<div class="typing-indicator-wrapper mb-3">
        <div class="d-flex align-items-start">
            <div class="bot-avatar me-2">
                <i class="bi bi-robot"></i>
            </div>
            <div class="typing-indicator">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>`;
            $('#messages-container').append(html);
            scrollToBottom();
        }

        function hideTypingIndicator() {
            $('.typing-indicator-wrapper').remove();
        }

        // Submit feedback with reason
        $(document).on('click', '.btn-feedback', function() {
            const messageId = $(this).data('id');
            const rating = $(this).data('rating');
            const $btn = $(this);
            const $feedbackSection = $btn.closest('.feedback-section');

            // Show reason modal if not helpful
            if (rating === 'khong_huu_ich') {
                showFeedbackModal(messageId, rating, $btn, $feedbackSection);
            } else {
                submitFeedback(messageId, rating, '', $btn, $feedbackSection);
            }
        });

        function showFeedbackModal(messageId, rating, $btn, $feedbackSection) {
            const modalHtml = `
        <div class="modal fade" id="feedbackModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-chat-left-text"></i> Góp ý cải thiện
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Vui lòng cho chúng tôi biết lý do để cải thiện chatbot:</p>
                        <div class="mb-3">
                            <label class="form-label">Chọn lý do:</label>
                            <select class="form-select" id="feedback-reason-select">
                                <option value="">-- Chọn lý do --</option>
                                <option value="Câu trả lời không chính xác">Câu trả lời không chính xác</option>
                                <option value="Câu trả lời không đầy đủ">Câu trả lời không đầy đủ</option>
                                <option value="Câu trả lời không liên quan">Câu trả lời không liên quan</option>
                                <option value="Ngôn ngữ khó hiểu">Ngôn ngữ khó hiểu</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="mb-3" id="custom-reason-container" style="display: none;">
                            <label class="form-label">Lý do cụ thể:</label>
                            <textarea class="form-control" id="feedback-reason-text" rows="3" 
                                      placeholder="Nhập lý do của bạn..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Góp ý thêm (tùy chọn):</label>
                            <textarea class="form-control" id="feedback-suggestion" rows="2" 
                                      placeholder="Bạn mong muốn câu trả lời như thế nào?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-danger" id="submit-feedback-btn">
                            <i class="bi bi-send"></i> Gửi góp ý
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

            // Remove old modal if exists
            $('#feedbackModal').remove();
            $('body').append(modalHtml);

            const modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
            modal.show();

            // Show/hide custom reason text area
            $('#feedback-reason-select').change(function() {
                if ($(this).val() === 'Khác') {
                    $('#custom-reason-container').slideDown();
                } else {
                    $('#custom-reason-container').slideUp();
                }
            });

            // Submit feedback
            $('#submit-feedback-btn').click(function() {
                let reason = $('#feedback-reason-select').val();

                if (!reason) {
                    toastr.error('Vui lòng chọn lý do!');
                    return;
                }

                if (reason === 'Khác') {
                    reason = $('#feedback-reason-text').val().trim();
                    if (!reason) {
                        toastr.error('Vui lòng nhập lý do!');
                        return;
                    }
                }

                const suggestion = $('#feedback-suggestion').val().trim();
                if (suggestion) {
                    reason += ' | Góp ý: ' + suggestion;
                }

                submitFeedback(messageId, rating, reason, $btn, $feedbackSection);
                modal.hide();
            });
        }

        function submitFeedback(messageId, rating, reason, $btn, $feedbackSection) {
            $.post('/sinh-vien/chatbot/feedback', {
                _token: '{{ csrf_token() }}',
                message_id: messageId,
                danh_gia: rating,
                ly_do: reason
            }, function(response) {
                if (response.success) {
                    $btn.addClass('active').siblings('.btn-feedback').prop('disabled', true);
                    $feedbackSection.find('.text-muted').html(
                        `<i class="bi bi-check-circle text-success"></i> 
                 <span class="text-success">Cảm ơn phản hồi của bạn!</span>`
                    );
                    toastr.success(rating === 'huu_ich' ? 'Cảm ơn đánh giá tích cực! 😊' :
                        'Cảm ơn góp ý! Chúng tôi sẽ cải thiện. 🙏');
                }
            });
        }

        // Suggested question click
        $(document).on('click', '.suggested-q', function() {
            const question = $(this).data('question');
            $('#message-input').val(question);

            if (!currentConversationId) {
                $('#btn-new-conversation').click();
                setTimeout(() => {
                    $('#chat-form').submit();
                }, 500);
            } else {
                $('#chat-form').submit();
            }
        });

        // Load conversation
        $('.conversation-item').click(function() {
            const convId = $(this).data('id');
            window.location.href = `/sinh-vien/chatbot/conversation/${convId}`;
        });

        // Delete conversation (using event delegation)
        $(document).on('click', '.btn-delete-conv', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const convId = $(this).data('id');
            const $btn = $(this);

            if (confirm('Bạn có chắc chắn muốn xóa cuộc trò chuyện này?')) {
                // Disable button để tránh click nhiều lần
                $btn.prop('disabled', true);

                $.ajax({
                    url: `/sinh-vien/chatbot/conversation/${convId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message || 'Đã xóa cuộc hội thoại');
                            // Reload trang sau 500ms
                            setTimeout(() => location.reload(), 500);
                        } else {
                            toastr.error(response.error || 'Không thể xóa cuộc trò chuyện');
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        let errorMsg = 'Có lỗi xảy ra khi xóa';

                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        } else if (xhr.status === 404) {
                            errorMsg = 'Cuộc hội thoại không tồn tại hoặc đã bị xóa';
                        } else if (xhr.status === 403) {
                            errorMsg = 'Bạn không có quyền xóa cuộc hội thoại này';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Lỗi hệ thống, vui lòng thử lại sau';
                        }

                        toastr.error(errorMsg);
                        $btn.prop('disabled', false);
                    }
                });
            }
        });

        function scrollToBottom() {
            const container = document.getElementById('chat-container');
            container.scrollTop = container.scrollHeight;
        }

        // Load suggested questions on page load
        loadSuggestedQuestions();

        // Change suggested questions when topic changes
        $('#chu-de').change(function() {
            loadSuggestedQuestions($(this).val());
        });

        // Auto-show input if there's active conversation
        @if ($activeConversation)
            $('#welcome-screen').hide();
            $('#messages-container').show();
            $('#chat-input-container').show();
            // Load messages
            $.get('/sinh-vien/chatbot/conversation/{{ $activeConversation->id }}/messages', function(response) {
                if (response.success) {
                    response.messages.forEach(function(msg) {
                        appendMessage(msg.nguoi_gui, msg.noi_dung, msg.id, msg.similarity);
                    });
                }
            });
        @endif
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        /* Dark Mode Variables */
        :root {
            --chatbot-bg-gradient-start: #f8f9fa;
            --chatbot-bg-gradient-end: #e9ecef;
            --chatbot-card-bg: #ffffff;
            --chatbot-text: #212529;
            --chatbot-text-muted: #6c757d;
            --chatbot-border: #dee2e6;
            --chatbot-hover-bg: #f8f9fa;
            --chatbot-shadow: rgba(0, 0, 0, 0.1);
            --chatbot-shadow-hover: rgba(0, 0, 0, 0.15);
            --bot-message-bg: #ffffff;
            --conversation-hover-start: #f8f9fa;
            --conversation-hover-end: #e9ecef;
            --scrollbar-track: rgba(0, 0, 0, 0.05);
        }

        [data-bs-theme="dark"] {
            --chatbot-bg-gradient-start: #1e2139;
            --chatbot-bg-gradient-end: #252941;
            --chatbot-card-bg: #2a2f4a;
            --chatbot-text: #f0f2f5;
            --chatbot-text-muted: #b8bdc9;
            --chatbot-border: #3d4363;
            --chatbot-hover-bg: #363c5c;
            --chatbot-shadow: rgba(0, 0, 0, 0.5);
            --chatbot-shadow-hover: rgba(102, 126, 234, 0.3);
            --bot-message-bg: linear-gradient(135deg, #363c5c 0%, #2e3449 100%);
            --conversation-hover-start: #363c5c;
            --conversation-hover-end: #2e3449;
            --scrollbar-track: rgba(255, 255, 255, 0.05);
            --select-bg: #2a2f4a;
            --select-border: #3d4363;
        }

        /* Chat Container Background */
        .chatbot-container {
            background: linear-gradient(to bottom, var(--chatbot-bg-gradient-start) 0%, var(--chatbot-bg-gradient-end) 100%) !important;
        }

        .card-body.chatbot-container {
            background: linear-gradient(to bottom, var(--chatbot-bg-gradient-start) 0%, var(--chatbot-bg-gradient-end) 100%) !important;
        }

        /* Chat Footer */
        .chatbot-footer {
            background-color: var(--chatbot-card-bg) !important;
            border-color: var(--chatbot-border) !important;
        }

        .chatbot-input {
            background-color: #ffffff !important;
            color: #212529 !important;
            border-color: var(--chatbot-border) !important;
        }

        .chatbot-input,
        .chatbot-input:hover,
        .chatbot-input:active {
            background-color: #ffffff !important;
            color: #212529 !important;
        }

        .chatbot-input::placeholder {
            color: var(--chatbot-text-muted) !important;
            opacity: 0.7;
        }

        /* Select dropdown styling */
        #chu-de {
            background-color: var(--chatbot-card-bg) !important;
            color: var(--chatbot-text) !important;
            border-color: var(--chatbot-border) !important;
        }

        #chu-de option {
            background-color: #ffffff !important;
            color: #212529 !important;
            padding: 8px;
        }

        #chu-de:focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
        }

        /* Button "Chat mới" */
        [data-bs-theme="dark"] .btn-light {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border-color: rgba(255, 255, 255, 0.2);
        }

        [data-bs-theme="dark"] .btn-light:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Quick Action Cards */
        .quick-action-card {
            background: var(--chatbot-card-bg) !important;
            padding: 22px;
            border-radius: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid var(--chatbot-border) !important;
            box-shadow: 0 3px 10px var(--chatbot-shadow);
            position: relative;
            overflow: hidden;
        }

        .quick-action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .quick-action-card:hover::before {
            opacity: 1;
        }

        .quick-action-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 10px 25px var(--chatbot-shadow-hover);
            border-color: #667eea !important;
        }

        [data-bs-theme="dark"] .quick-action-card:hover {
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            border-color: #667eea !important;
        }

        .quick-action-card i {
            font-size: 2.2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            display: block;
            position: relative;
            z-index: 1;
        }

        .quick-action-card div {
            font-weight: 600;
            color: var(--chatbot-text) !important;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }

        /* Message Bubbles */
        .message-bubble {
            position: relative;
            animation: fadeIn 0.3s ease-in;
        }

        .bot-message {
            background: var(--bot-message-bg) !important;
            padding: 14px 18px;
            border-radius: 18px 18px 18px 4px;
            box-shadow: 0 3px 10px var(--chatbot-shadow);
            color: var(--chatbot-text) !important;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        [data-bs-theme="dark"] .bot-message {
            background: linear-gradient(135deg, #363c5c 0%, #2e3449 100%) !important;
            border: 1px solid rgba(102, 126, 234, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .user-message {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 14px 18px;
            border-radius: 18px 18px 4px 18px;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        [data-bs-theme="dark"] .user-message {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5);
        }

        .message-content {
            white-space: pre-line;
            line-height: 1.6;
            word-wrap: break-word;
            color: inherit !important;
            font-size: 0.95rem;
        }

        .bot-message .message-content {
            color: var(--chatbot-text) !important;
        }

        [data-bs-theme="dark"] .bot-message .message-content {
            color: #f0f2f5 !important;
        }

        .user-message .message-content {
            color: white !important;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.75;
            margin-top: 6px;
            color: var(--chatbot-text-muted) !important;
        }

        [data-bs-theme="dark"] .message-time {
            opacity: 0.65;
        }

        /* Bot Avatar */
        .bot-avatar {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .bot-avatar i {
            color: white;
            font-size: 1.2rem;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .avatar-sm i {
            font-size: 0.9rem;
        }

        /* Typing Indicator */
        .typing-indicator {
            background: var(--bot-message-bg) !important;
            padding: 15px 20px;
            border-radius: 18px 18px 18px 4px;
            display: inline-flex;
            gap: 6px;
            box-shadow: 0 3px 10px var(--chatbot-shadow);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        [data-bs-theme="dark"] .typing-indicator {
            background: linear-gradient(135deg, #363c5c 0%, #2e3449 100%) !important;
            border: 1px solid rgba(102, 126, 234, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        .typing-indicator span {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            animation: typing 1.4s infinite;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.5);
        }

        .typing-indicator span:nth-child(1) {
            animation-delay: 0s;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.7;
            }

            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }

        /* Feedback Section */
        .feedback-section {
            padding-top: 10px;
            margin-top: 8px;
            border-top: 1px solid var(--chatbot-border) !important;
        }

        .btn-feedback {
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.85rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
        }

        .btn-feedback i {
            font-size: 1rem;
            display: inline-block;
        }

        .btn-feedback:hover {
            transform: scale(1.08);
        }

        .btn-feedback.active {
            box-shadow: 0 0 0 3px rgba(var(--bs-success-rgb), 0.3);
            transform: scale(1.05);
        }

        .btn-feedback.active.btn-outline-danger {
            box-shadow: 0 0 0 3px rgba(var(--bs-danger-rgb), 0.3);
        }

        [data-bs-theme="dark"] .btn-feedback {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.15);
            color: #f0f2f5;
        }

        [data-bs-theme="dark"] .btn-feedback:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        [data-bs-theme="dark"] .btn-outline-success {
            color: #51cf66;
            border-color: #51cf66;
        }

        [data-bs-theme="dark"] .btn-outline-success:hover {
            background-color: rgba(81, 207, 102, 0.1);
            color: #51cf66;
        }

        [data-bs-theme="dark"] .btn-outline-danger {
            color: #ff6b7f;
            border-color: #ff6b7f;
        }

        [data-bs-theme="dark"] .btn-outline-danger:hover {
            background-color: rgba(255, 107, 127, 0.1);
            color: #ff6b7f;
        }

        /* Delete Button in Conversation */
        [data-bs-theme="dark"] .btn-delete-conv {
            background-color: rgba(220, 53, 69, 0.15);
            border-color: rgba(220, 53, 69, 0.3);
            color: #ff6b7f;
        }

        [data-bs-theme="dark"] .btn-delete-conv:hover {
            background-color: rgba(220, 53, 69, 0.3);
            border-color: rgba(220, 53, 69, 0.5);
            color: #ff8594;
        }

        /* Primary Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
            color: white !important;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #7a8ef5 0%, #8a5bb2 100%) !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5) !important;
            transform: translateY(-1px);
            color: white !important;
        }

        .btn-primary:focus,
        .btn-primary:active {
            background: linear-gradient(135deg, #5568d3 0%, #653b8a 100%) !important;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.5) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
            color: white !important;
        }

        [data-bs-theme="dark"] .btn-primary:hover {
            background: linear-gradient(135deg, #7a8ef5 0%, #8a5bb2 100%) !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5) !important;
            transform: translateY(-1px);
            color: white !important;
        }

        [data-bs-theme="dark"] .btn-primary:focus,
        [data-bs-theme="dark"] .btn-primary:active {
            background: linear-gradient(135deg, #5568d3 0%, #653b8a 100%) !important;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.5) !important;
            color: white !important;
        }

        /* Conversation Items */
        .conversation-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: var(--chatbot-card-bg) !important;
            color: var(--chatbot-text) !important;
            border: 1px solid transparent;
        }

        .conversation-item:hover {
            background: linear-gradient(to right, var(--conversation-hover-start), var(--conversation-hover-end)) !important;
            transform: translateX(6px);
            border-color: rgba(102, 126, 234, 0.3);
            box-shadow: 0 2px 8px var(--chatbot-shadow);
        }

        [data-bs-theme="dark"] .conversation-item:hover {
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.2);
        }

        .conversation-item .text-muted {
            color: var(--chatbot-text-muted) !important;
        }

        /* Suggested Questions */
        .suggested-q {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: left;
            font-size: 0.88rem;
            background-color: var(--chatbot-card-bg) !important;
            color: var(--chatbot-text) !important;
            border-color: var(--chatbot-border) !important;
            font-weight: 500;
            padding: 10px 14px;
        }

        .suggested-q:hover {
            transform: translateX(8px) scale(1.02);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border-color: #667eea !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        [data-bs-theme="dark"] .suggested-q:hover {
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
        }

        /* Scrollbar Styling */
        #chat-container::-webkit-scrollbar {
            width: 6px;
        }

        #chat-container::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
            border-radius: 10px;
        }

        #chat-container::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        #chat-container::-webkit-scrollbar-thumb:hover {
            background: #667eea;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Card Gradient Headers */
        .bg-gradient {
            position: relative;
            overflow: hidden;
        }

        .bg-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Input Focus */
        #message-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Placeholder Text */
        .chatbot-input::placeholder {
            color: var(--chatbot-text-muted) !important;
            opacity: 0.7;
        }

        [data-bs-theme="dark"] .chatbot-input::placeholder {
            color: #8891a3 !important;
            opacity: 0.8;
        }

        /* Small Text */
        [data-bs-theme="dark"] small,
        [data-bs-theme="dark"] .small {
            color: var(--chatbot-text-muted) !important;
        }

        /* Feedback Question Text */
        [data-bs-theme="dark"] .feedback-section {
            border-top-color: rgba(102, 126, 234, 0.2) !important;
        }

        [data-bs-theme="dark"] .feedback-section small {
            color: #b8bdc9 !important;
        }

        /* Empty State */
        .text-muted {
            color: var(--chatbot-text-muted) !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #b8bdc9 !important;
        }

        /* Loading Spinner */
        [data-bs-theme="dark"] .spinner-border {
            color: #8b9ff5 !important;
        }

        /* Card Headers with Gradient */
        [data-bs-theme="dark"] .card-header {
            border-bottom-color: rgba(102, 126, 234, 0.2) !important;
        }

        /* Empty Chat Icon */
        [data-bs-theme="dark"] .bi-inbox {
            color: #6c7a8f !important;
        }

        /* Sidebar Cards */
        .chatbot-sidebar-card {
            background-color: var(--chatbot-card-bg) !important;
            border-color: var(--chatbot-border) !important;
        }

        .chatbot-sidebar-body {
            background-color: var(--chatbot-card-bg) !important;
        }

        .chatbot-conv-title {
            color: var(--chatbot-text) !important;
        }

        /* Main Chat Card */
        .chatbot-main-card {
            background-color: var(--chatbot-card-bg) !important;
            border-color: var(--chatbot-border) !important;
        }

        .chatbot-main-card .card-body {
            background-color: transparent !important;
        }

        /* Welcome Screen */
        .chatbot-welcome {
            color: var(--chatbot-text) !important;
        }

        .chatbot-welcome-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        /* Fallback for browsers that don't support background-clip */
        @supports not (-webkit-background-clip: text) {
            .chatbot-welcome-title {
                color: #667eea !important;
            }
        }

        [data-bs-theme="dark"] .chatbot-welcome-title {
            background: linear-gradient(135deg, #8b9ff5 0%, #9b7bc4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .chatbot-welcome-text {
            color: var(--chatbot-text-muted) !important;
            font-size: 1.08rem;
            font-weight: 500;
        }

        /* Scrollbar for sidebar */
        .chatbot-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .chatbot-scrollbar::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
            border-radius: 10px;
        }

        .chatbot-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .chatbot-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #667eea;
        }

        /* Page Heading */
        .page-heading h3 {
            font-weight: 700;
            color: var(--chatbot-text) !important;
        }

        [data-bs-theme="dark"] .page-heading h3 {
            color: #f0f2f5 !important;
        }

        .page-heading h3 i {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-heading p {
            color: var(--chatbot-text-muted) !important;
        }

        [data-bs-theme="dark"] .page-heading p {
            color: #b8bdc9 !important;
        }

        /* Badges */
        [data-bs-theme="dark"] .badge {
            background-color: rgba(102, 126, 234, 0.25) !important;
            color: #e9ecef !important;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }

        [data-bs-theme="dark"] .badge.bg-info {
            background-color: rgba(13, 202, 240, 0.25) !important;
            color: #a8e7ff !important;
            border: 1px solid rgba(13, 202, 240, 0.3);
        }

        [data-bs-theme="dark"] .badge.bg-success {
            background-color: rgba(25, 135, 84, 0.25) !important;
            color: #a3e2c4 !important;
            border: 1px solid rgba(25, 135, 84, 0.3);
        }

        /* Similarity Score Badge */
        .similarity-badge {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-block;
        }

        [data-bs-theme="dark"] .similarity-badge {
            background-color: rgba(102, 126, 234, 0.2);
            color: #a8b5ff;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .quick-action-card {
                padding: 18px;
            }

            .quick-action-card i {
                font-size: 1.8rem;
            }

            .message-bubble {
                max-width: 85% !important;
            }
        }
    </style>
@endpush
