


















// Add helper function to format dates
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
function loadFiles() {
        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        if (!taskId) {
            console.error('No task ID found');
            return;
        }

        const fileList = document.getElementById('fileList');
        fileList.innerHTML = ''; // Clear existing files

        fetch(`/tasks/${taskId}/files`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(files => {
                if (Array.isArray(files)) {
                    files.forEach(file => {
                        const fileElement = createFileElement(file);
                        fileList.appendChild(fileElement);
                    });

                    // Update the file count
                    document.getElementById('fileCount').textContent = files.length;
                } else {
                    console.error('Expected array of files, got:', files);
                }
            })
            .catch(error => {
                console.error('Error loading files:', error);
                fileList.innerHTML = '<div class="text-red-500 p-4">Error loading files. Please try again.</div>';
            });
    }

    function createFileElement(file) {
        const fileDiv = document.createElement('div');
        fileDiv.className = 'bg-white dark:bg-gray-800 rounded-lg shadow p-4';
        fileDiv.setAttribute('data-file-id', file.id);
        
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const fileIcon = getFileIcon(fileExtension);
        const fileSize = formatFileSize(file.size);
        
        fileDiv.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        ${fileIcon}
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${file.name}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            ${fileSize} • Uploaded by ${file.user.name} • ${formatDate(file.created_at)}
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="downloadFile(${file.id})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    ${file.user_id === {{ auth()->id() }} ? `
                        <button onclick="deleteFile(${file.id})" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2H4a1 1 0 000-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
        
        return fileDiv;
    }

    function getFileIcon(extension) {
        const iconClass = 'h-8 w-8 text-gray-500 dark:text-gray-400';
        
        switch (extension) {
            case 'pdf':
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>`;
            case 'doc':
            case 'docx':
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>`;
            case 'xls':
            case 'xlsx':
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>`;
            case 'jpg':
            case 'jpeg':
            case 'png':
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path></svg>`;
            default:
                return `<svg class="${iconClass}" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg>`;
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function handleFileSelect(event) {
        const files = event.target.files;
        if (!files.length) return;
        
        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        const formData = new FormData();
        
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }
        
        fetch(`/tasks/${taskId}/files`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Reload the files list
            loadFiles();
            // Clear the file input
            event.target.value = '';
        })
        .catch(error => console.error('Error uploading files:', error));
    }

    function downloadFile(fileId) {
        window.location.href = `/files/${fileId}/download`;
    }


function editComment(commentId) {
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;
        
        const contentElement = commentElement.querySelector('.text-gray-700');
        const currentContent = contentElement.textContent;
        
        // Create edit form
        const editForm = document.createElement('div');
        editForm.className = 'mt-2';
        editForm.innerHTML = `
            <textarea class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" rows="3">${currentContent}</textarea>
            <div class="mt-2 flex justify-end space-x-2">
                <button onclick="cancelEdit(${commentId})" class="px-3 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button onclick="saveEdit(${commentId})" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                    Save
                </button>
            </div>
        `;
        
        // Replace content with edit form
        contentElement.replaceWith(editForm);
        
        // Focus the textarea
        editForm.querySelector('textarea').focus();
    }

    function cancelEdit(commentId) {
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        if (!commentElement) return;
        
        // Reload the comment to restore original state
        loadComments();
    }



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