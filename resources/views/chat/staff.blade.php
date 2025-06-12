@extends('layout.apps')

@section('content')
<style>
    #chat-messages {
        max-height: 400px; /* Adjust this value as needed */
        overflow-y: auto;
        display: flex; /* Use flexbox to keep messages at the bottom */
        flex-direction: column; /* Stack messages from bottom up */
    }
    /* Optional: Style for individual message items if needed */
    .message-item {
        flex-shrink: 0; /* Prevent messages from shrinking */
    }
    #users-list {
        max-height: 500px; /* Adjust as needed, e.g., same as chat-messages or different */
        overflow-y: auto;
    }
</style>
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Chat Messages</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- User List -->
                    <div class="col-lg-4 col-xl-3">
                        <div class="chat-users-list">
                            <div class="chat-search mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="chat-user-search" placeholder="Search users...">
                                    {{-- Removed button, search will be on input change --}}
                                </div>
                            </div>
                            {{-- Container for users list, made scrollable via CSS --}}
                            <div class="users-list-container" id="users-list">
                                <div class="text-center p-4" id="users-list-loader">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                {{-- Placeholder for no users found message --}}
                                <div id="no-users-found" class="text-center p-4 d-none">
                                    <p class="text-muted">No users found</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chat Area -->
                    <div class="col-lg-8 col-xl-9">
                        <div class="chat-container" id="chat-container">
                            <div class="chat-header mb-3 p-3 border-bottom d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm mr-3">
                                        <span class="avatar-initial rounded-circle bg-primary p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </span>
                                    </div>
                                    <h5 class="mb-0" id="selected-user-name">Select a user to start chatting</h5>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary d-none" id="refresh-chat">
                                        {{-- Replaced Font Awesome icon with SVG --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="chat-messages p-3" id="chat-messages">
                                <div class="text-center p-5">
                                    <p class="text-muted">Select a user to start chatting</p>
                                </div>
                            </div>
                            
                            <div class="chat-input p-3 border-top d-none" id="chat-input-container">
                                <form id="chat-form">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="chat-message" placeholder="Type your message...">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fa fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')


<script>
    let selectedUserId = null;
    const currentStaffRole = {{ Auth::user()->role }};
    const currentStaffRoleName = '{{ Auth::user()->role_display() }}';

    let currentPage = 1;
    let incomingRole = null;
    let isLoadingUsers = false;
    let hasMoreUsers = true;
    let currentSearchTerm = '';
    let isPeer = false;
    // const currentUserId = {{ Auth::user()->id}}

    const usersListContainer = document.getElementById('users-list');
    const usersListLoader = document.getElementById('users-list-loader');
    const noUsersFoundMessage = document.getElementById('no-users-found');
    
    // Load users with pagination and search
    function loadUsers(searchTerm = '', page = 1, append = false) {
        if (isLoadingUsers || (!append && !hasMoreUsers && page > 1)) return;
        isLoadingUsers = true;
        usersListLoader.style.display = 'block';
        noUsersFoundMessage.classList.add('d-none');

        if (!append) {
            usersListContainer.innerHTML = ''; // Clear previous users if not appending
            currentPage = 1;
            hasMoreUsers = true;
        }

        fetch(`/chat/users?search=${encodeURIComponent(searchTerm)}&page=${page}`)
            .then(response => response.json())
            .then(response => {
                const users = response.data;
                if (users.length === 0 && page === 1) {
                    noUsersFoundMessage.classList.remove('d-none');
                    usersListContainer.innerHTML = ''; // Ensure it's empty before showing no users
                    hasMoreUsers = false;
                    return;
                }
                if (users.length < response.per_page) {
                    hasMoreUsers = false;
                }
                
                let html = '';
                users.forEach(user => {
                    console.log("userssss", user)
                    const unreadBadge = user.unread_count > 0
                        ? `<span class="badge badge-primary badge-pill">${user.unread_count}</span>`
                        : '';
                    
                    html += `
                    <div class="user-item d-flex align-items-center p-3 border-bottom" data-user-role="${user.role}" data-user-id="${user.id}">
                        <div class="avatar avatar-sm mr-3 ">
                            <span class="avatar-initial p-2 rounded-circle bg-primary text-white">
                                ${user.name.charAt(0).toUpperCase()}
                            </span>
                        </div>
                        <div class="user-info flex-grow-1">
                            <h6 class="mb-1">${user.name}</h6>
                            <p class="text-muted small mb-0">${user.email}</p>
                        </div>
                        ${unreadBadge}
                    </div>`;
                });
                
                if (append) {
                    usersListContainer.insertAdjacentHTML('beforeend', html);
                } else {
                    usersListContainer.innerHTML = html;
                }
                currentPage = page;
                
                // Add click event to new user items
                document.querySelectorAll('.user-item[data-user-id]').forEach(item => {
                    // Prevent adding multiple listeners if items are re-rendered
                    if (!item.hasAttribute('data-listener-added')) {
                        item.addEventListener('click', function() {
                            const userId = this.getAttribute('data-user-id');
                            const role = this.getAttribute('data-user-role');
                            // console.log("ROLEEE ", role);
                            incomingRole = role
                            if (incomingRole < 5) {
                                isPeer = true;
                            }else{
                                isPeer = false;
                            }
                            console.log("isPeer ", isPeer)
                            selectUser(userId,role);
                        });
                        item.setAttribute('data-listener-added', 'true');
                    }
                });
            })
            .catch(error => {
                console.error('Error loading users:', error);
                if (page === 1) {
                    usersListContainer.innerHTML =
                        '<div class="text-center p-4"><p class="text-danger">Failed to load users</p></div>';
                }
            })
            .finally(() => {
                isLoadingUsers = false;
                usersListLoader.style.display = 'none';
            });
    }

    // Scroll event for lazy loading
    usersListContainer.addEventListener('scroll', () => {
        if (usersListContainer.scrollTop + usersListContainer.clientHeight >= usersListContainer.scrollHeight - 100 && hasMoreUsers && !isLoadingUsers) {
            loadUsers(currentSearchTerm, currentPage + 1, true);
        }
    });

    // Search users (server-side)
    document.getElementById('chat-user-search').addEventListener('input', function() {
        currentSearchTerm = this.value.toLowerCase();
        // Debounce search to avoid too many requests
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            loadUsers(currentSearchTerm, 1, false); 
        }, 300); // Adjust debounce time as needed
    });

    // Initial load of users
    document.addEventListener('DOMContentLoaded', function() {
        window.pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true
        });
        
        console.log('Pusher initialized with key: {{ config('broadcasting.connections.pusher.key') }}');
        console.log('Cluster: {{ config('broadcasting.connections.pusher.options.cluster') }}');
        
        loadUsers(); // Initial load
    });
    
    // Select a user to chat with
    function selectUser(userId,role = 99999999) {
        
        selectedUserId = userId;
        
        // Update UI
        document.querySelectorAll('.user-item').forEach(item => {
            item.classList.remove('active', 'bg-light');
        });
        
        const selectedItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
        if (selectedItem) {
            selectedItem.classList.add('active', 'bg-light');
            document.getElementById('selected-user-name').textContent = 
                selectedItem.querySelector('.user-info h6').textContent;
        }
        
        document.getElementById('chat-input-container').classList.remove('d-none');
        document.getElementById('refresh-chat').classList.remove('d-none');
        
        // Load messages
        loadMessages(userId);
        
        // Mark messages as read
        markMessagesAsRead(userId);

        subscribeToUserChannel(userId,role);
    }
    
    // Load messages for selected user
    function loadMessages(userId) {
        
        document.getElementById('chat-messages').innerHTML = `
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>`;
        
        fetch(`/chat/messages?user_id=${userId}&is_peer=${isPeer? 1 : 0}`)
            .then(response => response.json())
            .then(messages => {
                console.log("messages",messages)
                renderMessages(messages);
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                document.getElementById('chat-messages').innerHTML = 
                    '<div class="text-center p-4"><p class="text-danger">Failed to load messages</p></div>';
            });
    }
    
    // Render messages
    function renderMessages(messages) {
        const chatMessages = document.getElementById('chat-messages');
        
        if (messages.length === 0) {
            chatMessages.innerHTML = '<div class="text-center p-4"><p class="text-muted">No messages yet</p></div>';
            return;
        }
        
        let html = '';
        messages.forEach(message => {
            const isFromMe = message.from_user_id == currentUserId;
            const messageClass = !isFromMe ? 'staff-message' : 'user-message';
            const alignClass = !isFromMe ? 'justify-content-start' : 'justify-content-end';
            const bubbleClass = !isFromMe ? 'bg-light text-dark' : 'bg-primary text-white';
            
            html += `
            <div class="message-item d-flex ${alignClass} mb-3">
                <div class="message-content ${messageClass} ${bubbleClass}" style="max-width: 75%; padding: 10px 15px; border-radius: 15px;">
                    ${message.message}
                    <div class="message-time text-right">
                        <small class="${!isFromMe ? 'text-muted' : 'text-white-50'}">
                            ${new Date(message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                        </small>
                    </div>
                </div>
            </div>`;
        });
        
        chatMessages.innerHTML = html;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Mark messages as read
    function markMessagesAsRead(userId) {
        fetch('/chat/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ user_id: userId,staff_role:incomingRole })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update unread badge
                const userItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
                if (userItem) {
                    const badge = userItem.querySelector('.badge');
                    if (badge) {
                        badge.remove();
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error marking messages as read:', error);
        });
    }
    
    // Send message
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!selectedUserId) return;
        
        const messageInput = document.getElementById('chat-message');
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        // Clear input
        messageInput.value = '';
        
        // Add message to UI immediately (optimistic UI update)
        const chatMessages = document.getElementById('chat-messages');
        const messageHtml = `
        <div class="message-item d-flex justify-content-end mb-3">
            <div class="message-content staff-message bg-primary text-white" style="max-width: 75%; padding: 10px 15px; border-radius: 15px;">
                ${message}
                <div class="message-time text-right">
                    <small class="text-white">
                        ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                    </small>
                </div>
            </div>
        </div>`;
        
        chatMessages.innerHTML += messageHtml;
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Send message to server
        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_id: selectedUserId,
                message: message,
                incoming_role: incomingRole
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Message sent:', data);
        })
        .catch(error => {
            console.error('Error sending message:', error);
            // If error, reload messages to ensure correct state
            loadMessages(selectedUserId);
        });
    });
    
    // Refresh chat
    document.getElementById('refresh-chat').addEventListener('click', function() {
        if (selectedUserId) {
            loadMessages(selectedUserId);
        }
    });
    
    // Search users
    document.getElementById('chat-user-search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const userName = item.querySelector('.user-info h6').textContent.toLowerCase();
            const userEmail = item.querySelector('.user-info p').textContent.toLowerCase();
            
            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
    var pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        // useTLS: true
    });
    
    // Set up Pusher for real-time chat
    let currentChannel = null;
    function subscribeToUserChannel(userId,role) {
        
        // Unsubscribe from previous channel if exists
        if (currentChannel) {
            pusher.unsubscribe(currentChannel.name);
        }
        
        
        // Create channel name based on the selected user and staff role
        var channelName = `chat-channel-${userId}-${currentStaffRole}`;

        if (role < 5 && currentStaffRole < 5) {
            
            const sortedIds = [userId, currentUserId].sort();
            channelName = `chat-channel-${sortedIds[0]}-${sortedIds[1]}`;
        }
        console.log(`Subscribing to channel: ${channelName}`);
        // Subscribe to the new channel
        currentChannel = pusher.subscribe(channelName);
        
        // Unbind any previous event handlers to prevent duplicates
        currentChannel.unbind('new-msg');
        currentChannel.unbind('new-message');
        currentChannel.unbind('App\\Events\\NewChatMessage');
        
        // Debug all events on this channel
        currentChannel.bind_global(function(event, data) {
            console.log(`Global event received on ${channelName}:`, event, data);
        });
        
        
        currentChannel.bind('App\\Events\\NewChatMessage', function(data) {
            console.log('New message received (App\\Events\\NewChatMessage):', data);
            // if (data.is_staff_message)return;
            handleNewMessage(data);
        });
    }
    
    // Separate the message handling logic to avoid duplication
    function handleNewMessage(data) {
        console.log("DATAAAAA ",data)
        // COMPARISONNNN
        const isFromMe = data.from_user_id == currentUserId;
        if (isFromMe) {
            return;
        }
        if (data.message) {
            // For backward compatibility
            addMessageToChat(data.message, isFromMe);
        }
        
        // Mark as read
        markMessagesAsRead(selectedUserId);
    }
    
    
    function addMessageToChat(message, isFromMe) {
        
        const chatMessages = document.getElementById('chat-messages');
        const alignClass = !isFromMe ? 'justify-content-start' : 'justify-content-end';
        const bubbleClass = !isFromMe ? 'bg-light text-dark' : 'bg-primary text-white';
        const messageClass = !isFromMe ? 'staff-message' : 'user-message';
        
        const messageHtml = `
        <div class="message-item d-flex ${alignClass} mb-3">
            <div class="message-content ${messageClass} ${bubbleClass}" style="max-width: 75%; padding: 10px 15px; border-radius: 15px;">
                ${message}
                <div class="message-time text-right">
                    <small class="${!isFromMe ? 'text-muted' : 'text-white-50'}">
                        ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                    </small>
                </div>
            </div>
        </div>`;
        
        chatMessages.innerHTML += messageHtml;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Load users on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Pusher with proper configuration
        window.pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true
        });
        
        console.log('Pusher initialized with key: {{ config('broadcasting.connections.pusher.key') }}');
        console.log('Cluster: {{ config('broadcasting.connections.pusher.options.cluster') }}');
        
        // Load users after Pusher is initialized
        loadUsers();
    });
    
    // Remove the duplicate Pusher initialization that was here before
</script>
@endsection