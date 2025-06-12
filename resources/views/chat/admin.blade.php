@extends('layout.apps')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Users</h5>
                </div>
                <div class="list-group list-group-flush border-bottom" style="max-height: 500px; overflow-y: auto;" id="user-list-container">
                    {{-- User list will be populated here by JavaScript --}}
                    <p class="p-3 text-muted" id="user-list-placeholder">Loading users...</p>
                </div> {{-- Corrected from </ul> to </div> --}}
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0" id="chat-with-user-name">Select a user to start chatting</h5>
                </div>
                <div class="card-body" style="height: 400px; overflow-y: auto;" id="chat-box">
                    {{-- Chat messages will appear here --}}
                    <p class="text-center text-muted" id="chat-placeholder">No conversation selected.</p>
                </div>
                <div class="card-footer" id="chat-form-container" style="display:none;">
                    <form id="chat-form">
                        <div class="input-group">
                            <input type="text" id="chat-message" class="form-control" placeholder="Type your message...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
// Initialize Pusher globally for the admin page
var pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
    cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
    useTLS: true
});

let selectedUser = null;
let selectedUserName = '';
const adminUserId = '11'; // Get the actual admin ID from Auth
const userListContainer = document.getElementById('user-list-container');
const userListPlaceholder = document.getElementById('user-list-placeholder');
const chatBox = document.getElementById('chat-box');
const chatFormContainer = document.getElementById('chat-form-container');
const chatForm = document.getElementById('chat-form');
const chatMessageInput = document.getElementById('chat-message');
const chatWithUserName = document.getElementById('chat-with-user-name');
const chatPlaceholder = document.getElementById('chat-placeholder');

// Subscribe to the admin's own channel to receive messages from all users
console.log("Admin subscribing to channel: chat-channel-" + adminUserId);
const adminChannel = pusher.subscribe('chat-channel-' + adminUserId);

// Listen for new messages on the admin's channel
adminChannel.bind('new-message', function(data) {
    console.log('Admin received message via Pusher:', data);
    
    // Ensure we have all required data
    if (!data || !data.from_user_id) {
        console.error('Received incomplete message data:', data);
        return;
    }
    
    const fromUserId = data.from_user_id;
    
    // If we're currently viewing this user's chat
    if (selectedUser == fromUserId) {
        // Check if we received encrypted message data
        if (data.message_encrypted && data.message_key) {
            // We need to fetch the decrypted message from the server
            fetch('/chat/decrypt-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    message_encrypted: data.message_encrypted,
                    message_key: data.message_key
                })
            })
            .then(res => res.json())
            .then(response => {
                if (response.success && response.message) {
                    // Now we have the decrypted message
                    const newMessage = {
                        from_user_id: fromUserId,
                        to_user_id: adminUserId,
                        message: response.message
                    };
                    
                    // Get existing messages from the DOM
                    const existingMessages = Array.from(document.querySelectorAll('#chat-box .d-flex')).map(el => {
                        const isFromAdmin = el.classList.contains('justify-content-end');
                        return {
                            from_user_id: isFromAdmin ? adminUserId : selectedUser,
                            message: el.querySelector('div').textContent
                        };
                    });
                    
                    // Render all messages including the new one
                    renderMessages([...existingMessages, newMessage]);
                    
                    // Mark the message as read since we're viewing it
                    markMessagesAsRead(fromUserId);
                } else {
                    console.error('Failed to decrypt message:', response);
                }
            })
            .catch(error => {
                console.error('Error decrypting message:', error);
            });
        } else if (data.message) {
            // For backward compatibility with unencrypted messages
            const newMessage = {
                from_user_id: fromUserId,
                to_user_id: adminUserId,
                message: data.message
            };
            
            // Get existing messages from the DOM
            const existingMessages = Array.from(document.querySelectorAll('#chat-box .d-flex')).map(el => {
                const isFromAdmin = el.classList.contains('justify-content-end');
                return {
                    from_user_id: isFromAdmin ? adminUserId : selectedUser,
                    message: el.querySelector('div').textContent
                };
            });
            
            // Render all messages including the new one
            renderMessages([...existingMessages, newMessage]);
            
            // Mark the message as read since we're viewing it
            markMessagesAsRead(fromUserId);
        } else {
            console.error('Message data is missing both encrypted and plain formats:', data);
        }
    } else {
        // If we're not viewing this user's chat, update the user list to show unread count
        checkNewMessages();
        
        // Play notification sound
        const notificationSound = document.getElementById('notification-sound');
        if (notificationSound) notificationSound.play();
        
        // Show browser notification if permitted
        if (Notification.permission === "granted") {
            // We might not have the user's name here, so we'll just show a generic message
            new Notification("New Message", {
                body: `You have a new message from a user`,
                icon: "/images/logo.png"
            });
        }
    }
});

function renderUserList(users) {
    userListContainer.innerHTML = ''; // Clear previous list or placeholder
    if (users.length === 0) {
        userListContainer.innerHTML = '<p class="p-3 text-muted">No users found.</p>';
        return;
    }
    users.forEach(u => {
        const userItem = document.createElement('a');
        userItem.href = '#';
        userItem.classList.add('list-group-item', 'list-group-item-action');
        userItem.setAttribute('data-id', u.id);
        userItem.setAttribute('data-name', u.name);
        userItem.setAttribute('data-unread', u.unread_count || 0);
        
        // Create a wrapper for user name and notification badge
        const userContent = document.createElement('div');
        userContent.classList.add('d-flex', 'justify-content-between', 'align-items-center', 'w-100');
        
        // Add user name
        const userName = document.createElement('span');
        userName.textContent = u.name;
        userContent.appendChild(userName);
        
        // Add notification badge if there are unread messages
        if (u.unread_count && u.unread_count > 0) {
            const badge = document.createElement('span');
            badge.classList.add('badge', 'badge-primary', 'badge-pill');
            badge.textContent = u.unread_count;
            userContent.appendChild(badge);
        }
        
        userItem.appendChild(userContent);
        
        userItem.onclick = function(e) {
            e.preventDefault();
            if (selectedUser === this.getAttribute('data-id')) return; // Already selected

            selectedUser = this.getAttribute('data-id');
            selectedUserName = this.getAttribute('data-name');

            // Update active state in user list
            document.querySelectorAll('#user-list-container .list-group-item-action').forEach(item => {
                item.classList.remove('active');
            });
            this.classList.add('active');
            
            // Remove notification badge when user is selected
            const badge = this.querySelector('.badge');
            if (badge) badge.remove();
            
            // Mark messages as read for this user
            markMessagesAsRead(selectedUser);

            chatWithUserName.textContent = 'Chat with ' + selectedUserName;
            chatFormContainer.style.display = '';
            chatPlaceholder.style.display = 'none';
            chatBox.innerHTML = '<p class="text-center text-muted">Loading messages...</p>';
            loadMessages();
        }
        userListContainer.appendChild(userItem);
    });
}

// Function to mark messages as read
function markMessagesAsRead(userId) {
    fetch('{{ route("chat.markAsRead") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to mark messages as read');
        }
        return response.json();
    })
    .then(data => {
        console.log('Messages marked as read:', data);
    })
    .catch(error => console.error('Error marking messages as read:', error));
}

// Remove the setupPusherForUser function as we're using a single admin channel now

// Load users function - simplified without the setupPusherForUser calls
function loadUsers() {
    fetch('{{ route('chat.users') }}')
        .then(res => {
            if (!res.ok) throw new Error('Failed to load users');
            return res.json();
        })
        .then(users => {
            console.log('Users loaded:', users);
            renderUserList(users);
            // No need to set up Pusher for each user anymore
        })
        .catch(error => {
            console.error('Error loading users:', error);
            userListContainer.innerHTML = '<p class="p-3 text-danger">Could not load users.</p>';
        });
}

// Check for new messages from all users - we'll still use this occasionally
// to ensure our UI is in sync, but not as frequently as before
function checkNewMessages() {
    if (document.visibilityState !== 'visible') return;
    
    fetch('{{ route("chat.users") }}')
        .then(res => res.json())
        .then(users => {
            // Update user list with unread counts
            renderUserList(users);
            
            // Set up Pusher for any new users
            users.forEach(user => {
                setupPusherForUser(user.id);
            });
            
            // Find users with unread messages
            const usersWithUnread = users.filter(u => u.unread_count && u.unread_count > 0);
            
            // If there are unread messages
            if (usersWithUnread.length > 0) {
                // Play notification sound if available
                const notificationSound = document.getElementById('notification-sound');
                if (notificationSound) notificationSound.play();
                
                // Show browser notification if permitted
                if (Notification.permission === "granted") {
                    const latestUser = usersWithUnread[0];
                    new Notification("New Message", {
                        body: `You have a new message from ${latestUser.name}`,
                        icon: "/images/logo.png"
                    });
                }
                
                // Update page title to show unread count
                const totalUnread = usersWithUnread.reduce((sum, user) => sum + user.unread_count, 0);
                document.title = totalUnread > 0 ? `(${totalUnread}) Chat - Admin` : "Chat - Admin";
            }
        })
        .catch(error => console.error('Error checking new messages:', error));
}

// Request notification permission on page load
function requestNotificationPermission() {
    if (Notification && Notification.permission !== "granted") {
        Notification.requestPermission();
    }
}

// Load users and set up initial Pusher subscriptions
loadUsers();
requestNotificationPermission();

// Add notification sound element
const soundElement = document.createElement('audio');
soundElement.id = 'notification-sound';
soundElement.src = '{{ asset("sounds/notification.mp3") }}';
soundElement.style.display = 'none';
document.body.appendChild(soundElement);

// Check for new messages occasionally (much less frequently than before)
setInterval(checkNewMessages, 30000); // Check every 30 seconds instead of 5

function renderMessages(messages) {
    chatBox.innerHTML = ''; // Clear previous messages or placeholder
    if (messages.length === 0) {
        chatBox.innerHTML = '<p class="text-center text-muted">No messages yet. Start the conversation!</p>';
        return;
    }
    messages.forEach(m => {
        const messageWrapper = document.createElement('div');
        messageWrapper.classList.add('d-flex', 'mb-2');

        const messageBubble = document.createElement('div');
        messageBubble.classList.add('p-2', 'rounded');
        messageBubble.style.maxWidth = '75%';
        messageBubble.style.wordBreak = 'break-word';
        
        if (m.from_user_id == adminUserId || m.is_admin_message == 1) { // Message from admin ("Me")
            messageWrapper.classList.add('justify-content-end');
            messageBubble.classList.add('bg-primary', 'text-white');
            messageBubble.style.borderBottomRightRadius = '5px';
        } else { // Message from user
            messageWrapper.classList.add('justify-content-start');
            messageBubble.classList.add('bg-light', 'text-dark', 'border');
            messageBubble.style.borderBottomLeftRadius = '5px';
        }
        
        messageBubble.textContent = m.message;
        messageWrapper.appendChild(messageBubble);
        chatBox.appendChild(messageWrapper);
    });
    
    // Always scroll to bottom after rendering
    setTimeout(() => {
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 100);
}

function loadMessages() {
    if (!selectedUser) return;

    fetch(`{{ route('chat.messages') }}?user_id=${selectedUser}`)
        .then(res => {
            if (!res.ok) throw new Error('Failed to load messages');
            return res.json();
        })
        .then(messages => {
            console.log("Messages loaded:", messages)
            // Messages should already be decrypted by the server
            renderMessages(messages);
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            chatBox.innerHTML = '<p class="text-center text-danger">Could not load messages.</p>';
        });
}

chatForm.onsubmit = function(e) {
    e.preventDefault();
    const msg = chatMessageInput.value.trim();
    if (!msg || !selectedUser) return;

    console.log('Sending message to user:', selectedUser);

    // Show message immediately in UI (optimistic UI update)
    const tempMessage = {
        from_user_id: adminUserId,
        to_user_id: selectedUser,
        message: msg
    };
    
    // Get existing messages from the DOM
    const existingMessages = Array.from(document.querySelectorAll('#chat-box .d-flex')).map(el => {
        const isFromAdmin = el.classList.contains('justify-content-end');
        return {
            from_user_id: isFromAdmin ? adminUserId : selectedUser,
            message: el.querySelector('div').textContent
        };
    });
    
    // Render all messages including the new one
    renderMessages([...existingMessages, tempMessage]);
    
    // Clear input field immediately for better UX
    chatMessageInput.value = '';

    fetch('{{ route('chat.send') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ to_user_id: selectedUser, message: msg })
    })
    .then(res => {
        if (!res.ok) throw new Error('Failed to send message');
        return res.json();
    })
    .then(data => {
        console.log('Message sent successfully:', data);
        // No need to reload messages - we've already shown the message
    })
    .catch(error => {
        console.error('Error sending message:', error);
        // On error, reload messages to ensure correct state
        loadMessages();
    });
};

// Remove any duplicate code and unnecessary Pusher channel subscriptions
// Removed duplicate loadUsers() call
// Removed duplicate setInterval for refreshing messages
</script>
@endsection