<?php
// functions.php

function get_comments($idea_id, $connection)
{
    // Query to get comments for the given idea_id
    $query = "SELECT 
                ic.ideacommentID,ic.ideacommentText,ic.idea_id,ic.created_at,u.user_name,ri.final_closure_date,d.department_name
                from idea_comment as ic
                inner join ideas as i on ic.idea_id = i.idea_id
                inner join users as u on ic.user_id = u.user_id
                inner join request_ideas as ri on i.requestIdea_id = ri.requestIdea_id
                inner join departments as d on u.department_id = d.department_id
                WHERE ic.idea_id = ?";

    // Prepare and execute the statement
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $idea_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch comments as an array
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    $stmt->close();

    // Return the comments as an array
    return $comments;
}
