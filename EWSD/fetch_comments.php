<?php
session_start();
$idea_id = $_GET['idea_id'] ?? null; // Now using GET correctly

if ($idea_id !== null) {
    // Dummy comments array
    $dummy_comments = [
        [
            ['department_name' => 'Marketing', 'comment' => 'This idea could boost engagement!', 'created_at' => '2025-03-07'],
            ['department_name' => 'IT', 'comment' => 'We need more technical details.', 'created_at' => '2025-03-06'],
            ['department_name' => 'HR', 'comment' => 'Great for employee well-being!', 'created_at' => '2025-03-05']
        ],
        [
            ['department_name' => 'Finance', 'comment' => 'What’s the estimated budget?', 'created_at' => '2025-03-04'],
            ['department_name' => 'Operations', 'comment' => 'Can we optimize this process further?', 'created_at' => '2025-03-03']
        ],
        [
            ['department_name' => 'IT', 'comment' => 'AI implementation can be tricky.', 'created_at' => '2025-03-02'],
            ['department_name' => 'HR', 'comment' => 'Would require additional training.', 'created_at' => '2025-03-01']
        ]
    ];

    // Select a random set of comments for the idea
    $comments = $dummy_comments[array_rand($dummy_comments)];

    foreach ($comments as $comment) { ?>
        <div class="comment">
            <strong><?php echo htmlspecialchars($comment['department_name']); ?></strong>
            <span><?php echo date('d.m.Y', strtotime($comment['created_at'])); ?></span>
            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
        </div>
    <?php }
} else {
    echo "<p>No comments yet.</p>";
}
?>
