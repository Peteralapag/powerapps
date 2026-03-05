<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$controlno = $_POST['controlno'];
$branch = $_POST['branch'];

?>

<table class="table table-striped table-sm">
    <thead>
        <tr>
            <th>#</th>
            <th>ITEMCODE</th>
            <th>ITEM DESCRIPTION</th>
            <th>REQUESTED QTY.</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $db->prepare("SELECT * FROM dbc_seasonal_branch_order WHERE control_no = ? AND branch = ?");
        $stmt->bind_param("ss", $controlno, $branch);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $counter = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$counter}</td>
                    <td>{$row['item_code']}</td>
                    <td>{$row['item_description']}</td>
                    <td style='text-align: right'>{$row['quantity']}</td>
                </tr>";
                $counter++;
            }
        } else {
            echo "<tr>
                <td colspan='4' class='text-center'>No records found</td>
            </tr>";
        }
        $stmt->close();
        $db->close();
        ?>
    </tbody>
</table>
