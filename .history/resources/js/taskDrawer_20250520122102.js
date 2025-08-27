


























function replyToComment(commentId, userName) {
        const textarea = document.getElementById('newComment');
        textarea.value = `@${userName} `;
        textarea.focus();
    }

    function showReplyForm(commentId) {
        // Hide all other reply forms
        document.querySelectorAll('[id^="replyForm-"]').forEach(form => {
            form.classList.add('hidden');
        });
        
        // Show the selected reply form
        const replyForm = document.getElementById(`replyForm-${commentId}`);
        replyForm.classList.remove('hidden');
        
        // Focus the textarea
        const textarea = document.getElementById(`replyText-${commentId}`);
        textarea.focus();
    }

    function cancelReply(commentId) {
        const replyForm = document.getElementById(`replyForm-${commentId}`);
        replyForm.classList.add('hidden');
        
        // Clear the textarea
        const textarea = document.getElementById(`replyText-${commentId}`);
        textarea.value = '';
    }

    function submitReply(commentId) {
        const textarea = document.getElementById(`replyText-${commentId}`);
        const content = textarea.value.trim();
        
        if (!content) return;
        
        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        
        fetch(`/tasks/${taskId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                content: content,
                parent_id: commentId
            })
        })
        .then(response => response.json())
        .then(data => {
            // Get or create the replies container
            let repliesContainer = document.getElementById(`replies-${commentId}`);
            if (!repliesContainer) {
                // Create the replies container if it doesn't exist
                const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
                const repliesWrapper = document.createElement('div');
                repliesWrapper.className = 'mt-4';
                
                // Create the toggle button and count
                const toggleButton = document.createElement('div');
                toggleButton.className = 'flex items-center space-x-2 mb-2';
                toggleButton.innerHTML = `
                    <button onclick="toggleReplies(${commentId})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 flex items-center">
                        <svg id="toggleIcon-${commentId}" class="h-4 w-4 transform transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-1 text-sm">1 reply</span>
                    </button>
                `;
                
                // Create the replies container
                repliesContainer = document.createElement('div');
                repliesContainer.id = `replies-${commentId}`;
                repliesContainer.className = 'space-y-4';
                
                // Append everything to the comment
                repliesWrapper.appendChild(toggleButton);
                repliesWrapper.appendChild(repliesContainer);
                commentElement.querySelector('.flex-grow').appendChild(repliesWrapper);
            } else {
                // Update the reply count for existing container
                const countSpan = document.querySelector(`#toggleIcon-${commentId}`).nextElementSibling;
                const currentCount = parseInt(countSpan.textContent);
                countSpan.textContent = `${currentCount + 1} ${currentCount + 1 === 1 ? 'reply' : 'replies'}`;
            }
            
            // Add the reply to the container
            const replyElement = createCommentElement(data.comment, true);
            repliesContainer.appendChild(replyElement);
            
            // Clear and hide the reply form
            textarea.value = '';
            cancelReply(commentId);
        })
        .catch(error => console.error('Error adding reply:', error));
    }

    function toggleReplies(commentId) {
        const repliesContainer = document.getElementById(`replies-${commentId}`);
        const toggleIcon = document.getElementById(`toggleIcon-${commentId}`);
        
        if (repliesContainer.classList.contains('hidden')) {
            repliesContainer.classList.remove('hidden');
            toggleIcon.classList.remove('rotate-180');
        } else {
            repliesContainer.classList.add('hidden');
            toggleIcon.classList.add('rotate-180');
        }
    }