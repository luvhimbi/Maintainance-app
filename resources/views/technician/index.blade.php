@extends('Layouts.TechnicianNavBar')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- User List Sidebar -->
        <div class="col-md-4 col-lg-3 p-0 bg-white border-end">
            <div class="d-flex flex-column  h-100">
                <!-- Chat Header -->
                <div class="p-4 bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fas fa-comment-dots me-2"></i>Messages
                        </h5>
                        <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="modal" data-bs-target="#newChatModal" style="width: 32px; height: 32px;">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                 <!-- User List -->
                <div class="flex-grow-1 overflow-auto">
                    @forelse($users as $user)
                    <a href="#" class="list-group-item list-group-item-action border-0 user-item d-flex justify-content-between align-items-center py-3 px-3" 
                       data-id="{{ $user->user_id }}" style="border-bottom: 1px solid #f0f0f0;">
                        <div class="d-flex align-items-center">
                            <div class="position-relative">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->username) }}&background=random&size=64" 
                                     class="rounded-circle me-3" width="48" height="48" style="object-fit: cover;">
                                <span class="position-absolute bottom-0 start-75 translate-middle p-1 bg-{{ $user->is_online ? 'success' : 'secondary' }} border border-2 border-white rounded-circle">
                                    <span class="visually-hidden">Online status</span>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-semibold">{{ $user->username }}</h6>
                                    <small class="text-muted">{{ $user->last_message_time ? $user->last_message_time->format('h:i A') : '' }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted text-truncate" style="max-width: 180px;">
                                        @if($user->last_message)
                                            {{ Str::limit($user->last_message, 25) }}
                                        @else
                                            Start a new conversation
                                        @endif
                                    </small>
                                    <span class="unread-count badge bg-danger rounded-pill {{ $user->unread_count ? '' : 'd-none' }}">
                                        {{ $user->unread_count }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-5">
                        <div class="mb-3" style="font-size: 3rem; color: #e0e0e0;">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <p class="text-muted">No conversations yet</p>
                        <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#newChatModal">
                            Start a new chat
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 col-lg-9 p-0 d-flex flex-column" style="background-color: #f5f7fb;">
            <!-- Chat Header -->
            <div class="d-flex justify-content-between align-items-center p-3 bg-white border-bottom" id="chat-header" style="box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center">
                    <i class="fas fa-arrow-left me-3 d-md-none cursor-pointer" id="back-to-users" style="font-size: 1.1rem;"></i>
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-3">
                            <img id="current-chat-avatar" src="" class="rounded-circle" width="40" height="40">
                            <span id="current-chat-status" class="position-absolute bottom-0 start-75 translate-middle p-1 bg-secondary border border-2 border-white rounded-circle"></span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" id="current-chat-user">Select a user to chat</h5>
                            <small class="text-muted" id="user-status">Offline</small>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light rounded-circle" type="button" id="chatMenu" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width: 200px;">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-info-circle me-2"></i>View Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash me-2"></i>Delete Chat</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Messages Container -->
            <div class="flex-grow-1 p-4 overflow-auto" id="messages-container" style="background-color: #f5f7fb;">
                <div class="text-center py-5" id="no-selection">
                    <div class="mb-3" style="font-size: 3rem; color: #e0e0e0;">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <h5 class="text-muted mb-2">Select a conversation</h5>
                    <p class="text-muted small">Choose a user from the sidebar to start chatting</p>
                </div>
                
                <!-- Messages will be loaded here dynamically -->
                <div id="messages-list" class="d-none">
                    <!-- Messages will be appended here -->
                </div>
            </div>
            
          <!-- Message Form -->
<div class="p-3 bg-white border-top d-none" id="message-form">
    <form id="send-message-form" class="d-flex align-items-center">
        @csrf
        <input type="hidden" name="receiver_id" id="receiver_id">
        
        <!-- Message Input (takes full available width) -->
        <input type="text" 
               name="message" 
               class="form-control rounded-pill me-2" 
               placeholder="Type your message..." 
               required
               style="flex-grow: 1;">
        
        <!-- Send Button -->
        <button type="submit" 
                class="btn btn-primary rounded-circle" 
                style="width: 40px; height: 40px;">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold">New Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 border-bottom">
                    <div class="input-group rounded-pill shadow-sm">
                        <span class="input-group-text bg-white border-0 ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-0" placeholder="Search users...">
                    </div>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @foreach($users as $user)
                    <a href="#" class="list-group-item list-group-item-action border-0 user-select py-3 px-4" data-id="{{ $user->user_id }}">
                        <div class="d-flex align-items-center">
                            <div class="position-relative me-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->username) }}&background=random&size=64" 
                                     class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                                <span class="position-absolute bottom-0 start-75 translate-middle p-1 bg-{{ $user->is_online ? 'success' : 'secondary' }} border border-2 border-white rounded-circle">
                                    <span class="visually-hidden">Online status</span>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold">{{ $user->username }}</h6>
                                <small class="text-muted">{{ ucfirst($user->user_role) }}</small>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Template (Hidden) -->
<div id="message-template" class="d-none">
    <div class="message-container mb-4">
        <div class="message-bubble rounded-3 p-3 position-relative">
            <div class="message-text"></div>
            <div class="message-time mt-2 text-end">
                <small><i class="far fa-clock me-1"></i><span class="time"></span></small>
                <i class="fas fa-check-double ms-2 text-primary seen-indicator d-none"></i>
            </div>
            <div class="message-arrow"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
 :root {
    --primary-color: #4361ee;
    --sent-bubble: #e3f2fd;
    --received-bubble: #ffffff;
    --sent-text: #0d47a1;
    --received-text: #212121;
    --time-color: #9e9e9e;
    --online-color: #4caf50;
    --offline-color: #9e9e9e;
    --unread-badge: #f44336;
}

/* Main Layout */
#messages-container {
    background-color: #f5f7fb;
    height: calc(100vh - 160px);
    padding: 1.5rem;
    overflow-y: auto;
    scroll-behavior: smooth;
}

/* Message Components */
.message-container {
    width: 100%;
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
}

.message-bubble {
    max-width: 70%;
    position: relative;
    padding: 12px 16px;
    border-radius: 18px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    word-wrap: break-word;
    line-height: 1.5;
}

.message-bubble.sent {
    background-color: var(--sent-bubble);
    margin-left: auto;
    color: var(--sent-text);
    border-bottom-right-radius: 4px;
}

.message-bubble.received {
    background-color: var(--received-bubble);
    margin-right: auto;
    color: var(--received-text);
    border-bottom-left-radius: 4px;
    border: 1px solid #f0f0f0;
}

.message-bubble.sent::after {
    content: '';
    position: absolute;
    right: -8px;
    top: 0;
    width: 0;
    height: 0;
    border: 12px solid transparent;
    border-left-color: var(--sent-bubble);
    border-right: 0;
    border-top: 0;
    margin-top: 0;
    margin-right: -8px;
}

.message-bubble.received::after {
    content: '';
    position: absolute;
    left: -8px;
    top: 0;
    width: 0;
    height: 0;
    border: 12px solid transparent;
    border-right-color: var(--received-bubble);
    border-left: 0;
    border-top: 0;
    margin-top: 0;
    margin-left: -8px;
}

.message-time {
    font-size: 0.75rem;
    color: var(--time-color);
    margin-top: 6px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.seen-indicator {
    font-size: 0.7rem;
}

/* User List Styling */
.user-item {
    transition: all 0.2s ease;
    background-color: white;
}

.user-item:hover {
    background-color: #f8f9fa;
}

.user-item.active {
    background-color: #f0f7ff;
}
.message.unread {
    background-color: #f8f9fa;
    border-left: 3px solid #4361ee;
}
.unread-count {
    font-size: 0.7rem;
    padding: 0.25em 0.6em;
    background-color: var(--unread-badge);
}

/* Chat Header */
#chat-header {
    background-color: white;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 1rem 1.5rem;
    z-index: 10;
}

/* Message Form */
#message-form {
    background-color: white;
    border-top: 1px solid rgba(0,0,0,0.05);
    padding: 1rem;
    z-index: 10;
}

/* Scrollbar styling */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for new messages */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message-container {
    animation: fadeIn 0.3s ease-out forwards;
}

/* Date divider */
.date-divider {
    display: flex;
    align-items: center;
    margin: 1.5rem 0;
    color: #9e9e9e;
    font-size: 0.8rem;
}

.date-divider::before, .date-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e0e0e0;
    margin: 0 1rem;
}

/* Typing indicator */
.typing-indicator {
    display: inline-flex;
    padding: 8px 12px;
    background-color: white;
    border-radius: 18px;
    border: 1px solid #f0f0f0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.typing-dot {
    width: 6px;
    height: 6px;
    background-color: #9e9e9e;
    border-radius: 50%;
    margin: 0 2px;
    animation: typingAnimation 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) {
    animation-delay: 0s;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typingAnimation {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-5px); }
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .col-md-4.col-lg-3 {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 1050;
        left: 0;
        top: 0;
        transition: transform 0.3s ease;
    }
    
    .col-md-4.col-lg-3.hidden {
        transform: translateX(-100%);
    }
    
    .message-bubble {
        max-width: 85%;
    }
    
    #messages-container {
        padding: 1rem;
    }
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
.badge-pulse {
    animation: pulse 1.5s infinite;
}
</style>
@endpush

@push('scripts')
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Pusher
        const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true
        });

        // Subscribe to private channel
        const channel = pusher.subscribe('chat.{{ auth()->id() }}');

        // Listen for new messages
        channel.bind('NewChatMessage', function(data) {
            const currentReceiver = $('#receiver_id').val();
            
            if (data.sender_id == currentReceiver) {
                addMessage(data);
                markAsRead(data.id);
            } else {
                updateUnreadCount(data.sender_id);
                showNewMessageNotification(data);
            }
        });

        // User selection
        $('.user-item, .user-select').click(function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            const userName = $(this).find('h6').text();
            const userRole = $(this).find('.badge').text().trim();
            
            startChat(userId, userName, userRole);
            const modal = bootstrap.Modal.getInstance(document.getElementById('newChatModal'));
            modal.hide();
        });

        // Send message
        $('#send-message-form').submit(function(e) {
            e.preventDefault();
            const message = $(this).find('input[name="message"]').val().trim();
            
            if (message) {
                sendMessage(message);
            }
        });

        // Back button for mobile
        $('#back-to-users').click(function() {
            $('.user-list').show();
            $('.chat-area').hide();
        });

        // Search functionality
        $('#chatSearch').keyup(function() {
            const searchText = $(this).val().toLowerCase();
            $('.user-item').each(function() {
                const userName = $(this).find('h6').text().toLowerCase();
                $(this).toggle(userName.includes(searchText));
            });
        });

        function startChat(userId, userName, userRole) {
            // Update UI
            $('.user-item').removeClass('active');
            $(`.user-item[data-id="${userId}"]`).addClass('active');
            $('#current-chat-user').html(`${userName} <span class="badge bg-${userRole === 'admin' ? 'success' : 'info'}">${userRole}</span>`);
            $('#receiver_id').val(userId);
            $('#message-form').removeClass('d-none');
            $('#no-selection').addClass('d-none');
            $(`.user-item[data-id="${userId}"] .unread-count`).addClass('d-none');
            
            // For mobile view
            $('.user-list').hide();
            $('.chat-area').show();
            
            // Load messages
            loadMessages(userId);
        }


       function addMessage(message) {
    const isSender = message.sender_id == {{ auth()->id() }};
    const messageClass = isSender ? 'sent' : 'received';
    const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    // Get sender name - assumes message has sender object with username property
    const senderName = isSender ? 'You' : (message.sender ? message.sender.username : 'User');
    
    const messageHtml = `
        <div class="message-container d-flex flex-column ${isSender ? 'align-items-end' : 'align-items-start'} mb-3">
            <small class="text-muted mb-1">${senderName}</small>
            <div class="message-bubble ${messageClass} ">
                <div class="message-text">${message.message}</div>
                <div class="message-time d-flex justify-content-${isSender ? 'end' : 'start'} mt-1">
                    ${time}
                    ${isSender && message.read ? '<i class="fas fa-check-double text-primary ms-2"></i>' : ''}
                </div>
            </div>
        </div>
    `;
    
    $('#messages-container').append(messageHtml);
    scrollToBottom();
    
    if (!isSender && !message.read) {
        markAsRead(message.id);
    }
}

  function loadMessages(userId) {
    $.ajax({
        url: `/chat/messages/${userId}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                $('#messages-container').empty();
                response.messages.forEach(message => {
                    addMessage(message);
                });
                scrollToBottom();
            } else {
                console.error('Error loading messages:', response.message);
                showAlert('Failed to load messages', 'danger');
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseJSON);
            showAlert('Connection error. Please try again.', 'danger');
        }
    });
}

function sendMessage(messageText) {
    const formData = $('#send-message-form').serialize();
    
    $.ajax({
        url: '/chat/send',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                addMessage(response.message);
                $('#send-message-form')[0].reset();
            } else {
                console.error('Error sending message:', response.message);
                showAlert('Failed to send message', 'danger');
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseJSON);
            showAlert('Failed to send message. Please try again.', 'danger');
            
            // Optional: Retry logic
            if(confirm('Message failed to send. Retry?')) {
                sendMessage(messageText);
            }
        }
    });
}

        function markAsRead(messageId) {
            $.post('/chat/read/' + messageId, {_token: '{{ csrf_token() }}'});
        }

        function updateUnreadCount(senderId) {
            const userItem = $(`.user-item[data-id="${senderId}"]`);
            let unreadCount = parseInt(userItem.find('.unread-count').text()) || 0;
            userItem.find('.unread-count').text(unreadCount + 1).removeClass('d-none');
        }

        function showNewMessageNotification(data) {
            // You can implement desktop notifications here
            if (Notification.permission === "granted") {
                new Notification(`New message from ${data.sender.username}`, {
                    body: data.message
                });
            }
        }

        function scrollToBottom() {
            const container = $('#messages-container');
            container.scrollTop(container[0].scrollHeight);
        }

        // Request notification permission
        if (window.Notification && Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    });
</script>
@endpush