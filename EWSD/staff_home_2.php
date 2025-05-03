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
$userProfileImg = $_SESSION['userProfile'] ?? 'default-profile.jpg';
$isDisabled = false;

if ($user_id) {
    $query = "SELECT account_status FROM users WHERE user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user['account_status'] !== 'active') {
        $isDisabled = true;
    }
} else {
    // User not logged in
    $isDisabled = true;
}

$disablepost = false;
$closureQuery = "SELECT * FROM request_ideas ORDER BY requestIdea_id DESC LIMIT 1";
$closureResult = $connection->query($closureQuery);
if ($closureResult && $row = $closureResult->fetch_assoc()) {
    $closure_date = $row['closure_date'];
    $today = date('Y-m-d');

    // Compare dates
    if ($closure_date < $today) {
        $disablepost = true;
    }
}

$shouldDisable = $isDisabled || $disablepost;

$disableComment = false;
$closureCommentQuery = "SELECT * FROM request_ideas ORDER BY requestIdea_id DESC LIMIT 1";
$closureCommentResult = $connection->query($closureCommentQuery);
if ($closureCommentResult && $row = $closureCommentResult->fetch_assoc()) {
    $final_closure_date = $row['final_closure_date'];
    $today = date('Y-m-d');

    // Compare dates
    if ($final_closure_date < $today) {
        $disableComment = true;
    }
}

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
            max-height: 67vh;
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

        .avatar img {
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

        .custom-box.disabled-box {
            background-color: #e0d6ec;
            /* Pale soft purple (disabled look) */
            opacity: 0.6;
            pointer-events: none;
            cursor: not-allowed;
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

        .announce-card-title,
        .department-name {
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

        .staff-btn-purple {
            background-color: #6A4C93;
            color: white;
        }

        .staff-btn-purple:hover {
            background-color: rgb(120, 91, 160);
            color: white;
        }
        .react-btn:hover{
            background-color: grey !important;
        }
        .logout {
            margin-top: auto;
            color: #fff;
            border-radius: 10px;
            background-color: #3c9a72;
            padding: 15px 0px;
            transition: all 0.3s;
            width: 100%;
            text-align: center;
            border-radius: 10px;
            text-decoration: none;
        }
    </style>


</head>

<body>
    <input type="hidden" id="user-id" value="<?php echo htmlspecialchars($user_id); ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar d-flex flex-column m-3">
                <span class="mb-5" style="background-color: white;"><img src="Images/logo.png" alt="logo" width="150px" style="margin: 8px 0px;"></span>
                <button class="btn btn-light side-btn active-nav-btn" onclick="sendRequest('','')">Idea Feed</button>
                <button class="btn btn-light side-btn" onclick="sendRequest('contributions', '')">Contributions</button>
                <button class="btn btn-light side-btn" onclick="sendRequest('', 'desc')">Most Liked</button>
                <button class="btn btn-light side-btn" onclick="loadAnnouncements()">Announcements</button>
                <div class="mt-auto text-center">
                    <p><strong><?php echo htmlspecialchars($userName); ?></strong><br>Department</p>
                    <a class=" logout" type="button" href="logout.php" onclick="return confirm('Do You Want To Log Out?')">Log Out</a>
                </div>
            </div>

            <div class="col-md-9 p-4">
                <!-- check the user account status whether allow to post idea -->
                <div class="custom-box <?php if ($shouldDisable) echo 'disabled-box'; ?>">
                    <div class="avatar">
                        <img src="<?php echo htmlspecialchars($userProfileImg); ?>"
                            alt="Profile Image">
                    </div>
                    <div
                        class="input-box py-2 idea-upload-box"
                        <?php if (!$shouldDisable): ?>
                        onclick="window.location.href='upload_idea_page.php'"
                        <?php endif; ?>>
                        <?php if ($isDisabled) {
                            echo 'Sorry, your account has been disabled. You cannot post ideas.';
                        } elseif ($disablepost) {
                            echo 'Idea submission is currently closed.';
                        } else {
                            echo 'Which idea would you like to submit?';
                        } ?>
                    </div>
                    <div class="icon">🔔</div>
                </div>


                <div class="mt-4 idea-box" id="idea-container">

                </div>
                <div id="pagination-controls" class="mt-3 d-flex justify-content-center"></div>
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
                        <div class="text-muted">Final Closure Date: <?php echo htmlspecialchars($final_closure_date); ?></div>
                    </div>

                    <!-- Comment List -->
                    <div id="comments-list" style="max-height: 500px; overflow-y: auto;" class="mb-3"></div>

                    <!-- Comment Box -->

                    <form id="modal-comment-form" class="mt-4" <?php if ($disableComment) echo 'onsubmit="return false;"'; ?>>
                        <div class="d-flex gap-2 align-items-center">
                            <textarea name="comment" class="form-control p-3 rounded-3 border border-dark-subtle" rows="3" placeholder="Leave your thoughts here" <?php if ($disableComment) echo 'disabled'; ?> required></textarea>
                            <button type="submit" class="btn rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;" <?php if ($disableComment) echo 'disabled'; ?>>
                                <span><img style="width: 20px;height:20px; vertical-align:text-bottom" src="Images/send.png"/></span>
                            </button>
                        </div>

                        <div class="form-check form-switch mt-2">
                            <!-- Hidden default value -->
                            <input type="hidden" name="anonymous" value="0">
                            <!-- Checkbox override -->
                            <input class="form-check-input" type="checkbox" role="switch" id="anonymousSwitch" name="anonymous" value="1" <?php if ($disableComment) echo 'disabled'; ?>>

                            <label class="form-check-label" for="anonymousSwitch">Post anonymously</label>
                        </div>

                        <input type="hidden" name="idea_id" id="modal_idea_id">

                    </form>
                    <?php if ($disableComment): ?>
                        <div class="alert alert-warning mt-2">
                            Commenting is currently disabled. Final Closure Date has passed.
                        </div>
                        <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- show if user is disabled to comment idea -->
    <div class="modal fade" id="disabledActionModal" tabindex="-1" aria-labelledby="disabledActionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="disabledActionLabel">Action not allowed</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Your account is disabled. You can't comment on ideas.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn staff-btn-purple" data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
            };

            const datePart = date.toISOString().split('T')[0]; // YYYY-MM-DD
            let hours = date.getHours();
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? ' PM' : ' AM';
            hours = hours % 12 || 12; // Convert 0 to 12 for 12-hour format

            return `${datePart} ${hours}:${minutes}${ampm}`;
        }
        const buttons = document.querySelectorAll('.side-btn');
        const isUserDisabled = <?php echo json_encode($isDisabled); ?>;

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(btn => btn.classList.remove('active-nav-btn'));
                button.classList.add('active-nav-btn');
            });
        });

        let currentPage = 1;
        const pageSize = 5; // Change this value as needed
        let allIdeas = []; // Store all ideas globally

        function paginateIdeas(ideas, page, pageSize) {
            const start = (page - 1) * pageSize;
            return ideas.slice(start, start + pageSize);
        }

        function displayPaginationControls(totalPages) {
            const paginationContainer = document.getElementById('pagination-controls');
            paginationContainer.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.innerText = '<';
            prevBtn.disabled = currentPage === 1;
            prevBtn.className = 'btn me-2';
            prevBtn.onclick = () => {
                currentPage--;
                displayIdeas(allIdeas);
            };

            const nextBtn = document.createElement('button');
            nextBtn.innerText = '>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.className = 'btn ms-2';
            nextBtn.onclick = () => {
                currentPage++;
                displayIdeas(allIdeas);
            };

            const pageInfo = document.createElement('span');
            pageInfo.className = 'align-self-center mx-3';
            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

            paginationContainer.appendChild(prevBtn);
            paginationContainer.appendChild(pageInfo);
            paginationContainer.appendChild(nextBtn);
        }


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
                    allIdeas = data;
                    currentPage = 1;
                    displayIdeas(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function displayIdeas(ideas) {
            const container = document.getElementById('idea-container');
            container.innerHTML = ''; // Clear existing content

            const totalPages = Math.ceil(ideas.length / pageSize);
            const paginatedIdeas = paginateIdeas(ideas, currentPage, pageSize);

            paginatedIdeas.forEach(idea => {
                const ideaElement = document.createElement('div');
                ideaElement.classList.add('card', 'p-3', 'mb-3', 'idea-card');

                ideaElement.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                
                    <div class="d-flex align-items-center">
                    <div class="avatar">
                    <img src="${idea.anonymousSubmission == 1? "Images/Default-avatar.png":idea.user_profile}"
                        alt="Profile Image">
                </div>
                        <span class="bi bi-person-circle fs-3 me-2"></span>
                        <div>
                            <h6 class="department-name m-0">${idea.anonymousSubmission == 1? "Announymous":idea.department_name}</h6>
                             <span>${idea.anonymousSubmission == 1? "": idea.user_name +' . '} </span><small class="date" style="color:grey">${formatDate(idea.created_at)}</small>
                        </div>
                    </div>
                    <div>
                        <span class="card-category me-2">${idea.MainCategoryTitle}</span>
                        <span class="card-subcategory">${idea.SubCategoryTitle}</span>
                    </div>
                </div>
                <div class="attachmentDiv p-3">
                    <img style="min-width:10%; max-width:50%; height:auto" src="${idea.img_path ? idea.img_path : ''}" />
                </div>
                <p class="mt-2">${idea.description}</p>
                <div class="mt-3 d-flex interaction-buttons">
                    <button class="btn btn-outline-dark react-btn me-2" id="like-${idea.idea_id}" onclick="handleVote(${idea.idea_id},1)"><b>${idea.most_like}</b> <span><img style="width:20px;height:20px; vertical-align: top;" src="Images/Like.png"/></span></button>
                    <button class="btn btn-outline-dark react-btn me-2" id="unlike-${idea.idea_id}" onclick="handleVote(${idea.idea_id},2)"><b>${idea.unlike}</b> <span><img style="width:20px;height:20px; vertical-align: bottom;" src="Images/Unlike.png"/></span></button>
                    <button class="btn btn-outline-dark react-btn me-2" id="comment-${idea.idea_id}" onclick="handleCommentClick(${idea.idea_id})"> <span><img style="width:20px;height:20px; vertical-align: middle;" src="Images/Comment.png"/></span></button>
                </div>
            `;

                container.appendChild(ideaElement);
            });
            displayPaginationControls(totalPages);
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

        function handleCommentClick(ideaId) {
            if (isUserDisabled) {
                const disabledModal = new bootstrap.Modal(document.getElementById('disabledActionModal'));
                disabledModal.show();
            } else {
                showCommentModel(ideaId);
            }
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
            fetch('announcement.php')
                .then(response => response.json())
                .then(data => {
                    // Display the latest request idea

                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    const container = document.getElementById('idea-container');
                    container.innerHTML = ''; // Clear existing content
                    // container.innerHTML = announcementHTML;
                    const paginationContainer = document.getElementById('pagination-controls');
                    paginationContainer.innerHTML = '';

                    if (data.request_idea) {
                        const announcementHTML = `
                                    <h2>Important</h2>
                                    <div class="announce-card">
                                        <div class="announce-card-content">
                                            <div class="announce-card-title">Closure Date Announcement</div>
                                            <div class="announce-card-text">
                                            <div>${data.request_idea.closure_date}</div>
                                                 ${data.request_idea.description || 'No description provided.'}
                                            </div>
                                        </div>
                                        <div class="announce-badge">Admin</div>
                                    </div>
                                    <div class="announce-card">
                                        <div class="announce-card-content">
                                            <div class="announce-card-title">Final Closure Date Announcement</div>
                                            <div class="announce-card-text">
                                            <div>${data.request_idea.final_closure_date}</div>
                                                 ${data.request_idea.description || 'No description provided.'}
                                            </div>
                                        </div>
                                        <div class="announce-badge">Admin</div>
                                    </div>
                                    <div class="announce-card">
                                        <div class="announce-card-content">
                                            <div class="announce-card-title">Terms and Condition</div>
                                            <div class="announce-card-text">
                                                Closure Date Information, Closure Date Information, Closure Date Information, Closure Date Information, Closure Date Information
                                            </div>
                                        </div>
                                        <div class="announce-badge">Admin</div>
                                    </div>
                                    <h2>Others</h2>`;

                        container.innerHTML = announcementHTML;

                    }

                    if (data.announcements) {
                        data.announcements.forEach(item => {
                            const card = document.createElement('div');
                            card.classList.add('announce-card');
                            card.innerHTML = `
                                            <div class="announce-card-content">
                                                <div class="announce-card-title">${item.announce_title}</div>
                                                <div class="announce-card-text">
                                                    ${item.description}
                                                </div>
                                            </div>
                                            <div class="announce-badge">${item.department_name}</div>
                                        `;
                            container.appendChild(card);
                        });

                    }


                    // Display all announcements
                    // const container = document.getElementById('announcement-container');
                    //container.innerHTML = '';

                })
                .catch(error => console.error('Error fetching data:', error));
        }




        $(document).ready(function() {
            $(function() {

            });
        });
    </script>

</body>



</html>