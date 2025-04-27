<?php
session_start();
include('connection.php');

$connect = new Connect();
$connection = $connect->getConnection();

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit();
}

$user_id = $_SESSION['userID'];
$userName = $_SESSION['userName'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idea Submission UI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            /* overflow-y: hidden; */
        }

        .sidebar {
            background-color: #7E5CAD;
            color: white;
            height: 95vh;
            padding: 50px;
            border-radius: 25px;
            text-align: center;
        }

        .sidebar .btn {
            width: 100%;
            height: 60px;
            margin-bottom: 10px;
            text-align: left;
            border-radius: 10px;
            text-align: center;
        }

        .topbar {
            background-color: #D1C4E9;
            padding: 15px;
            border-radius: 10px;
        }

        .card-category {
            background-color: #6A4C93;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .card-subcategory {
            background-color: #6FCF97;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .idea-card {
            border: 1px solid #38363A !important;
            border-radius: 25px;
        }

        .idea-box {
            max-height: 75vh;
            overflow-y: auto;
        }

        .custom-box {
            background-color: #c3a6d8;
            /* Light purple */
            padding: 10px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            min-height: 150px;
        }

        .avatar {
            width: 60px;
            height: 60px;
            background-color: #3b3b3b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        .input-box {
            flex-grow: 1;
            background-color: white;
            border-radius: 24px;
            padding: 12px;
            height: 70px;
            display: flex;
            align-items: center;

        }

        .icon {
            margin-left: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        .selected {
            background-color: #79D7BE;
            /* Bootstrap primary */

        }

        .comment-entry {
            display: flex;
            padding: 20px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .comment-entry .profile-icon {
            font-size: 36px;
            margin-right: 16px;
        }

        .comment-entry .dept-name {
            font-weight: bold;
            color: #2e7d65;
        }

        .comment-entry .date {
            white-space: nowrap;
            color: #6c757d;
            margin-left: auto;
        }

        .announce-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .announce-card-content {
            max-width: 80%;
        }

        .announce-card-title {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .announce-card-text {
            color: #333;
            line-height: 1.5;
        }

        .announce-badge {
            background-color: #3b2a5a;
            color: #fff;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            height: fit-content;
        }

        #showAnnouncements {
            padding: 10px 20px;
            background-color: #3b2a5a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .active-nav-btn {
            background-color: #79D7BE;
        }

        .active-nav-btn:hover {
            background-color: #79D7BE;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Soft shadow */
            transform: translateY(-2px);
            /* Subtle lift */
        }

        .side-btn:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Soft shadow */
            transform: translateY(-2px);
            /* Subtle lift */
        }

        .idea-upload-box {
            cursor: pointer;
        }
    </style>


</head>

<body>
    <input type="hidden" id="user-id" value="<?php echo htmlspecialchars($user_id); ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar d-flex flex-column m-3">
                <h2>Logo<br>AppName</h2>
                <button class="btn btn-light side-btn active-nav-btn" onclick="sendRequest('','')">Idea Feed</button>
                <button class="btn btn-light side-btn" onclick="sendRequest('contributions', '')">Contributions</button>
                <button class="btn btn-light side-btn" onclick="sendRequest('', 'desc')">Most Liked</button>
                <button class="btn btn-light side-btn" onclick="loadAnnouncements()">Announcements</button>
                <div class="mt-auto text-center">
                    <p><strong><?php echo htmlspecialchars($userName); ?></strong><br>Department</p>
                </div>
            </div>

            <div class="col-md-9 p-4">

                <div class="custom-box">
                    <div class="avatar">👤</div>
                    <div class="input-box py-2 idea-upload-box" onclick="window.location.href='upload_idea_page.php'">Which idea would you like to submit</div>
                    <div class="icon">🔔</div>
                </div>

                <div class="mt-4 idea-box" id="idea-container">

                </div>
            </div>
        </div>
    </div>
    <!-- COMMENT MODAL -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="fw-bold">Comments</h5>
                        <div class="text-muted">{finalClosureDate}</div>
                    </div>

                    <!-- Comment List -->
                    <div id="comments-list" style="max-height: 500px; overflow-y: auto;" class="mb-3"></div>

                    <!-- Comment Box -->
                    <form id="modal-comment-form" class="mt-4">
                        <div class="d-flex gap-2 align-items-center">
                            <textarea name="comment" class="form-control p-3 rounded-3 border border-dark-subtle" rows="3" placeholder="Leave your thoughts here" required></textarea>
                            <button type="submit" class="btn btn-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                ➡️
                            </button>
                        </div>

                        <div class="form-check form-switch mt-2">
                            <!-- Hidden default value -->
                            <input type="hidden" name="anonymous" value="0">
                            <!-- Checkbox override -->
                            <input class="form-check-input" type="checkbox" role="switch" id="anonymousSwitch" name="anonymous" value="1">

                            <label class="form-check-label" for="anonymousSwitch">Post anonymously</label>
                        </div>

                        <input type="hidden" name="idea_id" id="modal_idea_id">

                    </form>



                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const buttons = document.querySelectorAll('.side-btn');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(btn => btn.classList.remove('active-nav-btn'));
                button.classList.add('active-nav-btn');
            });
        });

        function sendRequest(userID = '', sortMostLike = '') {
            let formData = new URLSearchParams();
            formData.append('user_id', userID);
            formData.append('most_like', sortMostLike);

            fetch('idea_feed.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Ideas from server:", data);
                    displayIdeas(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function displayIdeas(ideas) {
            const container = document.getElementById('idea-container');
            container.innerHTML = ''; // Clear existing content

            ideas.forEach(idea => {
                const ideaElement = document.createElement('div');
                ideaElement.classList.add('card', 'p-3', 'mb-3', 'idea-card');

                ideaElement.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="bi bi-person-circle fs-3 me-2"></span>
                        <div>
                            <h6 class="department-name m-0">${idea.department_name}</h6>
                            <small class="date">${idea.created_at}</small>
                        </div>
                    </div>
                    <div>
                        <span class="card-category me-2">${idea.MainCategoryTitle}</span>
                        <span class="card-subcategory">${idea.SubCategoryTitle}</span>
                    </div>
                </div>
                <p class="mt-2">${idea.description}</p>
                <div class="mt-3 d-flex interaction-buttons">
                    <button class="btn btn-outline-dark me-2" id="like-${idea.idea_id}" onclick="handleVote(${idea.idea_id},1)">${idea.most_like} 👍</button>
                    <button class="btn btn-outline-dark me-2" id="unlike-${idea.idea_id}" onclick="handleVote(${idea.idea_id},2)">${idea.unlike} 👎</button>
                    <button class="btn btn-outline-dark me-2" id="comment-${idea.idea_id}" onclick="showCommentModel(${idea.idea_id})"> 💬</button>
                </div>
            `;

                container.appendChild(ideaElement);
            });
        }
        sendRequest();

        function handleVote(ideaId, voteType) {
            $.ajax({
                url: 'handleVote.php',
                type: 'POST',
                data: {
                    idea_id: ideaId,
                    votetype: voteType
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        const likeBtn = $(`#like-${ideaId}`);
                        const unlikeBtn = $(`#unlike-${ideaId}`);

                        likeBtn.html(`${data.like_count} 👍`);
                        unlikeBtn.html(`${data.unlike_count} 👎`);

                        if (data.current_vote === null) {
                            // Unvoted: remove all selections
                            likeBtn.removeClass('selected');
                            unlikeBtn.removeClass('selected');
                        } else if (data.current_vote === '1') {
                            likeBtn.addClass('selected');
                            unlikeBtn.removeClass('selected');
                        } else if (data.current_vote === '2') {
                            unlikeBtn.addClass('selected');
                            likeBtn.removeClass('selected');
                        }
                    } else {
                        alert(data.message);
                    }
                }
            });
        }

        function showCommentModel(ideaId) {
            $('#modal_idea_id').val(ideaId);

            $.ajax({
                url: 'handle_comments.php',
                type: 'POST',
                data: {
                    action: 'load',
                    idea_id: ideaId
                },
                success: function(response) {
                    $('#comments-list').html(response);
                    const modal = new bootstrap.Modal(document.getElementById('commentModal'));
                    modal.show();
                }
            });
        }

        $('#modal-comment-form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'handle_comments.php',
                type: 'POST',
                data: $(this).serialize() + '&action=submit',
                success: function(response) {
                    $('#comments-list').prepend(response);
                    $('#modal-comment-form')[0].reset();
                }
            });
        });

        function loadAnnouncements() {
            const announcementHTML = `
                                    <h2>Important</h2>
                                    <div class="announce-card">
                                    <div class="announce-card-content">
                                        <div class="announce-card-title">Closure Date Announcement</div>
                                        <div class="announce-card-text">
                                            Closure Date Information, Closure Date Information, Closure Date Information, Closure Date Information, Closure Date Information
                                        </div>
                                    </div>
                                    <div class="announce-badge">Admin</div>
                                    </div>
                                    <div class="announce-card">
                                    <div class="announce-card-content">
                                        <div class="announce-card-title">Final Closure Date Announcement</div>
                                        <div class="announce-card-text">
                                            Closure Date Information, Closure Date Information, Closure Date Information, Closure Date Information, Closure Date Information
                                        </div>
                                    </div>
                                    <div class="announce-badge">Admin</div>
                                    </div>
                                `;

            const container = document.getElementById('idea-container');
            container.innerHTML = ''; // Clear existing content
            container.innerHTML = announcementHTML;
        }

        $(document).ready(function() {
            $(function() {

            });
        });
    </script>

</body>



</html>