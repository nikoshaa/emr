<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>NeoSIMRS - Sistem Informasi Rumah Sakit</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/logo.png')}}">
	<link rel="stylesheet" href="{{asset('vendor/chartist/css/chartist.min.css')}}">
	<!-- Datatable -->
    <link href="{{asset('vendor/datatables/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/toastr/css/toastr.min.css')}}">
    <link href="{{asset('vendor/sweetalert2/dist/sweetalert2.min.css')}}" rel="stylesheet">
    <style>
    p {
        margin: 0;
    }

    </style>
    @yield('header')
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
</head>
<body>

    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">


        <div class="nav-header">
            <a href="#" class="brand-logo">
                 <img class="logo-abbr" src="{{asset('images/logo.png')}}" alt="">
                 {{-- <img class="logo-compact" src="{{asset('images/logo-text.png')}}" alt=""> --}}
                {{-- <img class="brand-title" src="{{asset('images/logo-text.png')}}" alt=""> --}}
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>


		<!--**********************************
            Header start
        ***********************************-->
        @include('layout.partial.header')
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include('layout.partial.sidebar')
        <!--**********************************
            Sidebar end
        ***********************************-->

		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				@yield('content')
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

        <!--**********************************
            Footer start
        ***********************************-->
        @include('layout.partial.footer')


    </div>

    <script src="{{asset('vendor/global/global.min.js')}}"></script>
	<script src="{{asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>
	<script src="{{asset('vendor/chart.js/Chart.bundle.min.js')}}"></script>
    <script src="{{asset('js/custom.min.js')}}"></script>
	<script src="{{asset('js/deznav-init.js')}}"></script>

    <!-- Datatable -->
    <script src="{{asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('vendor/toastr/js/toastr.min.js')}}"></script>
    <script src="{{asset('vendor/sweetalert2/dist/sweetalert2.min.js')}}"></script>
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

    <script>
        // Global Pusher error handling
        window.addEventListener('load', function() {
            if (typeof Pusher !== 'undefined') {
                Pusher.logToConsole = true; // Enable for debugging
                
                // Global error handler for Pusher
                Pusher.log = function(msg) {
                    console.log('Pusher Log:', msg);
                };
            } else {
                console.error('Pusher library not loaded!');
            }
        });
    </script>


	<script>
        @if(Session::has('sukses'))
            toastr.success("{{Session::get('sukses')}}", "Sukses",{timeOut: 5000})
        @endif
        @if(Session::has('gagal'))
            toastr.error("{{Session::get('gagal')}}", "Gagal",{timeOut: 5000})
        @endif

        // pusher
        var notificationsWrapper   = $('.dropdown-notifications');
        var notificationsToggle    = notificationsWrapper.find('a[data-toggle]');
        var notificationsCountElem = notificationsToggle.find('i[data-count]');
        var notificationsCount     = parseInt(notificationsCountElem.data('count'));
        var notifications          = notificationsWrapper.find('ul.timeline');

        var pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        
    });


        

        var user_id = "{{auth()->user()->id}}";
        var role = "{{auth()->user()->role_display()}}";

        // Subscribe to the channel we specified in our Laravel Event
        var channel = pusher.subscribe('status-rekam-updated-'+user_id);

        // Bind a function to a Event (the full Laravel class)
        channel.bind('App\\Events\\StatusRekamUpdate', function(data) {
            var existingNotifications = notifications.html();
            var avatar = Math.floor(Math.random() * (71 - 20 + 1)) + 20;
            var newNotificationHtml = `
            <li>
                <div class="timeline-panel">

                    <div class="media-body">
                        <h6 class="mb-1">`+data.no_rekam+`</h6>
                        <h6 class="mb-1">`+data.message+`</h6>
                        <small class="d-block">`+data.created_at+`</small>
                        <a href="`+data.link+`">Klik Proses</a>
                    </div>
                </div>
            </li>
            `;
            notifications.html(newNotificationHtml + existingNotifications);

            notificationsCount += 1;
            notificationsCountElem.attr('data-count', notificationsCount);
            notificationsWrapper.find('.notif-count').text(notificationsCount);
            // $("#data-count").html(notificationsCount);
            notificationsWrapper.show();


            if(role=="Dokter"){
                var listPeriksaDokter = `
                    <div class="d-flex pb-3 border-bottom mb-3 align-items-end">
                        <div class="mr-auto">
                            <p class="text-black font-w600 mb-2"><a href="#">`+data.no_rekam+`</a></p>
                            <ul>
                                <li><i class="las la-clock"></i>Time : `+data.created_at+`</li>
                                <li><i class="las la-clock"></i>No Rekam : `+data.no_rekam+`</li>
                                <li><i class="las la-user"></i>`+data.message+`</li>
                            </ul>
                        </div>
                        <a href="`+data.link+`"
                            class="btn-rounded btn-primary btn-xs"><i class="fa fa-user-md"></i> Periksa</a>
                    </div>`;

                    $("#antrian-list-notif").append(listPeriksaDokter);
            }else if(role=="Apotek"){
                var listPermintaanObat = `
                    <div class="d-flex pb-3 border-bottom mb-3 align-items-end">
                        <div class="mr-auto">
                            <p class="text-black font-w600 mb-2"><a href="#">`+data.no_rekam+`</a></p>
                            <ul>
                                <li><i class="las la-clock"></i>Time : `+data.created_at+`</li>
                                <li><i class="las la-clock"></i>No Rekam : `+data.no_rekam+`</li>
                                <li><i class="las la-user"></i>`+data.message+`</li>
                            </ul>
                        </div>
                        <a href="`+data.link+`"
                            class="btn-rounded btn-primary btn-xs"><i class="fa fa-user-md"></i> Berikan Obat</a>
                    </div>`;

                    $("#obat-list-notif").append(listPermintaanObat);
            }



        });
        //end pusher

		// (function($) {
		// 	var table = $('#example5').DataTable({
		// 		searching: true,
		// 		paging:true,
		// 		select: false,
		// 		//info: false,
		// 		lengthChange:false

		// 	});
		// 	$('#example tbody').on('click', 'tr', function () {
		// 		var data = table.row( this ).data();

		// 	});
		// })(jQuery);
	</script>
    @if(Auth::check() && Auth::user()->role != 1)
    {{-- Chat Button --}}
    <button id="open-chat" class="btn btn-primary rounded-circle shadow" style="position:fixed; bottom:20px; right:20px; width: 60px; height: 60px; font-size: 48px; z-index:9998; display: flex; align-items: center; justify-content: center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
    </button>

    {{-- Chat Modal --}}
    <div id="chat-modal" class="shadow-lg" style="display:none; position:fixed; bottom:90px; right:20px; width:350px; max-width: 90vw; height:450px; background:#fff; border-radius: 8px; z-index:9999; display:flex; flex-direction:column; overflow:hidden;">
        {{-- Chat Header --}}
        <div style=" color: white; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center;" class="bg-primary">
            <div class="d-flex align-items-center">
                <h6 style="margin:0; font-weight: 600; color: white">Chat with <span id="chat-recipient">Staff</span></h6>
                <div class="dropdown ml-2">
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="staffRoleDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-users"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="staffRoleDropdown">
                        <a class="dropdown-item staff-role-item" data-role="1" href="#">Admin</a>
                        <a class="dropdown-item staff-role-item" data-role="2" href="#">Pendaftaran</a>
                        <a class="dropdown-item staff-role-item" data-role="3" href="#">Dokter</a>
                        <a class="dropdown-item staff-role-item" data-role="4" href="#">Apotek</a>
                    </div>
                </div>
            </div>
            <button id="close-chat" class="btn btn-sm" style="color: white; background: transparent; border: none; font-size: 1.2rem; line-height: 1;">&times;</button>
        </div>

        {{-- Chat Messages Area --}}
        <div id="chat-box-user" style="flex-grow:1; overflow-y:auto; padding:15px; background-color: #f8f9fa;">
            {{-- Messages will be loaded here --}}
        </div>

        {{-- Chat Input Form --}}
        <form id="chat-form-user" style="padding:10px 15px; border-top: 1px solid #dee2e6; background-color: #fff;">
            <div class="input-group">
                <input type="text" id="chat-message-user" class="form-control" placeholder="Type a message..." style="border-radius: 20px 0 0 20px;">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit" style="border-radius: 0 20px 20px 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const currentUserId = {{ Auth::id() }};
        const chatModal = document.getElementById('chat-modal');
        const chatBoxUser = document.getElementById('chat-box-user');
        const chatMessageUserInput = document.getElementById('chat-message-user');
        const chatFormUser = document.getElementById('chat-form-user');
        const openChatButton = document.getElementById('open-chat');
        const closeChatButton = document.getElementById('close-chat');
        const chatRecipientSpan = document.getElementById('chat-recipient');
        const staffRoleDropdownItems = document.querySelectorAll('.staff-role-item'); // Get all items
        
        let selectedStaffRole = 1; // Default role ID
        let selectedStaffRoleName = 'Admin'; // Default role name
        let currentUserRole = {{ Auth::user()->role }};
        let currentChannelName = null;

        if (currentUserRole === 1) { // Current user is Admin
            selectedStaffRole = 1;
            selectedStaffRoleName = 'Admin';
            chatRecipientSpan.textContent = 'Admin';

            staffRoleDropdownItems.forEach(item => {
                if (item.getAttribute('data-role') !== '1') {
                    item.style.display = 'none'; // Hide non-Admin roles
                } else {
                    item.style.display = '';     // Ensure Admin role is visible
                }
            });
        } else { // Current user is NOT Admin
            let firstVisibleRoleSet = false;
            staffRoleDropdownItems.forEach(item => {
                if (item.getAttribute('data-role') === '1') { // Hide Admin role for non-admins
                    item.style.display = 'none';
                } else {
                    item.style.display = ''; // Ensure other roles are visible
                    if (!firstVisibleRoleSet) {
                        selectedStaffRole = parseInt(item.getAttribute('data-role'));
                        selectedStaffRoleName = item.textContent;
                        chatRecipientSpan.textContent = selectedStaffRoleName;
                        firstVisibleRoleSet = true;
                    }
                }
            });

            if (!firstVisibleRoleSet) {
                // This case should ideally not happen if there are other roles defined
                chatRecipientSpan.textContent = 'No staff available';
                selectedStaffRole = null; // Or some indicator of no selection
                selectedStaffRoleName = 'N/A';
                // Consider disabling chat or dropdown if no roles are available
            } else {
                // Messages and subscription for the new default role will be loaded/subscribed later
                // by the initial call to loadUserMessages() and subscribeToStaffChannel() if needed,
                // or when the user opens the chat.
                // For consistency, let's ensure they are called if a default is set.
                loadUserMessages(); 
                subscribeToStaffChannel(selectedStaffRole);
            }
        }
        
        // Function to subscribe to the chat channel for the selected staff role
        function subscribeToStaffChannel(staffRole) {
            console.log("STAFFF ROLEEE", staffRole);
            var pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
                cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            });
            
            // Unsubscribe from the previous channel if it exists
            if (currentChannelName) {
                pusher.unsubscribe(currentChannelName);
                console.log(`Unsubscribed from channel: ${currentChannelName}`);
            }

            // Create the new channel name
            let channelName = `chat-channel-${currentUserId}-${staffRole}`;
            if (currentUserRole === staffRole) {
                channelName = `chat-channel-${staffRole}`;
            }
            currentChannelName = channelName; // Store the new channel name
            console.log(`Subscribing to channel: ${channelName}`);

            
            // Subscribe to the new channel
            currentChannel = pusher.subscribe(channelName);

            // Unbind any previous 'App\\Events\\NewChatMessage' handlers to prevent duplicates
            if (currentChannel) {
                currentChannel.unbind('App\\Events\\NewChatMessage');
            }


            // Listen for new messages on the new channel
            currentChannel.bind('App\\Events\\NewChatMessage', function(data) {
                console.log('New message received via Pusher:', data);

                if (!data || !data.from_user_id) {
                    console.error('Received incomplete message data:', data);
                    return;
                }

                // Only process if chat is open AND message is from the currently selected staff role
                if (chatModal.style.display === 'flex' && data.staff_role && data.staff_role == selectedStaffRole) {
                    const isMyMessage = data.from_user_id == currentUserId;
                    if (isMyMessage) {
                        return
                    }
                    if (data.message) {
                        addNewMessageToChat(data.from_user_id, data.message, 
                            data.staff_role_name,data.staff_name);
                    }
                } else if (chatModal.style.display !== 'flex') {
                    // If chat is not open, show a notification
                    toastr.info(`New message from ${data.staff_role_name || 'staff'}`, "New Message", {timeOut: 5000});

                    // Play notification sound if you have one
                    const notificationSound = document.getElementById('chat-notification-sound');
                    if (notificationSound) notificationSound.play();
                }
                // Note: If chat is open but the message is from a *different* staff role,
                // the current logic correctly ignores it in the chat box but doesn't show a notification.
                // You might want to add a notification here if needed.
            });
        }

        // Handle staff role selection
        staffRoleDropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                // Only proceed if the item is visible
                if (this.style.display === 'none') {
                    return;
                }
                selectedStaffRole = parseInt(this.getAttribute('data-role'));
                selectedStaffRoleName = this.textContent;
                chatRecipientSpan.textContent = selectedStaffRoleName;
                loadUserMessages(); // Load messages for the newly selected role
                subscribeToStaffChannel(selectedStaffRole); // Subscribe to the new channel
            });
        });
        
        function renderUserMessages(messages) {
            chatBoxUser.innerHTML = ''; // Clear previous messages
            if (messages.length === 0) {
                chatBoxUser.innerHTML = '<p class="text-center text-muted small mt-2">No messages yet. Say hi!</p>';
                return;
            }
            
            messages.forEach(m => {
                // console.log("m",m)
                const messageWrapper = document.createElement('div');
                messageWrapper.classList.add('d-flex', 'mb-2');

                const contentContainer = document.createElement('div');
                contentContainer.style.maxWidth = '75%';
                contentContainer.style.wordBreak = 'break-word';

                const messageBubble = document.createElement('div');
                messageBubble.style.padding = '8px 12px';
                messageBubble.style.borderRadius = '15px';

                if (m.from_user_id == currentUserId) { 
                    messageWrapper.classList.add('justify-content-end');
                    messageBubble.classList.add('bg-primary', 'text-white');
                    messageBubble.style.borderBottomRightRadius = '5px';
                    contentContainer.appendChild(messageBubble); 
                } else { 
                    messageWrapper.classList.add('justify-content-start');
                    messageBubble.classList.add('bg-light', 'text-dark', 'border');
                    messageBubble.style.borderBottomLeftRadius = '5px';

                    if (m.from_user && m.from_user.name) { 
                        const roleLabel = document.createElement('small');
                        roleLabel.classList.add('d-block', 'text-muted', 'mb-1');
                        roleLabel.textContent = m.from_user.name;
                        contentContainer.appendChild(roleLabel); 
                    }
                    contentContainer.appendChild(messageBubble); 
                }
                messageBubble.textContent = m.message;
                messageWrapper.appendChild(contentContainer); 
                chatBoxUser.appendChild(messageWrapper);
            });
            
            setTimeout(() => {
                chatBoxUser.scrollTop = chatBoxUser.scrollHeight;
            }, 100);
        }

        function loadUserMessages() {
            console.log('Loading messages for current user with staff role:', selectedStaffRole);
            
            fetch(`/chat/messages?staff_role=${selectedStaffRole}&is_chat_corner=1`)
                .then(res => {
                    if (!res.ok) {
                        console.error('Server returned error:', res.status);
                        throw new Error('Failed to load messages');
                    }
                    return res.json();
                })
                .then(messages => {
                    console.log('Messages loaded:', messages);
                    renderUserMessages(messages);
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    chatBoxUser.innerHTML = '<p class="text-center text-danger">Failed to load messages. Please try again.</p>';
                });
        }

        // Initialize chat modal properly
        chatModal.style.display = 'none'; // Ensure it starts hidden

        openChatButton.onclick = function() {
            console.log('Opening chat modal');
            chatModal.style.display = 'flex'; // Use flex for column layout
            chatRecipientSpan.textContent = selectedStaffRoleName;
            loadUserMessages(); // Load initial messages
            subscribeToStaffChannel(selectedStaffRole); // Subscribe to the channel for the current selected role
            chatMessageUserInput.focus();
        };

        closeChatButton.onclick = function() {
            chatModal.style.display = 'none';
            // Optional: Unsubscribe when closing the modal to save resources
            // if (currentChannelName) {
            //     pusher.unsubscribe(currentChannelName);
            //     currentChannelName = null;
            //     currentChannel = null;
            //     console.log('Unsubscribed from channel on modal close');
            // }
        };

        chatFormUser.onsubmit = function(e) {
            e.preventDefault();
            const msg = chatMessageUserInput.value.trim();
            // console.log("CURRENT USER ROLEEEE", currentUserRole)
            if (!msg) return;

            // Show message immediately in UI (optimistic UI update)
            // Note: For user messages, we don't need to check staff_role
            addNewMessageToChat(currentUserId, msg, null); // Pass null for staffRoleName for user messages


            fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    staff_role: selectedStaffRole, // Send the selected staff role
                    message: msg,
                    is_chat_corner: 1
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Failed to send message');
                return res.json();
            })
            .then(() => {
                chatMessageUserInput.value = '';
            })
            .catch(error => {
                console.error('Error sending message:', error);
                // If error, reload messages to ensure correct state
                loadUserMessages();
            });
        };

        // Remove the static subscription here
        // const chatChannel = pusher.subscribe('chat-channel-' + currentUserId + '-' + selectedStaffRole);
        // console.log("chat-channel-"+currentUserId+"-"+selectedStaffRole+"")

        // Remove the static event binding here
        // chatChannel.bind('App\\Events\\NewChatMessage', function(data) { ... });


        // Helper function to add a new message to the chat
        // Modified to handle both user and staff messages
        function addNewMessageToChat(fromUserId, messageText, staffRoleName, staffName) {
            console.log("STAFFF NAMEEE", staffName)
            const isFromStaff = fromUserId !== currentUserId; // Determine if message is from staff
            const messageWrapper = document.createElement('div');
            messageWrapper.classList.add('d-flex', 'mb-2');

            // Create a container for the message content (name + bubble)
            const contentContainer = document.createElement('div');
            contentContainer.style.maxWidth = '75%'; // Apply max-width to the container
            contentContainer.style.wordBreak = 'break-word'; // Apply word-break to the container

            const messageBubble = document.createElement('div');
            messageBubble.style.padding = '8px 12px';
            messageBubble.style.borderRadius = '15px';


            if (!isFromStaff ) { // Message from current user ("Me")
                messageWrapper.classList.add('justify-content-end');
                messageBubble.classList.add('bg-primary', 'text-white');
                messageBubble.style.borderBottomRightRadius = '5px';
                // contentContainer.appendChild(messageBubble); // Append bubble directly for user messages
            } else { // Message from Staff
                messageWrapper.classList.add('justify-content-start');
                messageBubble.classList.add('bg-light', 'text-dark', 'border');
                messageBubble.style.borderBottomLeftRadius = '5px';

                // Add staff role label if available
                if (staffRoleName) {
                    const roleLabel = document.createElement('small');
                    roleLabel.classList.add('d-block', 'text-muted', 'mb-1');
                    roleLabel.textContent = staffName;
                    contentContainer.appendChild(roleLabel); // Append role label to the content container
                }
            }
            messageBubble.textContent = messageText;
            contentContainer.appendChild(messageBubble); // Append bubble to the content container
            messageWrapper.appendChild(contentContainer);
            chatBoxUser.appendChild(messageWrapper);

            // Always scroll to bottom after adding a message
            setTimeout(() => {
                chatBoxUser.scrollTop = chatBoxUser.scrollHeight;
            }, 100);
        }


        // Add notification sound element
        const soundElement = document.createElement('audio');
        soundElement.id = 'chat-notification-sound';
        soundElement.src = '{{ asset("sounds/notification.mp3") }}';
        soundElement.style.display = 'none';
        document.body.appendChild(soundElement);

        // Initial subscription when the page loads (or when the chat modal is first opened)
        // The subscription is now handled when the modal is opened via openChatButton.onclick
        // If you want to subscribe immediately on page load, uncomment the line below:
        // subscribeToStaffChannel(selectedStaffRole);

    </script>
    @endif
    @yield('script')
</body>
</html>
<style>
    /* Default state - grey stroke - for all SVG icons */
    .chat-icon .feather-message-circle,
    .patient-icon .feather-user,
    .medical-icon .feather-droplet {
        stroke: #888888;
        transition: stroke 0.3s ease;
    }
    
    /* Active/hover state - use currentColor */
    .chat-icon.active .feather-message-circle,
    .chat-icon:hover .feather-message-circle,
    .patient-icon.active .feather-user,
    .patient-icon:hover .feather-user,
    .medical-icon.active .feather-droplet,
    .medical-icon:hover .feather-droplet {
        stroke: currentColor;
    }
    
    /* For the active menu item */
    .metismenu .mm-active > .chat-icon .feather-message-circle,
    .metismenu .mm-active > .patient-icon .feather-user,
    .metismenu .mm-active > .medical-icon .feather-droplet {
        stroke: currentColor;
    }
</style>
