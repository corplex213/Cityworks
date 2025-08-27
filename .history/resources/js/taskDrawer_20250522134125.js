const openTaskDetails = (row) => taskDrawerHandler.open(row);
const closeTaskDetailsDrawer = () => taskDrawerHandler.close();

    function loadComments() {
        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        if (!taskId) {
            console.error('No task ID found');
            return;
        }

        const commentsContainer = document.getElementById('commentsContainer');
        if (!commentsContainer) {
            console.error('Comments container not found');
            return;
        }
        
        commentsContainer.innerHTML = '';

        fetch(`/tasks/${taskId}/comments`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(comments => {
                if (Array.isArray(comments)) {
                    comments.forEach(comment => {
                        const commentElement = createCommentElement(comment);
                        commentsContainer.appendChild(commentElement);
                    });

                    // Update the count and handle empty state
                    updateCommentCount(comments.length);
                } else {
                    console.error('Expected array of comments, got:', comments);
                    updateCommentCount(0);
                }
            })
            .catch(error => {
                console.error('Error loading comments:', error);
                commentsContainer.innerHTML = '<div class="text-red-500 p-4">Error loading comments. Please try again.</div>';
                updateCommentCount(0);
            });
    }

    function createCommentElement(comment, isReply = false) {
        const commentDiv = document.createElement('div');
        // All replies will have the same indentation level (ml-12)
        commentDiv.className = `bg-white dark:bg-gray-700 rounded-lg shadow p-4 mb-4 ${isReply ? 'ml-12' : ''}`;
        commentDiv.setAttribute('data-comment-id', comment.id);

        const isCurrentUser = comment.user_id === window.CURRENT_USER_ID;

        commentDiv.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                        ${comment.user.name.charAt(0)}
                    </div>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">${comment.user.name}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                ${formatDate(comment.created_at)}
                                ${comment.edited ? '<span class="text-xs text-gray-500 dark:text-gray-400 ml-2 italic">(Edited)</span>' : ''}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            ${!isReply ? `
                                <button onclick="showReplyForm(${comment.id})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            ` : ''}
                           ${isCurrentUser ? `
                            <button onclick="editComment(${comment.id})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button onclick="deleteComment(${comment.id})" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2H4a1 1 0 000-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        ` : ''}
                        </div>
                    </div>
                    <div class="mt-2 text-gray-700 dark:text-gray-300 whitespace-pre-wrap">${comment.content}</div>
                    
                    <!-- Reply Form (Hidden by default) -->
                    <div id="replyForm-${comment.id}" class="hidden mt-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm">
                                    ${window.CURRENT_USER_NAME ? window.CURRENT_USER_NAME[0] : ''}
                                </div>
                            </div>
                            <div class="flex-grow">
                                <textarea 
                                    id="replyText-${comment.id}" 
                                    class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" 
                                    rows="2"
                                    placeholder="Write a reply..."></textarea>
                                <div class="mt-2 flex justify-end space-x-2">
                                    <button onclick="cancelReply(${comment.id})" class="px-3 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                        Cancel
                                    </button>
                                    <button onclick="submitReply(${comment.id})" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                                        Reply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Replies Container -->
                    ${!isReply && comment.replies && comment.replies.length > 0 ? `
                        <div class="mt-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <button onclick="toggleReplies(${comment.id})" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 flex items-center">
                                    <svg id="toggleIcon-${comment.id}" class="h-4 w-4 transform transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-1 text-sm">${comment.replies.length} ${comment.replies.length === 1 ? 'reply' : 'replies'}</span>
                                </button>
                            </div>
                            <div id="replies-${comment.id}" class="space-y-4 hidden">
                                ${comment.replies.map(reply => createCommentElement(reply, true).outerHTML).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        return commentDiv;
    }

    function handleCommentKeydown(event) {
        if (event.key === '@') {
            showMentionSuggestions();
        } else if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            addComment();
        }
    }

    function showMentionSuggestions() {
        const suggestionsDiv = document.getElementById('mentionSuggestions');
        suggestionsDiv.classList.remove('hidden');
        // Fetch and populate team members
        fetch('/api/team-members')
            .then(response => response.json())
            .then(members => {
                suggestionsDiv.innerHTML = members.map(member => `
                    <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" onclick="insertMention('${member.name}')">
                        ${member.name}
                    </div>
                `).join('');
            });
    }

    function insertMention(name) {
        const textarea = document.getElementById('newComment');
        const cursorPos = textarea.selectionStart;
        const text = textarea.value;
        const beforeCursor = text.substring(0, cursorPos);
        const afterCursor = text.substring(cursorPos);
        
        // Find the last @ symbol before cursor
        const lastAtIndex = beforeCursor.lastIndexOf('@');
        if (lastAtIndex !== -1) {
            textarea.value = beforeCursor.substring(0, lastAtIndex) + `@${name} ` + afterCursor;
            document.getElementById('mentionSuggestions').classList.add('hidden');
        }
    }

    function addComment() {
        const newComment = document.getElementById('newComment').value;
        if (!newComment.trim()) return;

        const taskId = document.getElementById('taskDetailsDrawer').getAttribute('data-current-row');
        
        fetch(`/tasks/${taskId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                task_id: taskId,
                content: newComment
            })
        })
        .then(response => response.json())
        .then(data => {
            const commentsContainer = document.getElementById('commentsContainer');
            const commentElement = createCommentElement(data.comment);
            commentsContainer.appendChild(commentElement); // Append to the bottom instead of inserting at the top
            document.getElementById('newComment').value = ''; // Clear input
        })
        .catch(error => console.error('Error adding comment:', error));
    }

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
    if (!fileList) {
        console.error('File list container not found');
        return;
    }
    
    fileList.innerHTML = '';

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

                // Safely update file counts
                updateFileCount(files.length);
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
                    ${file.user_id === window.CURRENT_USER_ID ? `
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

// --- BEGIN: DRY and maintainable event delegation and helpers ---

// Helper: Find ancestor with selector
function closest(el, selector) {
    while (el && el.nodeType === 1) {
        if (el.matches(selector)) return el;
        el = el.parentElement;
    }
    return null;
}

// Event delegation for comment actions
function handleCommentAction(e) {
    const target = e.target.closest('button,svg,path');
    if (!target) return;
    const btn = target.closest('button');
    if (!btn) return;
    if (btn.onclick) return; // Inline handler fallback
    const commentDiv = closest(btn, '[data-comment-id]');
    if (!commentDiv) return;
    const commentId = commentDiv.getAttribute('data-comment-id');
    if (btn.classList.contains('text-red-500')) {
        // Delete
        window.deleteComment && window.deleteComment(Number(commentId));
    } else if (btn.classList.contains('text-gray-500') && btn.innerHTML.includes('edit')) {
        // Edit
        window.editComment && window.editComment(Number(commentId));
    } else if (btn.innerHTML.includes('Reply')) {
        window.showReplyForm && window.showReplyForm(Number(commentId));
    } else if (btn.innerHTML.includes('Cancel')) {
        window.cancelReply && window.cancelReply(Number(commentId));
    } else if (btn.innerHTML.includes('Save')) {
        window.saveEdit && window.saveEdit(Number(commentId));
    } else if (btn.innerHTML.includes('Reply')) {
        window.submitReply && window.submitReply(Number(commentId));
    }
}

// Event delegation for file actions
function handleFileAction(e) {
    const target = e.target.closest('button,svg,path');
    if (!target) return;
    const btn = target.closest('button');
    if (!btn) return;
    const fileDiv = closest(btn, '[data-file-id]');
    if (!fileDiv) return;
    const fileId = fileDiv.getAttribute('data-file-id');
    if (btn.innerHTML.includes('download')) {
        window.downloadFile && window.downloadFile(Number(fileId));
    } else if (btn.classList.contains('text-red-500')) {
        window.deleteFile && window.deleteFile(Number(fileId));
    }
}

function updateCommentCount(count) {
    // Update comment count in the header
    const commentCountElement = document.getElementById('commentCount');
    if (commentCountElement) {
        commentCountElement.textContent = count;
    }
    
    // Show/hide empty state message
    const noCommentsMessage = document.getElementById('noCommentsMessage');
    const commentsContainer = document.getElementById('commentsContainer');
    
    if (noCommentsMessage && commentsContainer) {
        if (count === 0) {
            noCommentsMessage.classList.remove('hidden');
            commentsContainer.classList.add('hidden');
        } else {
            noCommentsMessage.classList.add('hidden');
            commentsContainer.classList.remove('hidden');
        }
    }
}

function updateFileCount(count) {
    // Update file count in header
    const fileListCountElement = document.getElementById('fileListCount');
    if (fileListCountElement) {
        fileListCountElement.textContent = count;
    }
    
    // Update file count in tab (optional)
    const fileCountElement = document.getElementById('fileCount');
    if (fileCountElement) {
        fileCountElement.textContent = count;
    }
    
    // Show/hide empty state message
    const noFilesMessage = document.getElementById('noFilesMessage');
    if (noFilesMessage) {
        if (count > 0) {
            noFilesMessage.classList.add('hidden');
        } else {
            noFilesMessage.classList.remove('hidden');
        }
    }
}

// Attach event delegation on DOMContentLoaded
if (typeof window !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        const commentsContainer = document.getElementById('commentsContainer');
        if (commentsContainer) {
            commentsContainer.addEventListener('click', handleCommentAction);
        }
        const fileList = document.getElementById('fileList');
        if (fileList) {
            fileList.addEventListener('click', handleFileAction);
        }
    });
}

// Add handler for clicking outside the drawer to close it
document.addEventListener('mousedown', function(event) {
    const drawer = document.getElementById('taskDetailsDrawer');
    
    // Only handle clicks when drawer is open
    if (!drawer || drawer.classList.contains('translate-x-full')) {
        return;
    }
    
    // Check if the click is outside the drawer
    if (!drawer.contains(event.target)) {
        // Make sure we're not clicking on something that should open the drawer
        const clickedOnTaskRow = event.target.closest('tr[data-task-id]');
        if (!clickedOnTaskRow) {
            closeTaskDetailsDrawer();
        }
    }
});

// Optional: Add a semi-transparent overlay when drawer is open
function toggleDrawerOverlay(show) {
    let overlay = document.getElementById('drawerOverlay');
    
    // Create overlay if it doesn't exist
    if (!overlay && show) {
        overlay = document.createElement('div');
        overlay.id = 'drawerOverlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-200';
        overlay.style.opacity = '0';
        document.body.appendChild(overlay);
        
        // Fade in
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 15);
        
        // Add click handler to close drawer
        overlay.addEventListener('click', closeTaskDetailsDrawer);
    } 
    else if (overlay && !show) {
        // Fade out
        overlay.style.opacity = '0';
        
        // Remove after transition
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 15);
    }
}

// Enhance the existing open/close methods
const originalOpen = openTaskDetails;
window.openTaskDetails = function(row) {
    originalOpen(row);
    toggleDrawerOverlay(true);
};

const originalClose = closeTaskDetailsDrawer;
window.closeTaskDetailsDrawer = function() {
    originalClose();
    toggleDrawerOverlay(false);
};

window.openTaskDetails = openTaskDetails;
window.closeTaskDetailsDrawer = closeTaskDetailsDrawer;
window.loadComments = loadComments;
window.loadFiles = loadFiles;
window.editComment = editComment;
window.cancelEdit = cancelEdit;
window.replyToComment = replyToComment;
window.showReplyForm = showReplyForm;
window.cancelReply = cancelReply;
window.submitReply = submitReply;
window.toggleReplies = toggleReplies;
window.downloadFile = downloadFile;
window.saveEdit = saveEdit;
window.deleteComment = deleteComment;
window.handleCommentKeydown = handleCommentKeydown;
window.addComment = addComment;
window.handleFileSelect = handleFileSelect;