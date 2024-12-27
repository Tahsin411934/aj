<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>AJAX দিয়ে ডাটা ইনসার্ট, আপডেট এবং ডিলিট</h2>

        <!-- Add Post Modal Trigger -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">পোস্ট যোগ করুন</button>

        <!-- Modal for Adding or Updating Posts -->
        <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="postModalLabel">পোস্ট যোগ করুন</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="postForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">শিরোনাম</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="শিরোনাম লিখুন" required>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">বিস্তারিত</label>
                                <textarea class="form-control" id="content" name="content" rows="3" placeholder="বিস্তারিত লিখুন" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                            <button type="submit" class="btn btn-primary">সাবমিট</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Table to Show All Posts -->
        <div class="mt-5">
            <h3>পোস্টের তালিকা</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>শিরোনাম</th>
                        <th>বিস্তারিত</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody id="postsList">
                    <!-- Posts will be listed dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        $(document).ready(function () {
            const _token = $('meta[name="csrf-token"]').attr('content');

            // Fetch posts and display in table
            function fetchPosts() {
                $.ajax({
                    url: "{{ route('posts.index') }}",
                    method: 'GET',
                    success: function (response) {
                        let postsHtml = '';
                        $.each(response.posts, function (index, post) {
                            postsHtml += `
                                <tr>
                                    <td>${post.title}</td>
                                    <td>${post.content}</td>
                                    <td>
                                        <button class="btn btn-warning editBtn" data-id="${post.id}" data-title="${post.title}" data-content="${post.content}">এডিট</button>
                                        <button class="btn btn-danger deleteBtn" data-id="${post.id}">ডিলিট</button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#postsList').html(postsHtml);
                    }
                });
            }

            // Generic AJAX request for creating and updating posts
            function ajaxRequest(method, url, data, successMessage, errorMessage) {
                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    success: function () {
                        showToast(successMessage, "linear-gradient(to right, #00b09b, #96c93d)");
                        resetForm();
                        fetchPosts();
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = `<div class="alert alert-danger">`;
                        $.each(errors, function (key, value) {
                            errorHtml += `<p>${value[0]}</p>`;
                        });
                        errorHtml += '</div>';
                        $('#message').html(errorHtml);
                    }
                });
            }

            // Show toast notification
            function showToast(message, bgColor) {
                Toastify({
                    text: message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: bgColor,
                }).showToast();
            }

            // Reset form and modal state
            function resetForm() {
                $('#postForm')[0].reset();
                $('#postForm').removeData('id');
                $('button[type="submit"]').text('সাবমিট');
                $('#postModal').modal('hide');
            }

            // Handle form submission for creating and updating posts
            $('#postForm').on('submit', function (e) {
                e.preventDefault();
                let id = $('#postForm').data('id');
                let title = $('#title').val();
                let content = $('#content').val();

                let url = id ? `{{ url('posts') }}/${id}` : "{{ route('posts.store') }}";
                let method = id ? 'PUT' : 'POST';
                let successMessage = id ? "পোস্ট সফলভাবে আপডেট করা হয়েছে!" : "পোস্ট সফলভাবে যোগ করা হয়েছে!";

                ajaxRequest(method, url, {
                    _token: _token,
                    title: title,
                    content: content
                }, successMessage);
            });

            // Edit post
            $(document).on('click', '.editBtn', function () {
                let id = $(this).data('id');
                let title = $(this).data('title');
                let content = $(this).data('content');

                $('#title').val(title);
                $('#content').val(content);
                $('#postForm').data('id', id);
                $('button[type="submit"]').text('আপডেট');
                $('#postModal').modal('show');
            });

            // Delete post
            $(document).on('click', '.deleteBtn', function () {
                let id = $(this).data('id');
                if (confirm('আপনি কি নিশ্চিত যে আপনি এই পোস্টটি মুছে ফেলতে চান?')) {
                    ajaxRequest('DELETE', `{{ url('posts') }}/${id}`, { _token: _token }, "পোস্ট সফলভাবে ডিলিট হয়েছে!", "");
                }
            });

            // Initial fetch of posts
            fetchPosts();
        });
    </script>
</body>
</html>
