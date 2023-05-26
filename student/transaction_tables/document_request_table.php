<?php include './functions.php';?>

<?php
    // Add pagination to the table
    $rowsPerPage = 5;

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $rowsPerPage;

    $sortColumn = isset($_GET['sort']) ? $_GET['sort'] : '';
    $sortDirection = isset($_GET['dir']) ? $_GET['dir'] : 'ASC';

    // Validate and sanitize the sort column
    $validColumns = ['request_id', 'office_name', 'request_description', 'scheduled_datetime', 'status_name', 'amount_to_pay'];
    if (!in_array($sortColumn, $validColumns)) {
        $sortColumn = 'scheduled_datetime'; // Set a default sort column
    }

    // Validate and sanitize the sort direction
    $sortDirection = strtoupper($sortDirection);
    if ($sortDirection !== 'ASC' && $sortDirection !== 'DESC') {
        $sortDirection = 'ASC'; // Set a default sort direction
    }

    $documentRequests = "SELECT request_id, office_name, request_description, scheduled_datetime, status_name, amount_to_pay
                        FROM doc_requests
                        INNER JOIN offices ON doc_requests.office_id = offices.office_id
                        INNER JOIN statuses ON doc_requests.status_id = statuses.status_id
                        WHERE user_id = ". $_SESSION['user_id'] ." AND request_description IS NOT NULL
                        ORDER BY $sortColumn $sortDirection
                        LIMIT $offset, $rowsPerPage";

    $result = mysqli_query($connection, $documentRequests);

    if (isset($_POST['delete_request'])) {
        $deleteRequestId = $_POST['request_id'];
    
        $deleteQuery = "DELETE FROM doc_requests WHERE request_id = $deleteRequestId";
        mysqli_query($connection, $deleteQuery);
    
        // Redirect or refresh the page to update the table after deletion
        header("Refresh:0");
        exit();
    }
?>
<table id="transactions-table" class="table table-hover table-bordered">
    <thead>
        <tr>
            <th class="text-center" scope="col">
                <a class="text-decoration-none text-dark" href="?sort=request_id&dir=<?php echo $sortColumn === 'request_id' && $sortDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">
                    Request Code
                    <?php if ($sortColumn === 'request_id') { ?>
                        <span class="sort-icon <?php echo $sortDirection === 'ASC' ? 'asc' : 'desc'; ?>"></span>
                    <?php } ?>
                </a>
            </th>
            <th class="text-center sortable-header" scope="col">
                <a class="text-decoration-none text-dark" href="?sort=office_name&dir=<?php echo $sortColumn === 'office_name' && $sortDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">
                    Office
                    <?php if ($sortColumn === 'office_name') { ?>
                        <span class="sort-icon <?php echo $sortDirection === 'ASC' ? 'asc' : 'desc'; ?>"></span>
                    <?php } ?>
                </a>
            </th>
            <th class="text-center sortable-header" scope="col">
                <a class="text-decoration-none text-dark" href="?sort=request_description&dir=<?php echo $sortColumn === 'request_description' && $sortDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">
                    Request
                    <?php if ($sortColumn === 'request_description') { ?>
                        <span class="sort-icon <?php echo $sortDirection === 'ASC' ? 'asc' : 'desc'; ?>"></span>
                    <?php } ?>
                </a>
            </th>
            <th class="text-center sortable-header" scope="col">
                <a class="text-decoration-none text-dark" href="?sort=scheduled_datetime&dir=<?php echo $sortColumn === 'scheduled_datetime' && $sortDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">
                    Schedule
                    <?php if ($sortColumn === 'scheduled_datetime') { ?>
                        <span class="sort-icon <?php echo $sortDirection === 'ASC' ? 'asc' : 'desc'; ?>"></span>
                    <?php } ?>
                </a>
            </th>
            <th class="text-center" scope="col">
                <a class="text-decoration-none text-dark" href="?sort=amount_to_pay&dir=<?php echo $sortColumn === 'amount_to_pay' && $sortDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">
                    Amount to pay
                    <?php if ($sortColumn === 'amount_to_pay') { ?>
                        <span class="sort-icon <?php echo $sortDirection === 'ASC' ? 'asc' : 'desc'; ?>"></span>
                    <?php } ?>
                </a>
            </th>
            <th class="text-center sortable-header" scope="col">
                <a class="text-decoration-none text-dark" href="?sort=status_name&dir=<?php echo $sortColumn === 'status_name' && $sortDirection === 'ASC' ? 'DESC' : 'ASC'; ?>">
                    Status
                    <?php if ($sortColumn === 'status_name') { ?>
                        <span class="sort-icon <?php echo $sortDirection === 'ASC' ? 'asc' : 'desc'; ?>"></span>
                    <?php } ?>
                </a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $requestId = $row['request_id'];
                    $requestDescription = $row['request_description'];
                    $scheduledDateTime = $row['scheduled_datetime'];
                    $officeName = $row['office_name'];
                    $statusName = $row['status_name'];
                    $amountToPay = $row['amount_to_pay'];
        ?>
        <tr>
            <td><?php echo "DR-"; echo $requestId; ?></td>
            <td><?php echo $officeName; ?></td>
            <td><?php echo $requestDescription; ?></td>
            <td>
                <?php
                if ($scheduledDateTime === NULL) {
                    echo "Not yet scheduled";
                } else {
                    echo (new DateTime($scheduledDateTime))->format("m/d/Y g:i A");
                }
                ?>
            </td>
            <td><?php echo "₱"; echo $amountToPay; ?></td>
            <td class="text-center">
                <span class="badge rounded-pill <?php echo getStatusBadgeClass($statusName); ?>">
                    <?php echo $statusName; ?>
                </span>
            </td>
            <td>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this appointment?')">
                    <a href="<?php echo getSchedulePageRedirect($requestDescription); ?>" class="btn btn-primary btn-sm"><i class="fa-brands fa-wpforms"></i></a>
                    <input type="hidden" name="request_id" value="<?php echo $requestId; ?>">
                    <?php
                    if ($statusName == "Pending" || $statusName == "Disapproved") {
                        echo '<button type="submit" name="delete_request" class="btn btn-primary btn-sm"><i class="fa-solid fa-trash-can"></i></button>';
                    }
                    else {
                        echo '<button type="button" class="btn btn-sm" disabled><i class="fa-solid fa-trash-can"></i></button>';
                    }
                    ?>
                </form>
            </td>
        </tr>
        <?php
                    }
                }
                else {
        ?>
        <tr>
            <td class="text-center table-light p-4" colspan="6">No Transactions</td>
        </tr>
        <?php
            }
        }
        else {
            echo "Error executing the query: " . mysqli_error($connection);
        }

        $countTotalOnDocumentRequests = "SELECT COUNT(*) AS total FROM doc_requests WHERE user_id = 1";
        $countResult = mysqli_query($connection, $countTotalOnDocumentRequests);
        $countRow = mysqli_fetch_assoc($countResult);
        $totalRows = $countRow['total'];

        $totalPages = ceil($totalRows / $rowsPerPage);
        ?>
    </tbody>
</table>